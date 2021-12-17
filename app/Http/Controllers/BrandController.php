<?php

namespace App\Http\Controllers;

use App\Collections\UserPermissionCollection;
use App\Http\Middleware\AuthorizeUserMiddleware;
use App\Http\Middleware\PassportMiddleware;
use App\Http\Services\AuthServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Brand;
use App\Models\BrandConfiguration;
use App\Models\PassportStamp;
use App\Jobs\ScaffoldPermissionsJob;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

/**
 * The `BrandController` is the core entrypoint for interfacing with Brands in the platform - their creation,
 * retrieval, and the like.
 *
 * Class BrandController
 * @package App\Http\Controllers
 * @group Brands
 */
class BrandController extends Controller
{
    private $authService;
    private $beaconService;
    private $brandService;

    /**
     * @var ChannelServiceInterface
     */
    private $channelService;

    public function __construct(
        AuthServiceInterface $authService,
        BeaconServiceInterface $beaconService,
        BrandServiceInterface $brandService,
        ChannelServiceInterface $channelService
    ) {
        $this->authService = $authService;
        $this->beaconService = $beaconService;
        $this->brandService = $brandService;
        $this->channelService = $channelService;
    }

    /**
     * @authenticated
     * @bodyParam brandConfigurations array An array of BrandConfiguration objects used to set default - um - configurations
     * @bodyParam brandName string The name of the brand. Example: WhereBy.Us
     * @bodyParam brandSlug string A _unqiue_ alphanmeric (no spaces) slug that serves in part as the url. Example: wherebyus
     * @bodyParam channels array An array of Channel objects used to setup at least one channel for the new brand.
     * @bodyParam features array An array of Feature objects, which determine what features the brand has.
     * @param Request $request
     * @response 201 {
     *   "brandName": "WhereBy.Us",
     *   "brandSlug": "wherebyus"
     * }
     * @return JsonResponse
     */
    public function createBrand(
        string $brandName,
        ?UploadedFile $brandHorizontalLogo,
        string $brandSlug,
        ?UploadedFile $brandSquareLogo
    ): JsonResponse {
        $brandSpacesPath = "platformservice/brands/{$brandSlug}";
        $brandHorizontalLogoPath = empty($brandHorizontalLogo) ? '' : $brandHorizontalLogo->storePubliclyAs("{$brandSpacesPath}", "{$brandSlug}-horizontal-logo", 'spaces');
        $brandSquareLogoPath = empty($brandSquareLogo) ? '' : $brandSquareLogo->storePubliclyAs("{$brandSpacesPath}", "{$brandSlug}-square-logo", 'spaces');

        $brandToCreate = new Brand();
        $brandToCreate->setBrandSlug($brandSlug);
        $brandToCreate->setBrandName($brandName);
        $brandToCreate->setBrandSquareLogo($brandSquareLogoPath);
        $brandToCreate->setBrandHorizontalLogo($brandHorizontalLogoPath);

        $newlyCreatedBrand = $this->brandService->createBrand($brandToCreate);
        if (empty($newlyCreatedBrand)) {
            return response()->json('We weren\'t able to create a brand. This is our fault.', 500);
        }

        return response()->json($newlyCreatedBrand->convertToArray(), 201);
    }

    public function deleteBrandById(Request $request, int $id): JsonResponse
    {
        $brand = $this->brandService->getBrandById($id);

        if (empty($brand)) {
            return response()->json('We couldn\'t find this brand.', 404);
        }

        $hasBrandBeenDeleted = $this->brandService->deleteBrand($brand);

        return response()->json($hasBrandBeenDeleted, $hasBrandBeenDeleted ? 200 : 500);
    }

    public function getBrands(): JsonResponse
    {
        $brands = $this->brandService->getBrands();

        return response()->json($brands->getPublicArray());
    }

    /**
     * @return JsonResponse
     * @uses AuthorizeUserMiddleware::handle()
     * @uses PassportMiddleware::handle()
     */
    public function getBrandsUserAdministrates(UserPermissionCollection $permissions): JsonResponse
    {
        $brands = $this->brandService->getBrandsByIds($permissions->getBrandIdsUserAdministrates()->all());
        return response()->json($brands->getPublicArray());
    }

    public function updateBrand(
        Brand $brand,
        string $brandName,
        ?UploadedFile $brandHorizontalLogo,
        string $brandSlug,
        ?UploadedFile $brandSquareLogo
    ): JsonResponse {
        $brandSpacesPath = "platformservice/brands/{$brandSlug}";
        $brandHorizontalLogoPath = empty($brandHorizontalLogo) ? '' : $brandHorizontalLogo->storePubliclyAs("{$brandSpacesPath}", "{$brandSlug}-horizontal-logo", 'spaces');
        $brandSquareLogoPath = empty($brandSquareLogo) ? '' : $brandSquareLogo->storePubliclyAs("{$brandSpacesPath}", "{$brandSlug}-square-logo", 'spaces');

        $brand->setBrandName($brandName);
        $brand->setBrandSlug($brandSlug);
        $brand->setBrandHorizontalLogo($brandHorizontalLogoPath);
        $brand->setBrandSquareLogo($brandSquareLogoPath);

        $updatedBrand = $this->brandService->updateBrand($brand);

        if (empty($updatedBrand)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedBrand->convertToArray());
    }

    public function getBrandById(Brand $brand): JsonResponse
    {
        return response()->json($brand->convertToArray());
    }

    public function getBrandBySlug(Request $request, string $slug): JsonResponse
    {
        /**
         * @var PassportStamp $passportStamp
         */
        $passportStamp = $request->get('passportStamp');

        if (empty($passportStamp)) {
            return response()->json('This requires a passport stamp.', 500);
        }

        $brand = $this->brandService->getBrandBySlug($slug);

        if (empty($brand)) {
            return response()->json('We didn\'t find this brand', 404);
        }

        $passportAuthorized = $this->authService->authorizeActionFromPassportStamp($passportStamp, 'read', 'brand', $brand->getId());

        if (!$passportAuthorized) {
            return response()->json('Hey there! Sorry, you may be in the wrong place', 403);
        }

        return response()->json($brand->convertToArray());
    }

    public function getBrandChannelPackageById(Request $request, int $brandId, int $channelId, int $packageId): JsonResponse
    {
        $channel = $this->channelService->getChannelById($channelId);

        if (empty($channel)) {
            return response()->json("We couldn't find this channel.", 404);
        }

        $configurations = $channel->getChannelConfigurations();
        $listSize = $configurations->getTotalSubscribers();

        $resourcePath = "brands/{$brandId}/channels/{$channelId}/packages/{$packageId}/?list_size={$listSize}";
        $package = $this->beaconService->getResourceByBeaconSlug('ads', $brandId, $channelId, $resourcePath);

        if (empty($package)) {
            return response()->json("We couldn't find this package.", 404);
        }

        return response()->json($package, 200, [], JSON_UNESCAPED_SLASHES);
    }

    /**
     * @param Request $request
     * @param int $channelId
     * @return JsonResponse
     * @deprecated
     */
    public function updateChannelConfigurationValue(Request $request, int $channelId): JsonResponse
    {
        $channelConfigurations = $request->input('configurations', []);

        foreach ($channelConfigurations as $channelConfiguration => $configurationValue) {
            $configuration = $this->brandService->getConfigurationBySlug($channelConfiguration);

            if (!empty($configuration)) {
                if ($configuration->getDataType() === 'array') {
                    $configurationValue = serialize($configurationValue);
                }
                $this->brandService->updateChannelConfiguration($channelId, $configurationValue, $configuration->getId());
            }
        }

        return response()->json(true);
    }

    /**
     * @group Brand APIs
     *
     * Ads by Channel
     *
     * Get the upcoming ads of a given brand channel. You must pass your brand's key as a bearer token.
     *
     * @authenticated
     * @urlParam brandSlug A brand's unique, hyphenated slug. Example: black-bitter-coffee
     * @urlParam channelSlug The unique, hyphenated slug of one of that brand's channels Example: bitter-coffee-times
     * @param Request $request
     * @param string $brandSlug
     * @param string $channelSlug
     * @response 200 [
     *   {
     *     "adType": {
     *       "brandId": 46,
     *       "channelId": 3,
     *       "hasEmoji": true,
     *       "hasImage": true,
     *       "id": 18,
     *       "inventory": 3,
     *       "order": 0,
     *       "slug": "1sjfhnmpqz8",
     *       "title": "The Bitterest Coffee Banner"
     *     },
     *     "adTypeId": 18,
     *     "blurb": "Join us for the third annual Bitter Coffee Bean chug-off, where we do our best not to burn our mouths.",
     *     "brandId": 46,
     *     "callToAction": "Sign-up early, get a free coffee.",
     *     "callToActionUrl": "https://black-bitter-coffee.com/rsvp",
     *     "channelId": 3,
     *     "dateStart": "2020-02-21",
     *     "emoji": "☕",
     *     "heading": "Bitter Coffee Bean Chug-off",
     *     "id": 261,
     *     "pixel": "https://newsletters.whereby.us/rangers/lfbvt127d1/{{alphanumericUniqueValuePerReader}}/p.jpg",
     *     "promoterDisplayName": "Black Bitter Coffee",
     *     "promoterImage": "https://source.unsplash.com/random/300x300",
     *     "resolvedCallToActionUrl": "https://link.whereby.us/lbfvt127d1",
     *     "uniqueId": "lbfvt127d1"
     *   }
     * ]
     * @return JsonResponse
     */
    public function getBrandChannelsAds(Request $request, string $brandSlug, string $channelSlug): JsonResponse
    {
        $brand = $request->get('brand');

        $channels = $brand->getChannels();
        $channel = array_reduce(
            $channels,
            function ($emptyResult, $channel) use ($channelSlug) {
                return $channel->getSlug() === $channelSlug ? $channel : $emptyResult;
            }
        );

        if (empty($channel)) {
            return response()->json('This channel is not part of this brand.', 404, []);
        }

        $date = $request->input('date', '');
        $dateQueryParameter = empty($date) ? '' : "&date={$date}";

        $resolveContent = $request->input('resolveContent', 'false');

        $resourcePath = "brands/{$brand->getId()}/channels/{$channel->getId()}/ads/?resolveContent={$resolveContent}{$dateQueryParameter}";

        $ads = $this->beaconService->getResourceByBeaconSlug('ads', $brand->getId(), $channel->getId(), $resourcePath);

        return response()->json($ads, 200, [], JSON_UNESCAPED_SLASHES);
    }

    public function createBrandAndChannel(string $brandName, string $brandSlug): JsonResponse
    {
        $brandToCreate = new Brand();
        $brandToCreate->setBrandSlug($brandSlug);
        $brandToCreate->setBrandName($brandName);
        $brandToCreate->setBrandSquareLogo('');
        $brandToCreate->setBrandHorizontalLogo('');

        $newlyCreatedBrand = $this->brandService->createBrand($brandToCreate);

        if (empty($newlyCreatedBrand)) {
            return response()->json('We weren\'t able to create a brand. This is our fault.', 500);
        }

        $this->dispatchNow(new ScaffoldPermissionsJob('brand', $newlyCreatedBrand->getId()));

        $newlyCreatedChannel = $this->channelService->createChannel(
            '#0000EE',
            $newlyCreatedBrand->getId(),
            '',
            '',
            '',
            $brandSlug,
            '',
            '',
            '',
            'Montserrat',
            false,
            'Montserrat',
            false,
            $brandName
        );

        $this->dispatchNow(new ScaffoldPermissionsJob('channel', $newlyCreatedChannel->getId()));

        $arrayOfChannels = [ $newlyCreatedChannel];
        $newlyCreatedBrand->setChannels($arrayOfChannels);

        return response()->json($newlyCreatedBrand->convertToArray(), 201);
    }
}
