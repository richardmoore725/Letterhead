<?php

namespace App\Http\Services;

use App\Http\Response;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\PromotionCredit;
use Illuminate\Http\Request;

interface AdServiceInterface
{
    public function createPromotion(Channel $channel, array $promotionMultipartArray): Response;
    public function calculateApplicationFeeAmount(int $finalPriceOfPackage, float $revenueShare): int;
    public function calculateFinalPriceOfPackage(int $amount, int $discountValue): int;
    public function getAdRequestFormattedForMultipartPost(Channel $channel, Request $request): array;
    public function getPromotionByPromotionId(int $promotionId, bool $renderMjml): ?Promotion;
    public function getPromotionCreditByPromotionId(int $promotionId): ?PromotionCredit;
    public function getPromotionMetricsByChannelId(Channel $channel): ?object;
    public function getPromotions(int $brandId, int $channelId, string $date, bool $renderMetricsAndHtml, bool $renderMjml, int $status): Response;
    public function getPromotionsFromFromJsonString(string $promotionsJson): array;
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
    ): ?object;
    public function updatePromotionStatus(Promotion $promotion, int $status): ?Promotion;
}
