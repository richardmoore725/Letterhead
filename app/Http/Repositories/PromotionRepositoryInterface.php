<?php

namespace App\Http\Repositories;

use App\Http\Response;
use App\Models\Channel;

interface PromotionRepositoryInterface
{
    public function createPromotion(array $promotionMultipartArray, Channel $channel): Response;
    public function getPromotions(
        int $brandId,
        int $channelId,
        string $date,
        int $limit,
        int $page,
        bool $renderMetricsAndHtml,
        bool $renderMjml,
        int $status
    ): Response;
    public function getPromotion(
        int $adId,
        bool $renderMjml
    ): Response;
    public function updatePromotion(
        int $adId,
        array $promotion
    ): Response;
}
