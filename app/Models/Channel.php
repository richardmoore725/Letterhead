<?php

namespace App\Models;

use App\Collections\ChannelConfigurationCollection;
use App\DTOs\ChannelDto;
use App\Http\Services\MailChimpFacade;
use Illuminate\Support\Facades\Storage;

class Channel
{
    private $accentColor;
    private $brandId;

    /**
     * @var ChannelConfigurationCollection
     */
    private $channelConfigurations;
    private $channelHorizontalLogo;
    private $channelSlug;
    private $channelSquareLogo;
    private $channelDescription;
    private $channelImage;
    private $defaultEmailFromName;
    private $defaultEsp = 0;
    private $defaultFromEmailAddress;
    private $defaultFont;
    private $deletedAt;
    private $enableChannelAuthoring;
    private $hasValidMailChimpKey;
    private $headingFont;
    private $loadPromosBeforeHeadings;
    private $id;
    private $title;
    private $timeSinceMailChimpStatusPinged;
    private $ccAccessToken = '';
    private $ccRefreshToken = '';
    private $ccAccessTokenLastUsed;
    private $updatedAt;
    private $createdAt;

    public function __construct(ChannelDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->accentColor = $dto->accentColor;
        $this->brandId = $dto->brandId;
        $this->channelConfigurations = $dto->channelConfigurations;
        $this->channelHorizontalLogo = $dto->channelHorizontalLogo;
        $this->channelSlug = $dto->channelSlug;
        $this->channelDescription = $dto->channelDescription;
        $this->channelImage = $dto->channelImage;
        $this->channelSquareLogo = $dto->channelSquareLogo;
        $this->defaultEmailFromName = $dto->defaultEmailFromName;
        $this->defaultEsp = $dto->defaultEsp;
        $this->defaultFromEmailAddress = $dto->defaultFromEmailAddress;
        $this->defaultFont = $dto->defaultFont;
        $this->deletedAt = $dto->deletedAt;
        $this->enableChannelAuthoring = $dto->enableChannelAuthoring;
        $this->hasValidMailChimpKey = $dto->hasValidMailChimpKey;
        $this->headingFont = $dto->headingFont;
        $this->id = $dto->id;
        $this->loadPromosBeforeHeadings = $dto->loadPromosBeforeHeadings;
        $this->title = $dto->title;
        $this->timeSinceMailChimpStatusPinged = $dto->timeSinceMailChimpStatusPinged;
        $this->ccAccessToken = $dto->ccAccessToken;
        $this->ccRefreshToken = $dto->ccRefreshToken;
        $this->ccAccessTokenLastUsed = $dto->ccAccessTokenLastUsed;
        $this->updatedAt = $dto->updatedAt;
        $this->createdAt = $dto->createdAt;
    }

    public function convertToArray(): array
    {
        return [
            'accentColor' => $this->getAccentColor(),
            'adSchedulingBuffer' => $this->getAdSchedulingBuffer(),
            'averageDailyReads' => $this->channelConfigurations->getAverageDailyReads(),
            'autoUpdateChannelStatsFromMailchimp' => $this->getAutoUpdateChannelStatsFromMailchimp(),
            'brandId' => $this->brandId,
            'channelConfigurations' => $this->channelConfigurations->getPublicArray(),
            'channelContactAddress__city' => $this->channelConfigurations->getChannelContactAddress__city(),
            'channelContactAddress__postal' => $this->channelConfigurations->getChannelContactAddress__postal(),
            'channelContactAddress__state' => $this->channelConfigurations->getChannelContactAddress__state(),
            'channelContactAddress__street' => $this->channelConfigurations->getChannelContactAddress__street(),
            'channelContactEmail' => $this->channelConfigurations->getChannelContactEmail(),
            'channelContactName' => $this->channelConfigurations->getChannelContactName(),
            'channelContactPhone' => $this->channelConfigurations->getChannelContactPhone(),
            'channelDescription' => $this->channelDescription,
            'channelHorizontalLogo' => $this->getChannelHorizontalLogo(),
            'channelImage' => $this->getChannelImage(),
            'channelImageUrl' => $this->getChannelImage(),
            'channelStorefrontImageContentScreenshot' => $this->channelConfigurations->getChannelStorefrontImageContentScreenshot(),
            'channelImageStorefrontHero' => $this->channelConfigurations->getChannelStorefrontImageHero(),
            'channelSquareLogo' => $this->getChannelSquareLogo(),
            'channelSlug' => $this->channelSlug,
            'channelNewsletterUrl' => $this->channelConfigurations->getChannelNewsletterUrl(),
            'channelUrl' => $this->channelConfigurations->getChannelUrl(),
            'clickthroughRate' => $this->channelConfigurations->getClickthroughRate(),
            'defaultEmailFromName' => $this->getDefaultEmailFromName(),
            'defaultFromEmailAddress' => $this->getDefaultFromEmailAddress(),
            'defaultFont' => $this->getDefaultFont(),
            'disabledDates' => $this->channelConfigurations->getDisabledDates(),
            'enableChannelAuthoring' => $this->getEnableChannelAuthoring(),
            'hasMailChimpApiKey' => $this->getHasMailChimpApiKey(),
            'hasMailChimpIntegrationEnabled' => $this->getHasMailChimpIntegrationEnabled(),
            'hasConstantContactAccessToken' => $this->getHasConstantContactAccessToken(),
            'hasConstantContactIntegrationEnabled' => $this->getHasConstantContactIntegrationEnabled(),
            'hasMembershipEnabled' => $this->getHasMembershipEnabled(),
            'hasValidMailChimpKey' => $this->getHasValidMailChimpKey(),
            'headingFont' => $this->getHeadingFont(),
            'id' => $this->id,
            'loadPromosBeforeHeadings' => $this->getLoadPromosBeforeHeadings(),
            'openRate' => $this->channelConfigurations->getOpenRate(),
            'promotionPolicyUrl' => $this->getPromotionPolicyUrl(),
            'publicationHolidays' => $this->channelConfigurations->getPublicationHolidays(),
            'publicationScheduleDaily' => $this->channelConfigurations->getPublicationScheduleDaily(),
            'stripeAccount' => $this->channelConfigurations->getStripeAccount(),
            'mcSelectedEmailListId' => $this->channelConfigurations->getMcSelectedEmailListId(),
            'totalSubscribers' => $this->channelConfigurations->getTotalSubscribers(),
            'title' => $this->title,
            'timezone' => $this->channelConfigurations->getTimezone()->convertToArray(),
            'timeSinceMailChimpStatusPinged' => $this->getTimeSinceMailChimpStatusPinged(),
            'updatedAt' => $this->updatedAt,
            'createdAt' => $this->createdAt
        ];
    }

    public function convertToDto(): ChannelDto
    {
        $dto = new ChannelDto();
        $dto->accentColor = $this->accentColor;
        $dto->brandId = $this->brandId;
        $dto->channelConfigurations = $this->channelConfigurations;
        $dto->channelSquareLogo = $this->channelSquareLogo;
        $dto->channelHorizontalLogo = $this->channelHorizontalLogo;
        $dto->channelSlug = $this->channelSlug;
        $dto->channelDescription = $this->channelDescription;
        $dto->channelImage = $this->channelImage;
        $dto->defaultEmailFromName = $this->defaultEmailFromName;
        $dto->defaultEsp = $this->getDefaultEsp();
        $dto->defaultFromEmailAddress = $this->defaultFromEmailAddress;
        $dto->defaultFont = $this->defaultFont;
        $dto->deletedAt = $this->deletedAt;
        $dto->enableChannelAuthoring = $this->enableChannelAuthoring;
        $dto->hasValidMailChimpKey = $this->hasValidMailChimpKey;
        $dto->headingFont = $this->headingFont;
        $dto->id = $this->id;
        $dto->loadPromosBeforeHeadings = $this->loadPromosBeforeHeadings;
        $dto->title = $this->title;
        $dto->timeSinceMailChimpStatusPinged = $this->timeSinceMailChimpStatusPinged;
        $dto->ccAccessToken = $this->ccAccessToken;
        $dto->ccRefreshToken = $this->ccRefreshToken;
        $dto->ccAccessTokenLastUsed = $this->ccAccessTokenLastUsed;
        $dto->updatedAt = $this->updatedAt;
        $dto->createdAt = $this->createdAt;

        return $dto;
    }

    public function getAccentColor(): string
    {
        return $this->accentColor;
    }

    /**
     * Returns the number of hours from publication where promotions
     * can't be scheduled.
     *
     * @return int
     */
    public function getAdSchedulingBuffer(): int
    {
        return $this->getChannelConfigurations()->getAdSchedulingBuffer();
    }

    public function getAutoUpdateChannelStatsFromMailchimp(): bool
    {
        return $this->getChannelConfigurations()->getAutoUpdateChannelStatsFromMailchimp();
    }

    public function getBrandId(): int
    {
        return $this->brandId;
    }

    /**
     * This is a small helper method we can pass a value to and return a string. The string will either
     * be empty if the $propertyValue is empty, or it will return a url. That url will either be
     * the value itself because the value _is_ a url, or it will generate one.
     *
     * @param $propertyValue
     * @return string
     */
    private function getChannelAssetUrlFromPropertyValue($propertyValue): string
    {
        if (empty($propertyValue)) {
            return '';
        }

        return filter_var($propertyValue, FILTER_VALIDATE_URL)
            ? $propertyValue
            : Storage::url($propertyValue);
    }

    public function getChannelHorizontalLogo(): string
    {
        return $this->getChannelAssetUrlFromPropertyValue($this->channelHorizontalLogo);
    }

    public function getChannelSquareLogo(): string
    {
        return $this->getChannelAssetUrlFromPropertyValue($this->channelSquareLogo);
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getChannelImage(): string
    {
        return $this->getChannelAssetUrlFromPropertyValue($this->channelImage);
    }

    public function getChannelConfigurations(): ChannelConfigurationCollection
    {
        return $this->channelConfigurations;
    }

    public function getDefaultEmailFromName(): string
    {
        if (empty($this->defaultEmailFromName)) {
            return $this->channelConfigurations->getDefaultEmailFromName();
        }

        return $this->defaultEmailFromName;
    }

    public function getDefaultEsp(): int
    {
        return $this->defaultEsp;
    }

    public function getDefaultFromEmailAddress(): string
    {
        if (empty($this->defaultFromEmailAddress)) {
            return $this->channelConfigurations->getDefaultFromEmailAddress();
        }

        return $this->defaultFromEmailAddress;
    }

    public function getDefaultFont(): string
    {
        return $this->defaultFont;
    }

    public function getDefaultListId(): string
    {
        return $this->channelConfigurations->getMcSelectedEmailListId();
    }

    public function getEnableChannelAuthoring(): bool
    {
        return $this->enableChannelAuthoring;
    }

    public function getHasMailChimpApiKey(): bool
    {
        return $this->getChannelConfigurations()->getHasMailChimpApiKey();
    }

    public function getHasConstantContactAccessToken(): bool
    {
        return $this->getCCAccessToken() != '';
    }

    public function getHasMailChimpIntegrationEnabled(): bool
    {
        return $this->getChannelConfigurations()->getHasMailChimpIntegrationEnabled();
    }

    public function getHasConstantContactIntegrationEnabled(): bool
    {
        return $this->getChannelConfigurations()->getHasConstantContactIntegrationEnabled();
    }

    public function getHasMembershipEnabled(): bool
    {
        return $this->getChannelConfigurations()->getHasMembershipEnabled();
    }

    public function getHasValidMailChimpKey(): bool
    {
        return $this->hasValidMailChimpKey;
    }

    public function getHeadingFont(): string
    {
        return $this->headingFont;
    }

    public function getLoadPromosBeforeHeadings(): bool
    {
        return $this->loadPromosBeforeHeadings;
    }

    /**
     * Return an instance of the Channel's MailChimp instance if it is valid.
     * @return MailChimpFacade|null
     */
    public function getMailChimp(): ?MailChimpFacade
    {
        return MailChimpFacade::createFromChannel($this);
    }

    public function getPromotionPolicyUrl(): string
    {
        return $this->getChannelConfigurations()->getPromotionPolicyUrl();
    }

    public function getSlug(): string
    {
        return $this->channelSlug;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getTimezone(): Timezone
    {
        return $this->channelConfigurations->getTimezone();
    }

    public function getTimezoneOffset(): string
    {
        return $this->getTimezone()->getOffset();
    }

    public function getTimeSinceMailChimpStatusPinged(): string
    {
        return $this->timeSinceMailChimpStatusPinged;
    }

    public function setCCAccessToken($access_token): void
    {
        $this->ccAccessToken = $access_token;
    }

    public function getCCAccessToken(): string
    {
        return $this->ccAccessToken;
    }

    public function setCCRefreshToken($refresh_token): void
    {
        $this->ccRefreshToken = $refresh_token;
    }

    public function getCCRefreshToken(): string
    {
        return $this->ccRefreshToken;
    }

    public function setCCAccessTokenLastUsed(): void
    {
        $this->ccAccessTokenLastUsed = date('Y-m-d H:i:s');
    }

    public function getCCAccessTokenLastUsed()
    {
        return $this->ccAccessTokenLastUsed;
    }

    public function isAccessTokenExpired(): bool
    {
        if (!$this->getCCAccessTokenLastUsed()) {
            return false;
        }

        /**
         * Access Token for Constant Contact expires 2 hours after their last use.
         */
        return (strtotime(date('Y-m-d H:i:s')) - strtotime($this->getCCAccessTokenLastUsed())) > 7200;
    }

    public function clearCCTokens(): void
    {
        $this->ccAccessToken = '';
        $this->ccRefreshToken = '';
        $this->ccAccessTokenLastUsed = null;
    }
    /**
     * Returns an array of Validation rules, which can be used to pass to a
     * Validator.
     *
     * @return array
     * @see https://lumen.laravel.com/docs/5.8/validation
     * @see https://laravel.com/docs/5.8/validation#available-validation-rules
     */
    public static function getValidationRules(): array
    {
        return [
            'brandId' => 'nullable|integer',
            'channelSlug' => 'required|string',
            'channelDescription' => 'nullable|string',
            'title' => 'required|string',
            'enableChannelAuthoring' => 'nullable',
            'accentColor' => 'nullable|string',
            'defaultEmailFromName' => 'nullable|string',
            'defaultFromEmailAddress' => 'nullable|string',
            'defaultFont' => 'nullable|string',
            'headingFont' => 'nullable|string'
        ];
    }

    public function setAccentColor(string $accentColor): void
    {
        $this->accentColor = $accentColor;
    }

    /**
     * @param int $brandId
     */
    public function setBrandId(int $brandId): void
    {
        $this->brandId = $brandId;
    }

    /**
     * @param string $channelSlug
     */
    public function setChannelSlug(string $channelSlug): void
    {
        $this->channelSlug = $channelSlug;
    }

    public function setChannelHorizontalLogo(string $channelHorizontalLogo): void
    {
        $this->channelHorizontalLogo = $channelHorizontalLogo;
    }


    public function setChannelSquareLogo(string $channelSquareLogo): void
    {
        $this->channelSquareLogo = $channelSquareLogo;
    }

    /**
     * @param string $channelDescription
     */
    public function setChannelDescription(string $channelDescription): void
    {
        $this->channelDescription = $channelDescription;
    }

    /**
     * @param string $channelImage
     */
    public function setChannelImage(string $channelImage): void
    {
        $this->channelImage = $channelImage;
    }

    public function setDefaultFont(string $defaultFont): void
    {
        $this->defaultFont = $defaultFont;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setHasValidMailChimpKey(bool $hasValidMailChimpKey): void
    {
        $this->hasValidMailChimpKey = $hasValidMailChimpKey;
    }

    public function setLoadPromosBeforeHeadings(bool $loadPromosBeforeHeadings): void
    {
        $this->loadPromosBeforeHeadings = $loadPromosBeforeHeadings;
    }

    public function setTimeSinceMailChimpStatusPinged(string $timeSinceMailChimpStatusPinged): void
    {
        $this->timeSinceMailChimpStatusPinged = $timeSinceMailChimpStatusPinged;
    }

    public function setEnableChannelAuthoring(bool $enableChannelAuthoring): void
    {
        $this->enableChannelAuthoring = $enableChannelAuthoring;
    }

    public function setDefaultEmailFromName(string $defaultEmailFromName): void
    {
        $this->defaultEmailFromName = $defaultEmailFromName;
    }

    public function setDefaultEsp(int $defaultEsp): void
    {
        $this->defaultEsp = $defaultEsp;
    }

    public function setDefaultFromEmailAddress(string $defaultFromEmailAddress): void
    {
        $this->defaultFromEmailAddress = $defaultFromEmailAddress;
    }

    public function setHeadingFont(string $headingFont): void
    {
        $this->headingFont = $headingFont;
    }
}
