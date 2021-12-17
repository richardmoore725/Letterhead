<?php

namespace App\Http\Services;

use App\Collections\DiscountCodeCollection;
use App\Models\DiscountCode;

interface DiscountCodeServiceInterface
{
    public function checkIfCodeWasAlreadyDefined(string $discountCode): bool;
    public function createDiscountCode(DiscountCode $discountCode): ?DiscountCode;
    public function getDiscountCodesByChannelId(int $channelId): ?DiscountCodeCollection;
    public function getDiscountCodeById(int $discountCodeId): ?DiscountCode;
    public function getDiscountCodeByCode(string $discountCode): ?DiscountCode;
    public function deleteDiscountCode(int $discountCodeId): bool;
    public function updateDiscountCode(DiscountCode $discountCode): ?DiscountCode;
}
