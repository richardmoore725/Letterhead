<?php

namespace App\Http\Repositories;

use App\Collections\DiscountCodeCollection;
use App\DTOs\DiscountCodeDto;

interface DiscountCodeRepositoryInterface
{
    public function createDiscountCode(DiscountCodeDto $dto): ?DiscountCodeDto;
    public function getDiscountCodesByChannelId(int $channelId): ?DiscountCodeCollection;
    public function getDiscountCodeById(int $id): ?DiscountCodeDto;
    public function getDiscountCodeByCode(string $code): ?DiscountCodeDto;
    public function deleteDiscountCode(int $id): bool;
    public function updateDiscountCode(DiscountCodeDto $dto): ?DiscountCodeDto;
}
