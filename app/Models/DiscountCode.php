<?php

namespace App\Models;

use App\DTOs\DiscountCodeDto;

class DiscountCode
{
    private $channelId;
    private $createdAt;
    private $deletedAt;
    private $discountCode;
    private $discountValue;
    private $displayName;
    private $id;
    private $isActive;
    private $updatedAt;

    public function __construct(DiscountCodeDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->channelId = $dto->channelId;
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->discountCode = $dto->discountCode;
        $this->discountValue = $dto->discountValue;
        $this->displayName = $dto->displayName;
        $this->id = $dto->id;
        $this->isActive = $dto->isActive;
        $this->updatedAt = $dto->updatedAt;
    }

    public function convertToArray(): array
    {
        return [
            'channelId' => $this->channelId,
            'createdAt' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'discountCode' => $this->discountCode,
            'discountValue' => $this->discountValue,
            'displayName' => $this->displayName,
            'id' => $this->id,
            'isActive' => $this->isActive,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public function convertToDto(): DiscountCodeDto
    {
        $dto = new DiscountCodeDto();
        $dto->channelId = $this->channelId;
        $dto->createdAt = $this->createdAt;
        $dto->deletedAt = $this->deletedAt;
        $dto->discountCode = $this->discountCode;
        $dto->discountValue = $this->discountValue;
        $dto->displayName = $this->displayName;
        $dto->id = $this->id;
        $dto->isActive = $this->isActive;
        $dto->updatedAt = $this->updatedAt;

        return $dto;
    }

    public static function getValidationRules(): array
    {
        return [
            'channelId' => 'required|int',
            'discountCode' => 'required|string',
            'discountValue' => 'required|int',
            'displayName' => 'required|string',
            'isActive' => 'required|boolean',
        ];
    }

    // Getters
    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getDeletedAt(): string
    {
        if (empty($this->deletedAt)) {
            return '';
        }

        return $this->deletedAt;
    }

    public function getDiscountValue(): int
    {
        return $this->discountValue;
    }

    public function getId(): int
    {
        return $this->id;
    }

    // Setters
    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }
    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
    public function setDiscountCode(string $discountCode): void
    {
        $this->discountCode = $discountCode;
    }
    public function setDiscountValue(int $discountValue): void
    {
        $this->discountValue = $discountValue;
    }
    public function setDisplayName(string $displayName): void
    {
        $this->displayName = $displayName;
    }
    public function setId(int $id): void
    {
        $this->id = $id;
    }
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
