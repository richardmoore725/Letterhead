<?php

namespace App\Http\Services;

use App\Http\Repositories\BeaconRepositoryInterface;
use Illuminate\Http\Request;

class PackageService implements PackageServiceInterface
{
    /**
     * @var BeaconRepositoryInterface
     */
    private $beaconRepository;

    public function __construct(BeaconRepositoryInterface $beaconRepository)
    {
        $this->beaconRepository = $beaconRepository;
    }

    /**
     * Use `getPackageResourceFromAdService` to lookup resources from the Packages API;
     *
     * @param string $path
     * @return null | mixed
     */
    public function getPackageResourcesFromAdService(int $brandId, int $channelId, string $path)
    {
        $endpoint = env('SERVICE_ADS_ENDPOINT');
        $key = env('SERVICE_ADS_KEY');

        if (empty($endpoint) || empty($key)) {
            return null;
        }

        $api = "{$endpoint}/brands/{$brandId}/channels/{$channelId}/packages/{$path}";

        return $this->beaconRepository->getAdResourceFromService($api, $key, $path);
    }

    /**
     * In order to pass a form that may include a file to an API through
     * a library like Guzzle, we need to format it in this somewhat obnoxious way.
     *
     * @param Request $request
     * @return array
     */
    public function getPackageRequestFormattedForMultipartPost(Request $request): array
    {
        $arrayOfAdTypeObjects = array_map(function ($type) {
            return json_decode($type);
        }, $request->input('adTypesInPackage', []));

        $packageFormData = [
            [
                'name' => 'adTypesInPackage',
                'contents' => json_encode($arrayOfAdTypeObjects, JSON_UNESCAPED_SLASHES),
            ],
            [
                'name' => 'brandId',
                'contents' => (int) $request->input('brandId'),
            ],
            [
                'name' => 'channelId',
                'contents' => (int) $request->input('channelId'),
            ],
            [
                'name' => 'description',
                'contents' => $request->input('description'),
            ],
            [
                'name' => 'displayOrder',
                'contents' => (int) $request->input('displayOrder'),
            ],
            [
                'name' => 'isDisplayed',
                'contents' => $request->input('isDisplayed', true),
            ],
            [
                'name' => 'name',
                'contents' => $request->input('name'),
            ],
            [
                'name' => 'price',
                'contents' => (int) $request->input('price'),
            ],
            [
                'name' => 'hasDiscount',
                'contents' => $request->input('hasDiscount', false),
            ],
            [
                'name' => 'hasPercentageDiscount',
                'contents' => $request->input('hasPercentageDiscount', false),
            ],
            [
                'name' => 'discount',
                'contents' => (float) $request->input('discount'),
            ],
            [
                'name' => 'useFlatFee',
                'contents' => $request->input('useFlatFee'),
            ],
        ];

        /**
         * If `packageImage` is a file then we have to add an additional `filename`
         * as well as "open" the attached file before we can pass it along.
         */
        if ($request->hasFile('packageImage')) {
            $packageImage = $request->file('packageImage');

            $packageFormData[] = [
                'name' => 'packageImage',
                'contents' => fopen($packageImage->path(), 'r'),
                'filename' => $packageImage->getClientOriginalName(),
            ];
        } else {
            $packageFormData[] = [
                'name' => 'packageImage',
                'contents' => $request->input('packageImage', ''),
            ];
        }

        return $packageFormData;
    }
}
