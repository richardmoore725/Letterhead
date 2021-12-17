<?php

namespace App\Http\Repositories;

use App\Collections\PromotionCollection;
use App\DTOs\PromotionDto;
use App\DTOs\UserDto;
use App\Http\Response;
use App\Models\Channel;
use App\Models\PassportStamp;
use App\Models\Promotion;

/**
 * The PromotionRepository is responsible largely for interfacing with PromotionService, either
 * over the API or with one of its related resources. While we don't usually like to
 * extend classes, PromotionRepository extends BeaconRepository to utilize its API
 * connectors.
 *
 * Class PromotionRepository
 * @package App\Http\Repositories
 */
class PromotionRepository extends BeaconRepository implements PromotionRepositoryInterface
{
    /**
     * Create a promotion with a multipart array. Eventually we should consider
     * changing this to a PromotionDto.
     *
     * @param array $promotionMultipartArray
     * @param Channel $channel
     * @return Response
     */
    public function createPromotion(array $promotionMultipartArray, Channel $channel): Response
    {
        $endpoint = $this->getEndpoint($channel);

        /**
         * @see BeaconRepository::getResponseFromApi()
         */
        $responseFromAdServiceApi = $this->getResponseFromApi($endpoint, $this->getServiceKey(), 'POST', $promotionMultipartArray, 'Bearer', true);

        if ($responseFromAdServiceApi->isError()) {
            return $responseFromAdServiceApi;
        }

        $promotionObject = $responseFromAdServiceApi->getData();
        $promotionDto = new PromotionDto(null, $promotionObject);

        return new Response('', 201, $promotionDto);
    }

    private function getEndpoint(Channel $channel): string
    {
        $endpoint = env('SERVICE_ADS_ENDPOINT');

        return "{$endpoint}/brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads";
    }

    public function getPromotions(
        int $brandId,
        int $channelId,
        string $date,
        int $limit,
        int $page,
        bool $renderMetricsAndHtml,
        bool $renderMjml,
        int $status
    ): Response {
        $dateQueryParameter = empty($date) ? '' : "&date={$date}";
        $endpointRoot = env('SERVICE_ADS_ENDPOINT');
        $mjml = $renderMjml ? 'true' : 'false';
        $resolveContent = $renderMetricsAndHtml ? 'true' : 'false';
        $endpoint = "{$endpointRoot}/brands/{$brandId}/channels/{$channelId}/ads?resolveContent={$resolveContent}{$dateQueryParameter}&mjml={$mjml}&status={$status}";

        $responseFromAdServiceApi = $this->getResponseFromApi($endpoint, $this->getServiceKey(), 'GET', []);

        if ($responseFromAdServiceApi->isError()) {
            return $responseFromAdServiceApi;
        }

        $arrayOfPromotions = $responseFromAdServiceApi->getData();
        $promotionCollection = new PromotionCollection($arrayOfPromotions);

        return new Response('', 200, $promotionCollection);
    }

    public function getPromotion(int $adId, bool $renderMjml): Response
    {
        $endpointRoot = env('SERVICE_ADS_ENDPOINT');
        $mjml = $renderMjml ? 'true' : 'false';
        $endpoint = "{$endpointRoot}/promotions/{$adId}/?mjml={$mjml}";

        $response = $this->getResponseFromApi($endpoint, $this->getServiceKey(), 'GET', []);

        if ($response->isError()) {
            return $response;
        }

        $promotionFromResponse = $response->getData();
        $dto = new PromotionDto(null, $promotionFromResponse);
        $promotion = new Promotion($dto);

        return new Response('', 200, $promotion);
    }

    public function updatePromotion(int $adId, array $promotion): Response
    {
        $endpointRoot = env('SERVICE_ADS_ENDPOINT');
        $endpoint = "{$endpointRoot}/ads/{$adId}";

        $response = $this->getResponseFromApi($endpoint, $this->getServiceKey(), 'POST', $promotion, 'Bearer', false);

        if ($response->isError()) {
            return $response;
        }

        $promotionFromResponse = $response->getData();
        $dto = new PromotionDto(null, $promotionFromResponse);
        $promotion = new Promotion($dto);

        return new Response('', 200, $promotion);
    }

    private function getServiceKey(): string
    {
        $key = env('SERVICE_ADS_KEY');

        return $key;
    }
}
