<?php

namespace App\Http\Services;

use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Response;
use Illuminate\Http\Request;

class AdTypeService implements AdTypeServiceInterface
{
    /**
     * @var BeaconRepositoryInterface
     */
    private $repository;

    public function __construct(BeaconRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * In order to pass a form that may include a file to an API through
     * a library like Guzzle, we need to format it in this somewhat obnoxious way.
     *
     * @param Request $request
     * @return array
     */
    public function getAdTypeRequestFormattedForMultipartPost(Request $request): array
    {
        $adTypeFormData = [
            [
                'name' => 'adImageHeight',
                'contents' => (int) $request->input('adImageHeight', 300),
            ],
            [
                'name' => 'adImageWidth',
                'contents' => (int) $request->input('adImageWidth', 300),
            ],
            [
                'name' => 'blurbCharacterCount',
                'contents' => (int) $request->input('blurbCharacterCount', 140),
            ],
            [
                'name' => 'brand_id',
                'contents' => (int) $request->input('brandId'),
            ],
            [
                'name' => 'callToActionCharacterCount',
                'contents' => (int) $request->input('callToActionCharacterCount', 50),
            ],
            [
                'name' => 'channel_id',
                'contents' => (int) $request->input('channelId'),
            ],
            [
                'name' => 'contentCharacterCount',
                'contents' => (int) $request->input('contentCharacterCount', 50),
            ],
            [
                'name' => 'cpm',
                'contents' => (int) $request->input('cpm', 0),
            ],
            [
                'name' => 'daysPublished',
                'contents' => json_encode($request->input('daysPublished', []), true),
            ],
            [
                'name' => 'description',
                'contents' => $request->input('description', ''),
            ],
            [
                'name' => 'descriptionCharacterCount',
                'contents' => (int) $request->input('descriptionCharacterCount', 140),
            ],
            [
                'name' => 'headingCharacterCount',
                'contents' => (int) $request->input('headingCharacterCount', 50),
            ],
            [
                'name' => 'hasAdvertiserLogo',
                'contents' => $request->input('hasAdvertiserLogo', 'false'),
            ],
            [
                'name' => 'hasBlurb',
                'contents' => $request->input('hasBlurb', 'true'),
            ],
            [
                'name' => 'hasCallToAction',
                'contents' => $request->input('hasCallToAction', 'true'),
            ],
            [
                'name' => 'hasContent',
                'contents' => $request->input('hasContent', 'false'),
            ],
            [
                'name' => 'hasCustomSchedule',
                'contents' => $request->input('hasCustomSchedule', 'false'),
            ],
            [
                'name' => 'hasEmoji',
                'contents' => $request->input('hasEmoji', 'false'),
            ],
            [
                'name' => 'hasHeading',
                'contents' => $request->input('hasHeading', 'true'),
            ],
            [
                'name' => 'hasImage',
                'contents' => $request->input('hasImage', 'false'),
            ],
            [
                'name' => 'inventory',
                'contents' => (int) $request->input('inventory', 1),
            ],
            [
                'name' => 'order',
                'contents' => (int) $request->input('order', 0),
            ],
            [
                'name' => 'positioning',
                'contents' => (int) $request->input('positioning', 0),
            ],
            [
                'name' => 'title',
                'contents' => $request->input('title', ''),
            ],
        ];

        /**
         * If `screenshot` is a file then we have to add an additional `filename`
         * as well as "open" the attached file before we can pass it along.
         */
        if ($request->hasFile('screenshot')) {
            $screenshot = $request->file('screenshot');

            $adTypeFormData[] = [
                'name' => 'screenshot',
                'contents' => fopen($screenshot->path(), 'r'),
                'filename' => $screenshot->getClientOriginalName(),
            ];
        } else {
            $adTypeFormData[] = [
                'name' => 'screenshot',
                'contents' => $request->input('screenshot', ''),
            ];
        }

        return $adTypeFormData;
    }

    public function getAvailableDatesByAdType(int $adTypeId, int $brandId, int $channelId, array $disabledDates): Response
    {
        $promotionServiceApiUrl = env('SERVICE_ADS_ENDPOINT');

        $endpoint = "{$promotionServiceApiUrl}/brands/{$brandId}/channels/{$channelId}/ads/available-dates-by-type/{$adTypeId}";
        $promotionServiceKey = env('SERVICE_ADS_KEY', '');

        $repositoryResponse = $this->repository->getResponseFromApi(
            $endpoint,
            $promotionServiceKey,
            'GET',
            [
                "disabledDates" => $disabledDates,
            ]
        );

        return $repositoryResponse;
    }

    private function getPromotionTypeEndpoint(int $brandId, int $channelId, ?int $promotionTypeId): string
    {
        $promotionServiceApiUrl = env('SERVICE_ADS_ENDPOINT');

        return empty($promotionTypeId)
            ? "{$promotionServiceApiUrl}/brands/{$brandId}/channels/{$channelId}/ads/types"
            : "{$promotionServiceApiUrl}/ad-types/{$promotionTypeId}";
    }

    public function getAdTypesWithPricesByChannel(int $brandId, int $channelId, int $listSize): Response
    {
        $endpoint = "{$this->getPromotionTypeEndpoint($brandId, $channelId, null)}/prices/?listSize={$listSize}";
        $promotionServiceKey = env('SERVICE_ADS_KEY', '');

        $repositoryResponse = $this->repository->getResponseFromApi(
            $endpoint,
            $promotionServiceKey,
            'GET',
            []
        );

        return $repositoryResponse;
    }

    public function updatePromotionTypeTemplate(int $brandId, int $channelId, int $promotionTypeId, string $template): Response
    {
        $endpoint = "{$this->getPromotionTypeEndpoint($brandId, $channelId, $promotionTypeId)}/template";
        $promotionServiceKey = env('SERVICE_ADS_KEY', '');
        $requestBody = [
            'mjmlTemplate' => $template,
        ];

        $repositoryResponse = $this->repository->getResponseFromApi(
            $endpoint,
            $promotionServiceKey,
            'POST',
            $requestBody
        );

        if ($repositoryResponse->isError()) {
            $error = $repositoryResponse->getData();
            $errorMessage = isset($error->message) ? $error->message : '';
            return new Response($errorMessage, $repositoryResponse->getStatus());
        }

        return $repositoryResponse;
    }

    /**
     * Scaffolds the default promotion types for a channel if no other types currently
     * exist for it. While we only return true or false, failure can actually result
     * because a type already exists (a 400 error at the repository layer), or of course there
     * is some other server error (a 500).
     *
     * @param int $brandId
     * @param int $channelId
     * @return bool
     */
    public function scaffoldDefaultPromotionTypesForNewChannel(int $brandId, int $channelId): bool
    {
        $promotionServiceApiUrl = env('SERVICE_ADS_ENDPOINT');
        $promotionServiceKey = env('SERVICE_ADS_KEY');

        $endpoint = "{$promotionServiceApiUrl}/brands/{$brandId}/channels/{$channelId}/types/scaffold";
        $wasScaffoldingSuccessful = $this->repository->createBrandChannelResourceFromService(
            $endpoint,
            $promotionServiceKey,
            $brandId,
            $channelId,
            null,
            false
        );

        return empty($wasScaffoldingSuccessful) ? false : $wasScaffoldingSuccessful;
    }
}
