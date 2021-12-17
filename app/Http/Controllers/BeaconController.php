<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\PassportStamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeaconController extends Controller
{
    private $authService;
    private $beaconService;

    public function __construct(AuthServiceInterface $authService, BeaconServiceInterface $beaconService)
    {
        $this->authService = $authService;
        $this->beaconService = $beaconService;
    }

    public function createResource(Request $request, int $brandId, int $channelId, string $beaconSlug, $resource = ''): JsonResponse
    {
        $passport = $request->get('passportStamp');
        $signal = $request->get('signal');

        $canCreateOnBrand = $this->authService->authorizeActionFromPassportStamp(
            $passport,
            'create',
            'brand',
            $brandId
        );

        if (!$canCreateOnBrand) {
            return response()->json('You do not have the right privileges on this brand.', 403);
        }

        $canCreateOnChannel = $this->authService->authorizeActionFromPassportStamp(
            $passport,
            'create',
            'channel',
            $channelId
        );

        if (!$canCreateOnChannel) {
            return response()->json('You aren\'t setup to see this on this channel.', 403);
        }

        $test = $this->beaconService->createResourceByBeaconSlug($beaconSlug, $brandId, $channelId, $resource, $signal, false);
        return response()->json($test);
    }

    /**
     * @param Request $request
     * @param string $resource
     * @param null|string $identifier
     * @return JsonResponse
     * @todo Should probably constrain channels that belong to the Brand
     */
    public function getResource(Request $request, Brand $brand, string $beaconSlug, Channel $channel, PassportStamp $passport, $resource = ''): JsonResponse
    {
        $canCreateOnBrand = $this->authService->authorizeActionFromPassportStamp(
            $passport,
            'create',
            'brand',
            $brand->getId()
        );

        if (!$canCreateOnBrand) {
            return response()->json('You do not have the right privileges on this brand.', 403);
        }

        $resourcePath = "brands/{$brand->getId()}/channels/{$channel->getId()}/{$resource}";

        $resource = $this->beaconService->getResourceByBeaconSlug($beaconSlug, $brand->getId(), $channel->getId(), $resourcePath);

        if (empty($resource)) {
            return response()->json('It doesn\'t look like we found anything at this beacon.', 404);
        }

        return response()->json($resource, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
