<?php

namespace App\Models;

use App\DTOs\PlatformEventDto;

class PlatformEvent
{
    private $createdAt;
    private $description;
    private $deletedAt;
    private $eventSlug;
    private $id;
    private $name;
    private $updatedAt;

    public function __construct(PlatformEventDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->createdAt = $dto->createdAt;
        $this->description = $dto->description;
        $this->deletedAt = $dto->deletedAt;
        $this->eventSlug = $dto->eventSlug;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->updatedAt = $dto->updatedAt;
    }

    public function convertToDto(): PlatformEventDto
    {
        $dto = new PlatformEventDto();
        $dto->createdAt = $this->createdAt;
        $dto->description = $this->description;
        $dto->deletedAt = $this->deletedAt;
        $dto->eventSlug = $this->eventSlug;
        $dto->id = $this->id;
        $dto->name = $this->name;
        $dto->updatedAt = $this->updatedAt;

        return $dto;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEventSlug(): string
    {
        return $this->eventSlug;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setEventSlug(string $eventSlug): void
    {
        $this->eventSlug = $eventSlug;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }


    public function setId($id): void
    {
        $this->id = $id;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt($deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function convertToArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'description' => $this->description,
            'eventSlug' => $this->eventSlug,
            'id' => $this->id,
            'name' => $this->name,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'description' => 'required|string',
            'eventSlug' => 'required|string',
            'name' => 'required|string',
        ];
    }
}
