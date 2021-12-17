<?php

namespace App\Collections;

use App\DTOs\ChannelConfigurationDto;
use App\Models\ChannelConfiguration;
use App\Models\Timezone;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Carbon\CarbonPeriod;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

/**
 * Our `ChannelConfigurationCollection` is better thought of as a catch-all property for the Channel, which includes
 * all of the Channel's settings.
 *
 * Class ChannelConfigurationCollection
 * @package App\Collections
 */
class ChannelConfigurationCollection extends BaseCollection
{
    /**
     * Whether the channel has our catch-all Letterhead Membership services enabled.
     */
    private const HAS_MEMBERSHIP_ENABLED = 'hasMembershipEnabled';

    private $disabledDates;

    public function __construct(Collection $channelConfigurationDatabaseResults)
    {
        /**
         * @note Looping through the array twice on construction isn't very performant.
         */
        $dtos = $this->getDtos($channelConfigurationDatabaseResults);
        $channelConfigurations = $this->getModels($dtos);

        $this->disabledDates = $this->getDisabledDates();
        parent::__construct($channelConfigurations);
    }

    /**
     * We tend to use `Y-m-d` date formats across the board, and we often have to
     * convert Carbon dates to that. This small helper means we only have to
     * write this method once.
     *
     * @param array $arrayOfCarbonDates
     * @return array
     */
    private function formatCarbonDatesAsYmd(array $arrayOfCarbonDates): array
    {
        return array_map(function (Carbon $date) {
            return $date->format('Y-m-d');
        }, $arrayOfCarbonDates);
    }

    public function getAdSchedulingBuffer(): int
    {
        return $this->getConfigurationValueBySlug('adSchedulingBuffer', 72);
    }

    public function getAutoUpdateChannelStatsFromMailchimp(): bool
    {
        /**
         * @var int|bool
         */
        $autoUpdate = $this->getConfigurationValueBySlug('autoUpdateChannelStatsFromMailchimp', false);

        return (bool) $autoUpdate;
    }

    public function getAverageDailyReads(): int
    {
        return $this->getConfigurationValueBySlug('averageDailyReads', 0);
    }

    public function getChannelContactAddress__city(): string
    {
        return $this->getConfigurationValueBySlug('channelContactAddress__city', '');
    }

    public function getChannelContactAddress__postal(): string
    {
        return $this->getConfigurationValueBySlug('channelContactAddress__postal', '');
    }

    public function getChannelContactAddress__state(): string
    {
        return $this->getConfigurationValueBySlug('channelContactAddress__state', '');
    }

    public function getChannelContactAddress__street(): string
    {
        return $this->getConfigurationValueBySlug('channelContactAddress__street', '');
    }

    public function getChannelContactEmail(): string
    {
        return $this->getConfigurationValueBySlug('channelContactEmail', '');
    }

    public function getChannelContactName(): string
    {
        return $this->getConfigurationValueBySlug('channelContactName', '');
    }

    public function getChannelNewsletterUrl(): string
    {
        return $this->getConfigurationValueBySlug('channelNewsletterUrl', '');
    }

    public function getChannelContactPhone(): string
    {
        return $this->getConfigurationValueBySlug('channelContactPhone', '');
    }

    public function getChannelStorefrontImageContentScreenshot(): string
    {
        return $this->getConfigurationValueBySlug('channelStorefrontImageContentScreenshot', '');
    }

    public function getChannelStorefrontImageHero(): string
    {
        return $this->getConfigurationValueBySlug('channelStorefrontImageHero', '');
    }

    public function getChannelUrl(): string
    {
        return $this->getConfigurationValueBySlug('channelUrl', '');
    }

    public function getClickthroughRate(): float
    {
        return $this->getConfigurationValueBySlug('clickthroughRate', 0.0);
    }

    public function getDefaultFromEmailAddress(): string
    {
        return $this->getConfigurationValueBySlug('defaultFromEmailAddress', '');
    }

    public function getDefaultEmailFromName(): string
    {
        return $this->getConfigurationValueBySlug('defaultEmailFromName', '');
    }

    public function getConfigurationBySlug(string $slug): ?ChannelConfiguration
    {
        /**
         * @var ChannelConfiguration|null
         */
        $configuration = $this->first(function (ChannelConfiguration $channelConfiguration) use ($slug) {
            return $channelConfiguration->getConfigurationSlug() === $slug;
        });

        return empty($configuration) ? null : $configuration;
    }

    /**
     * Given the `slug` of a configuration, we'll check to see if there is a value set and
     * retrieve either that or return a default.
     *
     * @param string $slug
     * @param $defaultValue
     * @return mixed
     */
    private function getConfigurationValueBySlug(string $slug, $defaultValue)
    {
        /**
         * @var ChannelConfiguration|null
         */
        $configuration = $this->getConfigurationBySlug($slug);

        return empty($configuration) ? $defaultValue : $configuration->getChannelConfigurationValue();
    }

    /**
     * Returns an array of date strings formatted Y-m-d (like ['2020-02-03', '2020-03-05']), which
     * represent dates we want to disable in various publishing calendars.
     *
     * @return array
     */
    public function getDisabledDates(): array
    {
        $datesWithNoScheduledContent = $this->formatCarbonDatesAsYmd($this->getDatesChannelContentIsNotScheduled());

        $holidays = $this->getPublicationHolidays();
        $schedulingBuffer = $this->getAdSchedulingBuffer();

        $periodOfCarbonDatesToDisable = CarbonPeriod::create(
            CarbonImmutable::now()->toDateString(),
            CarbonImmutable::now()->addHours($schedulingBuffer)
        )->toArray();

        $datesWithinSchedulingBuffer = $this->formatCarbonDatesAsYmd($periodOfCarbonDatesToDisable);

        $uniqueDisabledDates = array_unique(array_merge($holidays, $datesWithinSchedulingBuffer, $datesWithNoScheduledContent), SORT_REGULAR);
        return array_values($uniqueDisabledDates);
    }

    /**
     * Return an array of Carbon dates between now and the end of the year that.
     * @return CarbonPeriod
     */
    private function getDatesBetweenNowAndEndOfYear(): CarbonPeriod
    {
        $now = CarbonImmutable::now()->toDateString();
        $endOfThisYear = CarbonImmutable::createFromDate(null, 12, 31);

        return CarbonPeriod::create($now, $endOfThisYear);
    }

    /**
     * Return an array of Carbon dates of every day between now and the end of the year
     * where content won't be published.
     *
     * @return array Array of carbon dates
     */
    private function getDatesChannelContentIsNotScheduled(): array
    {
        $arrayOfWeekDayIndices = [0, 1, 2, 3, 4, 5, 6];
        $indicesOfWeekDaysWithNoContent = array_diff($arrayOfWeekDayIndices, $this->getPublicationScheduleDaily());
        $datesWithNoContentBetweenNowAndEndOfYear = array_filter(
            $this->getDatesBetweenNowAndEndOfYear()->toArray(),
            function (Carbon $date) use ($indicesOfWeekDaysWithNoContent) {
                return in_array($date->dayOfWeek, $indicesOfWeekDaysWithNoContent);
            }
        );

        return $datesWithNoContentBetweenNowAndEndOfYear;
    }

    private function getDtos(Collection $channelConfigurationDatabaseResults): array
    {
        return array_map(function ($channelConfigurationObject) {
            return new ChannelConfigurationDto($channelConfigurationObject);
        }, $channelConfigurationDatabaseResults->toArray());
    }

    public function getHasMailChimpIntegrationEnabled(): bool
    {
        /**
         * @var int|bool
         */
        $mcIntegrationValue = $this->getConfigurationValueBySlug('mcIntegration', false);

        return (bool) $mcIntegrationValue;
    }

    public function getHasConstantContactIntegrationEnabled(): bool
    {
        /**
         * @var int|bool
         */
        $mcIntegrationValue = $this->getConfigurationBySlug('ccIntegration', false);

        return (bool) $mcIntegrationValue;
    }
    /**
     * A simple boolean lookup to confirm whether or not the channel
     * has an API key in its configuration.
     *
     * @return bool
     */
    public function getHasMailChimpApiKey(): bool
    {
        return !empty($this->getMcApiKey());
    }

    /**
     * @return bool Whether the channel has membership enabled.
     */
    public function getHasMembershipEnabled(): bool
    {
        $hasMembershipEnabled = $this->getConfigurationValueBySlug(self::HAS_MEMBERSHIP_ENABLED, false);
        return (bool) $hasMembershipEnabled;
    }

    private function getModels(array $channelConfigurationDtos): array
    {
        return array_map(function (ChannelConfigurationDto $dto) {
            return new ChannelConfiguration($dto);
        }, $channelConfigurationDtos);
    }

    public function getOpenRate(): float
    {
        return $this->getConfigurationValueBySlug('openRate', 0.0);
    }

    public function getPublicArray(): array
    {
        return array_map(function (ChannelConfiguration $channelConfiguration) {
            return $channelConfiguration->convertToArray();
        }, $this->items);
    }

    public function getPublicationHolidays(): array
    {
        return $this->getConfigurationValueBySlug('publicationHolidays', []);
    }

    public function getPublicationScheduleDaily(): array
    {
        return $this->getConfigurationValueBySlug('publicationScheduleDaily', []);
    }

    public function getStripeAccount(): string
    {
        return $this->getConfigurationValueBySlug('stripeAccount', '');
    }

    /**
     * This will get the Channel's selected MailChimp List Id. In the event that
     * the API key is blank, the list ID is technically useless, so we'll
     * return an empty string instead.
     *
     * @return string
     */
    public function getMcSelectedEmailListId(): string
    {
        $mailChimpApiKey = $this->getMcApiKey();

        if (empty($mailChimpApiKey)) {
            return '';
        }

        return $this->getConfigurationValueBySlug('mcSelectedEmailListId', '');
    }

    public function getMcApiKey(): string
    {
        return $this->getConfigurationValueBySlug('mcApiKey', '');
    }

    public function getPromotionPolicyUrl(): string
    {
        return $this->getConfigurationValueBySlug('promotionPolicyUrl', '');
    }

    public function getTimezone(): Timezone
    {
        $defaultTimezone = new \stdClass();
        $defaultTimezone->offset = '-05:00';
        $defaultTimezone->label = '(GMT-05:00) Eastern Time';
        $defaultTimezone->tzCode = 'America/New_York';

        $timezoneObject = $this->getConfigurationValueBySlug('timezone', $defaultTimezone);

        return new Timezone($timezoneObject);
    }

    public function getTotalSubscribers(): int
    {
        return $this->getConfigurationValueBySlug('totalSubscribers', 0);
    }
}
