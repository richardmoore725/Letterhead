<?php

namespace App\Http\Controllers;

use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\PackageServiceInterface;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PackageController extends Controller
{
    /**
     * @var string A simple slug that identifies which 2nd-party service we connect with.
     */
    private $beacon;

    /**
     * @var BeaconServiceInterface
     */
    private $beaconService;

    /**
     * @var PackageServiceInterface
     */
    private $packageService;

    public function __construct(BeaconServiceInterface $beaconService, PackageServiceInterface $packageService)
    {
        /**
         * AdTypes are part of the larger AdService.
         */
        $this->beacon = 'ads';
        $this->beaconService = $beaconService;
        $this->packageService = $packageService;
    }

    public function createPackage(Channel $channel, Request $request)
    {
        try {
            $packageFormData = $this->packageService->getPackageRequestFormattedForMultipartPost($request);
            $serviceEndpoint = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/packages";

            $package = $this->beaconService->createResourceByBeaconSlug(
                $this->beacon,
                $channel->getBrandId(),
                $channel->getId(),
                $serviceEndpoint,
                $packageFormData,
                true
            );

            if (empty($package)) {
                return response()->json("Shelackin'. We cannot create this package.", 500);
            }

            return response()->json($package, 201, [], JSON_UNESCAPED_SLASHES);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return response()->json($e->getMessage());
        }
    }

    public function getPackages(Channel $channel, Request $request): JsonResponse
    {
        /**
         * @todo This should really not be left up to a query parameter, and we need to check permissions.
         */
        $displayHidden = $request->input('displayHidden', 'false');
        $listSize = $channel->getChannelConfigurations()->getTotalSubscribers();

        /**
         * @see https://github.com/wherebyus/promotion-app/blob/staging/app/controllers/packages_controller.rb#L134
         */
        $queryParameterToAppendToEndpoint = "?displayHidden={$displayHidden}&list_size={$listSize}";
        $packages = $this->packageService->getPackageResourcesFromAdService($channel->getBrandId(), $channel->getId(), $queryParameterToAppendToEndpoint);
        $responseHeaders = [];
        $responseStatusCode = empty($packages) ? 404 : 200;

        return response()->json($packages, $responseStatusCode, $responseHeaders, JSON_UNESCAPED_SLASHES);
    }

    /**
     * Delete a package by its id
     *
     * @param int $packageId
     * @return JsonResponse
     */
    public function deletePackage(int $packageId): JsonResponse
    {
        $serviceEndpoint = "packages/{$packageId}";

        $wasPackageDeleted = $this->beaconService->deleteResourceFromService($this->beacon, $serviceEndpoint);

        if (empty($wasPackageDeleted)) {
            return response()->json($wasPackageDeleted, 500);
        }

        return response()->json($wasPackageDeleted, 200);
    }

    public function updatePackage(Channel $channel, int $packageId, Request $request): JsonResponse
    {
        $packageFormData = $this->packageService->getPackageRequestFormattedForMultipartPost($request);
        $serviceEndpoint = "brands/{$channel->getBrandId()}/channels/{$channel->getId()}/packages/{$packageId}";

        $updatedPackage = $this->beaconService->createResourceByBeaconSlug(
            $this->beacon,
            $channel->getBrandId(),
            $channel->getId(),
            $serviceEndpoint,
            $packageFormData,
            true
        );

        if (empty($updatedPackage)) {
            return response()->json("Criminy. We cannot update this package.", 500);
        }

        return response()->json($updatedPackage, 200, [], JSON_UNESCAPED_SLASHES);
    }
}
