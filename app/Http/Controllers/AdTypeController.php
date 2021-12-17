<?php

namespace App\Http\Controllers;

use App\Http\Response;
use App\Http\Services\AdTypeServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\PassportStamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdTypeController extends Controller
{
    /**
     * @var AdTypeServiceInterface
     */
    private $adTypeService;

    /**
     * @var string A simple slug that identifies which 2nd-party service we connect with.
     */
    private $beacon;

    /**
     * @var BeaconServiceInterface
     */
    private $beaconService;

    public function __construct(AdTypeServiceInterface $adTypeService, BeaconServiceInterface $beaconService)
    {
        $this->adTypeService = $adTypeService;

        /**
         * AdTypes are part of the larger AdService.
         */
        $this->beacon = 'ads';
        $this->beaconService = $beaconService;
    }

    /**
     * We use `createAdType` to do just that. If something goes wrong, then the $adType will
     * return nothing and we'll return a 500. Otherwise, we'll transform the $adType into
     * json.
     *
     * @param Channel $channel
     * @param Request $request
     * @return JsonResponse
     */
    public function createAdType(Channel $channel, Request $request): JsonResponse
    {
        $adTypeFormData = $this->adTypeService->getAdTypeRequestFormattedForMultipartPost($request);
        $serviceEndpoint = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads/types";

        $adType = $this->beaconService->createResourceByBeaconSlug(
            $this->beacon,
            $channel->getBrandId(),
            $channel->getId(),
            $serviceEndpoint,
            $adTypeFormData,
            true
        );

        if (empty($adType)) {
            return response()->json('Shoot. We could not create this ad type.', 500);
        }


        return response()->json($adType, 201, [], JSON_UNESCAPED_SLASHES);
    }

    public function deleteAdType(int $adTypeId): JsonResponse
    {
        $serviceEndpoint = "ad-types/{$adTypeId}";

        $wasTypeDeleted = $this->beaconService->deleteResourceFromService($this->beacon, $serviceEndpoint);

        if (empty($wasTypeDeleted)) {
            return response()->json($wasTypeDeleted, 500);
        }

        return response()->json($wasTypeDeleted, 200);
    }

    /**
     * @param int $adTypeId
     * @return JsonResponse
     */
    public function getAdTypeById(int $adTypeId): JsonResponse
    {
        $serviceEndpoint = "ad-types/{$adTypeId}";

        $adType = $this->beaconService->getResourceByBeaconSlug($this->beacon, 0, 0, $serviceEndpoint);

        if (empty($adType)) {
            return response()->json('Bummer. No ad type with this ID exists.', 404);
        }

        return response()->json($adType);
    }

    public function getAdTypesByChannel(Channel $channel): JsonResponse
    {
        $beaconSlug = 'ads';
        $restfulResourcePath = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads/types";

        $adTypes = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $channel->getBrandId(), $channel->getId(), $restfulResourcePath);

        return response()->json($adTypes);
    }

    public function getAvailableDatesByAdType(Channel $channel, int $adTypeId): JsonResponse
    {
        $brandId = $channel->getBrandId();
        $channelConfig = $channel->getChannelConfigurations();
        $channelId = $channel->getId();
        $disabledDates = $channelConfig->getDisabledDates();
        $scheduleBuffer = $channelConfig->getAdSchedulingBuffer();

        $response = $this->adTypeService->getAvailableDatesByAdType($adTypeId, $brandId, $channelId, $disabledDates, $scheduleBuffer);

        if ($response->isError()) {
            return response()->json("Darn, we were unable to retrieve the available dates for ad type ${adTypeId} right now. We will work on fixing this ASAP. If you need more immediate help in the meantime, ping us in chat and we will help you with your promotion.", 500);
        }

        $availableDates = $response->getData();

        return response()->json($availableDates, 200);
    }

    public function getAdTypesWithPricesByChannel(Channel $channel): JsonResponse
    {
        $brandId = $channel->getBrandId();
        $channelId = $channel->getId();
        $channelConfigs = $channel->getChannelConfigurations();
        $listSize = $channelConfigs->getTotalSubscribers();

        $response = $this->adTypeService->getAdTypesWithPricesByChannel($brandId, $channelId, $listSize);

        $promoTypes = $response->getData();

        if (empty($promoTypes)) {
            return response()->json('We couldn\'t retrieve the priced promotion types', 404);
        }

        return response()->json($promoTypes, 200);
    }

    public function getDisabledDatesByAdType(Channel $channel, int $adTypeId): JsonResponse
    {
        $beaconSlug = "ads";
        $serviceEndpoint = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads/booked-dates-by-type/{$adTypeId}";

        $disabledDates = $this->beaconService->getResourceByBeaconSlug(
            $beaconSlug,
            $channel->getBrandId(),
            $channel->getId(),
            $serviceEndpoint
        );

        return response() -> json($disabledDates);
    }

    /**
     * Use this controller to have our PromotionService scaffold the default promotion types
     * offered to a new channel. It will respond with a 201 if the promotion types are successfully
     * created, or a 400 if a type happens to exist already.
     *
     * @param Channel $channel
     * @return JsonResponse
     */
    public function scaffoldDefaultPromotionTypesForNewChannel(Channel $channel): JsonResponse
    {
        $didWeScaffoldSuccessfully = $this->adTypeService->scaffoldDefaultPromotionTypesForNewChannel($channel->getBrandId(), $channel->getId());
        $responseStatus = $didWeScaffoldSuccessfully ? 201 : 400;

        return response()->json($didWeScaffoldSuccessfully, $responseStatus);
    }

    /**
     * In a similar vein we'll use `updateAdType` to format and publish updates to the
     * AdType to AdService. If something goes wrong and the $adType returns empty, we'll
     * return the user a 500 error. Otherwise, a we'll return the updated object as json.
     *
     * @param Channel $channel
     * @param int $adTypeId
     * @param Request $request
     * @return JsonResponse
     */
    public function updateAdType(Channel $channel, int $adTypeId, Request $request): JsonResponse
    {
        $adTypeFormData = $this->adTypeService->getAdTypeRequestFormattedForMultipartPost($request);
        $serviceEndpoint = "ad-types/{$adTypeId}";

        $adType = $this->beaconService->createResourceByBeaconSlug(
            $this->beacon,
            $channel->getBrandId(),
            $channel->getId(),
            $serviceEndpoint,
            $adTypeFormData,
            true
        );

        if (empty($adType)) {
            return response()->json('Shoot. We could not update this ad type.', 500);
        }


        return response()->json($adType, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function updatePromotionTypeTemplate(
        Channel $channel,
        int $adTypeId,
        PassportStamp $passport,
        Request $request,
        UserServiceInterface $userService
    ): JsonResponse {
        $isAuthorized = $userService->checkWhetherUserCanPerformAction('create', 'brand', $passport, $channel->getBrandId());

        if (!$isAuthorized) {
            return response()->json('You don\'t seem to have the right permissions to update this template', 403);
        }

        $mjml = $request->input('mjmlTemplate');

        if (empty($mjml)) {
            return response()->json('You need to write some MJML, first!', 400);
        }

        $response = $this->adTypeService->updatePromotionTypeTemplate($channel->getBrandId(), $channel->getId(), $adTypeId, $mjml);
        return response()->json($response->getData(), $response->getStatus(), [], JSON_UNESCAPED_SLASHES);
    }
}
