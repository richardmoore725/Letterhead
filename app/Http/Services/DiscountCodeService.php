<?php

namespace App\Http\Services;

use App\Collections\DiscountCodeCollection;
use App\DTOs\DiscountCodeDto;
use App\Models\DiscountCode;
use App\Http\Repositories\DiscountCodeRepositoryInterface;

class DiscountCodeService implements DiscountCodeServiceInterface
{
    private $repository;

    public function __construct(DiscountCodeRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function checkIfCodeWasAlreadyDefined(string $discountCode): bool
    {
        $wasThisCodeAlreadyDefined = $this->repository->getDiscountCodeByCode($discountCode);

        return isset($wasThisCodeAlreadyDefined);
    }

    public function createDiscountCode(DiscountCode $discountCode): ?DiscountCode
    {
        $dto = $this->repository->createDiscountCode($discountCode->convertToDto());

        if (empty($dto)) {
            return null;
        }

        return new DiscountCode($dto);
    }

    public function getDiscountCodesByChannelId(int $channelId): ?DiscountCodeCollection
    {
        $codes = $this->repository->getDiscountCodesByChannelId($channelId);

        if (empty($codes)) {
            return null;
        }

        return $codes;
    }

    public function getDiscountCodeById(int $discountCodeId): ?DiscountCode
    {
        $code = $this->repository->getDiscountCodeById($discountCodeId);

        if (empty($code)) {
            return null;
        }

        return new DiscountCode($code);
    }

    public function getDiscountCodeByCode(string $discountCode): ?DiscountCode
    {
        $code = $this->repository->getDiscountCodeByCode($discountCode);

        if (empty($code)) {
            return null;
        }

        return new DiscountCode($code);
    }

    public function deleteDiscountCode(int $discountCodeId): bool
    {
        $discountCodeWasDeleted = $this->repository->deleteDiscountCode($discountCodeId);

        return $discountCodeWasDeleted;
    }

    public function updateDiscountCode(DiscountCode $discountCode): ?DiscountCode
    {
        $updatedDto = $this->repository->updateDiscountCode($discountCode->convertToDto());

        if (empty($updatedDto)) {
            return null;
        }

        return new DiscountCode($updatedDto);
    }
}
