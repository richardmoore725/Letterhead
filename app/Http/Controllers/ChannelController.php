<?php

namespace App\Http\Controllers;

use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\MailChimpFacade;
use App\Http\Services\MailChimpFacadeInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\Configuration;
use App\Collections\ChannelConfigurationCollection;
use App\Jobs\SyncMailChimpListData;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Contracts\Queue\Queue;
use DrewM\MailChimp\MailChimp;

/**
 * Class ChannelController
 * @group Channels
 * @package App\Http\Controllers
 */
class ChannelController extends Controller
{
    private $brandService;
    private $channelService;
    private $queue;

    public function __construct(
        BrandServiceInterface $brandService,
        ChannelServiceInterface $channelService,
        Queue $queue
    ) {
        $this->brandService = $brandService;
        $this->channelService = $channelService;
        $this->queue = $queue;
    }

    /**
     * @param Brand $brand
     * @param $channelHorizontalLogo string|UploadedFile
     * @param string $channelDescription
     * @param $channelImage string|UploadedFile
     * @param string $channelSlug
     * @param $channelSquareLogo string|UploadedFile
     * @param string $title
     * @return JsonResponse
     */
    public function createBrandChannel(
        string $accentColor,
        Brand $brand,
        $channelHorizontalLogo,
        string $channelDescription,
        $channelImage,
        string $channelSlug,
        $channelSquareLogo,
        string $defaultEmailFromName,
        string $defaultFromEmailAddress,
        string $defaultFont,
        bool $enableChannelAuthoring,
        string $headingFont,
        bool $loadPromosBeforeHeadings,
        string $title
    ) {
        /**
         * @todo Abstract to channelService
         */
        $currentTime = time();

        $channelSpacesPath = "platformservice/brands/{$brand->getId()}/channels/{$channelSlug}";
        $channelImagePath = empty($channelImage) ? '' : $channelImage->storePubliclyAs("{$channelSpacesPath}", "{$channelSlug}-{$currentTime}-image.{$channelImage->extension()}", 'spaces');
        $channelHorizontalLogoPath = empty($channelHorizontalLogo) ? '' : $channelHorizontalLogo->storePubliclyAs("{$channelSpacesPath}", "{$channelSlug}-{$currentTime}-horizontal-logo.{$channelHorizontalLogo->extension()}", 'spaces');
        $channelSquareLogo = empty($channelSquareLogo) ? '' : $channelSquareLogo->storePubliclyAs("{$channelSpacesPath}", "{$channelSlug}-{$currentTime}-square-logo.{$channelSquareLogo->extension()}", 'spaces');

        $newlyCreatedChannel = $this->channelService->createChannel(
            $accentColor,
            $brand->getId(),
            $channelDescription,
            $channelHorizontalLogoPath,
            $channelImagePath,
            $channelSlug,
            $channelSquareLogo,
            $defaultEmailFromName,
            $defaultFromEmailAddress,
            $defaultFont,
            $enableChannelAuthoring,
            $headingFont,
            $loadPromosBeforeHeadings,
            $title
        );

        if (empty($newlyCreatedChannel)) {
            return response()->json('We were not able to create this channel', 500);
        }

        return response()->json($newlyCreatedChannel->convertToArray());
    }

    public function deleteChannel(Channel $channel): JsonResponse
    {
        $wasChannelDeleted = $this->channelService->deleteChannel($channel);

        return response()->json($wasChannelDeleted);
    }

    /**
     * @param Request $request
     * @param Channel $channel
     *
     * @response 200 {
     * {
     *  "brandId": 141,
     *  "channelConfigurations": [],
     *  "channelSlug": "wee",
     *  "id": 17,
     *  "title": "Black Bitter Coffee Times"
     * }
     *
     * @response 404
     *
     * @return JsonResponse
     *
     * @urlParam slug required The alphanumeric hyphenated slug of the channel
     */
    public function getChannel(Channel $channel): JsonResponse
    {
        return response()->json($channel->convertToArray());
    }

    /**
     * Here, we can update a channel's specific configuration - presuming it exists in the
     * Configurations table. If it's an image we'll make sure that image gets uploaded, and if it's
     * suppose to be an array we'll serialize it.
     *
     * @param Channel $channel
     * @param Configuration $configuration
     * @param Request $request
     * @return JsonResponse
     */
    public function updateChannelConfiguration(
        Channel $channel,
        Configuration $configuration,
        Request $request
    ): JsonResponse {
        if ($request->hasFile('channelConfigurationValue')) {
            $channelConfigurationValue = $request->file('channelConfigurationValue');

            $channelSpacesPath = "platformservice/brands/{$channel->getId()}/channels/{$channel->getSlug()}";

            $configurationValuePath = empty($channelConfigurationValue) ? '' : $channelConfigurationValue->storePubliclyAs($channelSpacesPath, "{$configuration->getConfigurationSlug()}-image.{$channelConfigurationValue->extension()}", 'spaces');
            $configurationValueImageUrl = Storage::url($configurationValuePath);
            $updatedChannelConfiguration = $this->brandService->updateChannelConfiguration($channel->getId(), $configurationValueImageUrl, $configuration->getId());
            return response()->json($updatedChannelConfiguration);
        }

        /**
         * This is a bananas ternary.
         */
        $channelConfigurationValue = $configuration->getDataType() === 'array'
            ? serialize($request->input('channelConfigurationValue', []))
            : ($configuration->getDataType() === 'boolean'
                ? 'true' == $request->input('channelConfigurationValue', false)
                    ? 1
                    : 0
                : ($configuration->getDataType() === 'object'
                    ? json_encode($request->input('channelConfigurationValue', null))
                    : $request->input('channelConfigurationValue')));

        $channelConfigurationValue = $channelConfigurationValue === 'null' ? null : $channelConfigurationValue;

        $updatedChannelConfiguration = $this->brandService->updateChannelConfiguration($channel->getId(), $channelConfigurationValue, $configuration->getId());

        return response()->json($updatedChannelConfiguration);
    }

    /**
     * Update the mcApiKey ChannelConfiguration and validate it.
     *
     * @param Channel $channel
     * @param Configuration $configuration
     * @param Request $request
     * @return JsonResponse
     */
    public function updateChannelConfigurationMailChimpApiKey(Channel $channel, Configuration $configuration, Request $request): JsonResponse
    {
        /* update mcApiKey in channelConfigurations */
        $mcApiKey = $request->input('channelConfigurationValue');
        $updatedChannelConfiguration = $this->brandService->updateChannelConfiguration($channel->getId(), $mcApiKey, $configuration->getId());

        if ($updatedChannelConfiguration) {
            /* update this channel with the updated mcApiKey */
            $updatedChannel = $this->channelService->updateChannel($channel);
            if (empty($updatedChannel)) {
                return response()->json("Failed to update channel with id #{$channel->getId()}", 500);
            }

            $mailChimp = MailChimpFacade::createFromChannel($updatedChannel);
            if (empty($mailChimp)) {
                $updatedChannel->setHasValidMailChimpKey(false);
                $updatedChannel->setTimeSinceMailChimpStatusPinged(CarbonImmutable::now()->toDateTimeString());
                $updatedChannelWithValidatingProperties = $this->channelService->updateChannel($updatedChannel);
                if (empty($updatedChannelWithValidatingProperties)) {
                    return response()->json("Failed to update channel with id #{$channel->getId()}", 500);
                }
                return response()->json("{$mcApiKey} is not a valid mc api key.", 400);
            }

            /** we ping to check whether it's a valid mv api key */
            $mcResponse = $mailChimp->ping();
            /** we set these 2 channel properties */
            $updatedChannel->setTimeSinceMailChimpStatusPinged(CarbonImmutable::now()->toDateTimeString());
            $isValidMcApiKey = $mcResponse->isSuccess();
            $updatedChannel->setHasValidMailChimpKey($isValidMcApiKey);

            /** we update this channel so aragorn can get these 2 updated properties:
             * 1. hasValidMailChimpKey
             * 2. timeSinceMailChimpStatusPinged
             */
            $updatedChannelWithValidatingProperties = $this->channelService->updateChannel($updatedChannel);
            if (empty($updatedChannelWithValidatingProperties)) {
                return response()->json("Failed to update channel with id #{$channel->getId()}", 500);
            }
        }

        return response()->json($updatedChannelConfiguration);
    }

    /**
     * Update the selectedMailChimpListId ChannelConfiguration, and toggle MailChimp Integration accordingly.
     *
     * @param Channel $channel
     * @param Configuration $configuration
     * @param Request $request
     * @return JsonResponse
     */
    public function updateChannelConfigurationMailChimpListId(Channel $channel, Configuration $configuration, Request $request): JsonResponse
    {
        $channelConfigurations = $channel->getChannelConfigurations();

        if (empty($channelConfigurations->getMcApiKey())) {
            return response()->json('A MailChimp API key should be set before trying to choose a list', 400);
        }

        $existingMailChimpListId = $channelConfigurations->getMcSelectedEmailListId();
        $newMailChimpListId = $request->input('channelConfigurationValue');

        $hasListChanged = $existingMailChimpListId !== $newMailChimpListId;

        if (!$hasListChanged) {
            return response()->json('This value didn\'t change.', 200);
        }

        if (!empty($newMailChimpListId)) {
            $syncMailChimpListDataJob = new SyncMailChimpListData($channel, $newMailChimpListId);
            $this->queue->pushOn('mcstats', $syncMailChimpListDataJob);
        }

        return $this->updateChannelConfiguration($channel, $configuration, $request);
    }

    /**
     * @param Channel $channel
     * @param string $channelDescription
     * @param $channelImage string|UploadedFile
     * @param $channelHorizontalLogo string|UploadedFile
     * @param string $channelSlug
     * @param $channelSquareLogo string|UploadedFile
     * @param string $title
     * @return JsonResponse
     */
    public function updateBrandChannel(
        string $accentColor,
        Channel $channel,
        string $channelDescription,
        $channelImage,
        $channelHorizontalLogo,
        string $channelSlug,
        $channelSquareLogo,
        string $defaultEmailFromName,
        string $defaultFromEmailAddress,
        string $defaultFont,
        bool $enableChannelAuthoring,
        string $headingFont,
        bool $loadPromosBeforeHeadings,
        string $title
    ): JsonResponse {
        $channel->setAccentColor($accentColor);
        $channel->setChannelSlug($channelSlug);
        $channel->setChannelDescription($channelDescription);

        $channelImagePath = $this->channelService->getChannelImagePath($channel, $channelImage);
        $channelHorizontalLogoPath = $this->channelService->getChannelImagePath($channel, $channelHorizontalLogo);
        $channelSquareLogoPath = $this->channelService->getChannelImagePath($channel, $channelSquareLogo);

        $channel->setChannelSquareLogo($channelSquareLogoPath);
        $channel->setChannelHorizontalLogo($channelHorizontalLogoPath);
        $channel->setChannelImage($channelImagePath);
        $channel->setDefaultEmailFromName($defaultEmailFromName);
        $channel->setDefaultFromEmailAddress($defaultFromEmailAddress);
        $channel->setDefaultFont($defaultFont);
        $channel->setEnableChannelAuthoring($enableChannelAuthoring);
        $channel->setHeadingFont($headingFont);
        $channel->setLoadPromosBeforeHeadings($loadPromosBeforeHeadings);
        $channel->setTitle($title);

        $updatedChannel = $this->channelService->updateChannel($channel);

        if (empty($updatedChannel)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedChannel->convertToArray(), 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function getChannels(): JsonResponse
    {
        $channels = $this->channelService->getChannels();

        return response()->json($channels->getPublicArray());
    }
}
