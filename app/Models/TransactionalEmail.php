<?php

namespace App\Models;

use App\DTOs\TransactionalEmailDto;

class TransactionalEmail
{
    public $brandId;
    public $channelId;
    public $createdAt;
    public $deletedAt;
    public $description;
    public $emailId;
    public $eventId;
    public $id;
    public $isActive;
    public $name;
    public $updatedAt;

    public function __construct(TransactionalEmailDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->brandId = $dto->brandId;
        $this->channelId = $dto->channelId;
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->description = $dto->description;
        $this->emailId = $dto->emailId;
        $this->eventId = $dto->eventId;
        $this->id = $dto->id;
        $this->isActive = $dto->isActive;
        $this->name = $dto->name;
        $this->updatedAt = $dto->updatedAt;
    }

    public function convertToDto(): TransactionalEmailDto
    {
        $dto = new TransactionalEmailDto();
        $dto->brandId = $this->brandId;
        $dto->channelId = $this->channelId;
        $dto->createdAt = $this->createdAt;
        $dto->deletedAt = $this->deletedAt;
        $dto->description = $this->description;
        $dto->emailId = $this->emailId;
        $dto->eventId = $this->eventId;
        $dto->id = $this->id;
        $dto->isActive = $this->isActive;
        $dto->name = $this->name;
        $dto->updatedAt = $this->updatedAt;

        return $dto;
    }

    public function getBrandId(): int
    {
        return $this->brandId;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEmailId(): int
    {
        return $this->emailId;
    }

    public function getEventId(): int
    {
        return $this->eventId;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setBrandId(int $brandId): void
    {
        $this->brandId = $brandId;
    }

    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setDeletedAt(string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setEmailId(int $emailId): void
    {
        $this->emailId = $emailId;
    }

    public function setEventId(int $eventId): void
    {
        $this->eventId = $eventId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function convertToArray(): array
    {
        return [
          'brandId' => $this->brandId,
          'channelId' => $this->channelId,
          'createdAt' => $this->createdAt,
          'deletedAt' => $this->deletedAt,
          'description' => $this->description,
          'emailId' => $this->emailId,
          'eventId' => $this->eventId,
          'id' => $this->id,
          'isActive' => $this->isActive,
          'name' => $this->name,
          'updatedAt' => $this->updatedAt,
        ];
    }

    public static function getValidationRules()
    {
        return [
            'brandId' => 'required|int',
            'channelId' => 'required|int',
            'description' => 'required|string',
            'emailId' => 'required|int',
            'eventId' => 'required|int',
            'isActive' => 'required|boolean',
            'name' => 'required|string',
        ];
    }
}
