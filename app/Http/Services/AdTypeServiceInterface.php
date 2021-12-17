<?php

namespace App\Http\Services;

use App\Http\Response;
use Illuminate\Http\Request;

interface AdTypeServiceInterface
{
    public function getAdTypeRequestFormattedForMultipartPost(Request $request): array;
    public function getAvailableDatesByAdType(int $adTypeId, int $brandId, int $channelId, array $disabledDates): Response;
    public function getAdTypesWithPricesByChannel(int $brandId, int $channelId, int $listSize): Response;
    public function scaffoldDefaultPromotionTypesForNewChannel(int $brandId, int $channelId): bool;
    public function updatePromotionTypeTemplate(int $brandId, int $channelId, int $promotionTypeId, string $template): Response;
}
