<?php

namespace App\Models;

use App\DTOs\MessageDto;
use Carbon\CarbonImmutable;

/**
 * The `Message` is a simple model designed for leaving messages - either by a user
 * or by the system - about a specific resource. We use Messages for activity tracking
 * as well as user-to-user commenting.
 *
 * Class Message
 * @package App\Models
 */
class Message
{
    private $createdAt;
    private $deletedAt;
    private $id;
    private $message;
    private $resourceId;
    private $resourceName;
    private $uniqueId;
    private $userId;

    public function __construct(MessageDto $dto)
    {
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->id = $dto->id;
        $this->message = $dto->message;
        $this->resourceId = $dto->resourceId;
        $this->resourceName = $dto->resourceName;
        $this->uniqueId = $dto->uniqueId;
        $this->userId = $dto->userId;
    }

    public function convertToDto(): MessageDto
    {
        $dto = new MessageDto();
        $dto->createdAt = $this->getCreatedAt();
        $dto->deletedAt = $this->getDeletedAt();
        $dto->id = $this->getId();
        $dto->message = $this->getMessage();
        $dto->resourceId = $this->getResourceId();
        $dto->resourceName = $this->getResourceName();
        $dto->uniqueId = $this->getUniqueId();
        $dto->userId = $this->getUserId();

        return $dto;
    }

    public function convertToArray(): array
    {
        return [
            'createdAt' => $this->getCreatedAt(),
            'deletedAt' => $this->getDeletedAt(),
            'id' => $this->getId(),
            'message' => $this->getMessage(),
            'resourceId' => $this->getResourceId(),
            'resourceName' => $this->getResourceName(),
            'uniqueId' => $this->getUniqueId(),
            'userId' => $this->getUserId(),
        ];
    }

    /**
     * Lumen-side validation rules we use to ensure that POSTs include the
     * required values.
     *
     * @return array
     * @see https://laravel.com/docs/7.x/validation#available-validation-rules
     */
    public static function getValidationRules(): array
    {
        return [
            'message' => 'required|string',
            'resourceId' => 'required|integer',
            'resourceName' => 'required|string',
            'userId' => 'nullable|integer'
        ];
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getResourceId(): int
    {
        return $this->resourceId;
    }

    public function getResourceName(): string
    {
        return $this->resourceName;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setCreatedAtToNow(): void
    {
        $this->setCreatedAt(CarbonImmutable::now()->toAtomString());
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function setResourceId(int $resourceId): void
    {
        $this->resourceId = $resourceId;
    }

    public function setResourceName(string $resourceName): void
    {
        $this->resourceName = $resourceName;
    }

    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
