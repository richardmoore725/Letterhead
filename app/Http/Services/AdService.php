<?php

namespace App\Http\Services;

use App\DTOs\PromotionDto;
use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Repositories\PromotionRepositoryInterface;
use App\Http\Response;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\PromotionCredit;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;

class AdService implements AdServiceInterface
{
    private $beaconRepository;
    private $beaconService;

    /**
     * @var PromotionRepositoryInterface
     */
    private $promotionRepository;

    public function __construct(
        BeaconRepositoryInterface $beaconRepository,
        BeaconServiceInterface $beaconService,
        PromotionRepositoryInterface $promotionRepository
    ) {
        $this->beaconRepository = $beaconRepository;
        $this->beaconService = $beaconService;
        $this->promotionRepository = $promotionRepository;
    }

    public function calculateApplicationFeeAmount(int $finalPriceOfPackage, float $revenueShare): int
    {
        return (int) floor($revenueShare * $finalPriceOfPackage);
    }

    public function calculateFinalPriceOfPackage(int $amount, int $discountValue): int
    {
        return (int) floor(( 1 - $discountValue / 100 ) * $amount);
    }

    public function createPromotion(Channel $channel, array $promotionMultipartArray): Response
    {
        $repositoryResponse = $this->promotionRepository->createPromotion($promotionMultipartArray, $channel);

        if ($repositoryResponse->isError()) {
            return $repositoryResponse;
        }

        /**
         * @var PromotionDto $promotionDto
         */
        $promotionDto = $repositoryResponse->getData();
        return new Response('', 201, $promotionDto->convertToModel());
    }

    /**
     * In order to pass a form that may include a file to an API through
     * a library like Guzzle, we need to format it in this somewhat obnoxious way.
     *
     * @param Request $request
     * @return array
     */
    public function getAdRequestFormattedForMultipartPost(Channel $channel, Request $request): array
    {
        $adCreditId = $request->input('adCreditId');
        $adTypeId = (int) $request->input('adTypeId');
        $blurb = $request->input('blurb');
        $callToAction = $request->input('callToAction');
        $callToActionUrl = $request->input('callToActionUrl');
        $content = $request->input('content', '');
        $dateStart = $request->input('dateStart');
        $emoji = $request->input('emoji');
        $heading = $request->input('heading');
        $platformUserId = $request->input('platformUserId');
        $promoterDisplayName = $request->input('promoterDisplayName');
        $requireAdCreditId = $request->input('requireAdCreditId', 'true');
        $status = (int) $request->input('status', Promotion::STATUS_NEWLY_CREATED);

        $channelTimezone = $channel->getChannelConfigurations()->getTimezone()->getOffset();
        $scheduledDate = CarbonImmutable::createFromFormat('Y-m-d', $dateStart, $channelTimezone)->toAtomString();

        $adServicePostDataArray = [
            [
                'name' => 'adCreditId',
                'contents' => $adCreditId
            ],
            [
                'name' => 'adTypeId',
                'contents' => $adTypeId
            ],
            [
                'name' => 'blurb',
                'contents' => $blurb
            ],
            [
                'name' => 'brandId',
                'contents' => $channel->getBrandId(),
            ],
            [
                'name' => 'callToAction',
                'contents' => $callToAction
            ],
            [
                'name' => 'callToActionUrl',
                'contents' => $callToActionUrl
            ],
            [
                'name' => 'channelId',
                'contents' => $channel->getId(),
            ],
            [
                'name' => 'content',
                'contents' => $content,
            ],
            [
                'name' => 'dateStart',
                'contents' => $dateStart
            ],
            [
                'name' => 'emoji',
                'contents' => $emoji
            ],
            [
                'name' => 'heading',
                'contents' => $heading
            ],
            [
                'name' => 'orderId',
                'contents' => 0,
            ],
            [
                'name' => 'platformUserId',
                'contents' => $platformUserId,
            ],
            [
                'name' => 'promoterDisplayName',
                'contents' => $promoterDisplayName
            ],
            [
                'name' => 'promoterImageAlternativeText',
                'contents' => $request->input('promoterImageAlternativeText', ''),
            ],
            [
                'name' => 'requireAdCreditId',
                'contents' => $requireAdCreditId,
            ],
            [
                'name' => 'scheduledDate',
                'contents' => $scheduledDate,
            ],
            [
                'name' => 'status',
                'contents' => $status,
            ],
        ];

        /**
         * If `promoterImage` or `advertiserLogo` is a file then we have to add an additional `filename`
         * as well as "open" the attached file before we can pass it along.
         */
        if ($request->hasFile('promoterImage')) {
            $promoterImage = $request->file('promoterImage');

            $adServicePostDataArray[] = [
                'name' => 'promoterImage',
                'contents' => fopen($promoterImage->path(), 'r'),
                'filename' => $promoterImage->getClientOriginalName(),
            ];
        } else {
            $adServicePostDataArray[] = [
                'name' => 'promoterImage',
                'contents' => $request->input('promoterImage', ''),
            ];
        }

        if ($request->hasFile('advertiserLogo')) {
            $advertiserLogo = $request->file('advertiserLogo');

            $adServicePostDataArray[] = [
                'name' => 'advertiserLogo',
                'contents' => fopen($advertiserLogo->path(), 'r'),
                'filename' => $advertiserLogo->getClientOriginalName(),
            ];
        } else {
            $adServicePostDataArray[] = [
                'name' => 'advertiserLogo',
                'contents' => $request->input('advertiserLogo', ''),
            ];
        }


        return $adServicePostDataArray;
    }

    /**
     * Returns the ad credit used for a specific promotion by promotion ID
     */
    public function getPromotionCreditByPromotionId(int $promotionId): ?PromotionCredit
    {
        $endpoint = env('SERVICE_ADS_ENDPOINT');
        $key = env('SERVICE_ADS_KEY');
        $path = "{$endpoint}/promotions/{$promotionId}/credit";

        $adCredit = $this->beaconRepository->getResourceFromService($path, $key);

        if (empty($adCredit)) {
            return null;
        }

        $promotionCredit = new PromotionCredit();
        $orderId = empty($adCredit->orderId) ? 0 : $adCredit->orderId;
        $promotionCredit->setOrderId($orderId);
        $promotionCredit->setUserId($adCredit->userId);

        return $promotionCredit;
    }

    /**
     * Get a specific channel's aggregate promotion metrics for the last thirty
     * days.
     * @param Channel $channel
     * @return mixed
     */
    public function getPromotionMetricsByChannelId(Channel $channel): ?object
    {
        $endpoint = env('SERVICE_ADS_ENDPOINT');
        $key = env('SERVICE_ADS_KEY');
        $path = "{$endpoint}/brands/{$channel->getBrandId()}/channels/{$channel->getId()}/ads/metrics";

        return $this->beaconRepository->getResourceFromService($path, $key);
    }

    /**
     * Return an array of Promotion models given a json string promotions.
     * @param string $promotionsJson
     * @return array[Promotion]
     */
    public function getPromotionsFromFromJsonString(string $promotionsJson): array
    {
        $arrayOfPromotionObjects = json_decode($promotionsJson);

        return array_map(function (object $promotionObject) {
            $promotionDto = new PromotionDto(null, $promotionObject);
            return new Promotion($promotionDto);
        }, $arrayOfPromotionObjects);
    }

    public function getPromotions(
        int $brandId,
        int $channelId,
        string $date,
        bool $renderMetricsAndHtml,
        bool $renderMjml,
        int $status
    ): Response {
        return $this->promotionRepository->getPromotions(
            $brandId,
            $channelId,
            $date,
            0,
            0,
            $renderMetricsAndHtml,
            $renderMjml,
            $status
        );
    }

    /**
     * Send what we need to to place an order and book a promotion
     */
    public function orderAndBookSinglePromotion(
        int $brandId,
        int $channelId,
        int $finalPriceOfPackage,
        string $dateStart,
        int $discountCodeId,
        int $discountValue,
        int $userId,
        int $originalPurchasePrice,
        string $paymentMethod,
        int $promotionTypeId,
        float $revenueShare,
        string $userName
    ): ?object {
        /**
         * Now that we've charged the user, let's place create a package,
         * place an order, and book a promotion.
         */
        $adsBeaconSlug = "ads";
        $adServiceResourcePath = "brands/{$brandId}/channels/{$channelId}/orders/individual";
        $adServicePostDataArray = [
            'amount' => $finalPriceOfPackage,
            'brandId' => $brandId,
            'channelId' => $channelId,
            'dateStart' => $dateStart,
            'discountCodeId' => $discountCodeId,
            'discountValue' => $discountValue,
            'platformUserId' => $userId,
            'originalPurchasePrice' => $originalPurchasePrice,
            'promotionTypeId' => $promotionTypeId,
            'stripe_id' => $paymentMethod,
            'revenueShare' => $revenueShare,
            'userName' => $userName,
        ];

        return $this->beaconService->createResourceByBeaconSlug($adsBeaconSlug, $brandId, $channelId, $adServiceResourcePath, $adServicePostDataArray, false);
    }

    public function getPromotionByPromotionId(int $adId, bool $renderMjml): ?Promotion
    {
        $response = $this->promotionRepository->getPromotion($adId, false);

        $promotion = $response->getData();

        if (empty($promotion)) {
            return null;
        }

        return $promotion;
    }

    public function updatePromotionStatus(Promotion $promotion, int $status): ?Promotion
    {
        $adId = $promotion->getId();
        $promotion->setStatus($status);

        $promotionAsArray = $promotion->convertToArray();

        $response =  $this->promotionRepository->updatePromotion($adId, $promotionAsArray);

        $updatedPromotion = $response->getData();

        if (empty($updatedPromotion)) {
            return null;
        }

        return $updatedPromotion;
    }
}
