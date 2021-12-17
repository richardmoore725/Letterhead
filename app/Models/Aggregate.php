<?php

namespace App\Models;

use App\DTOs\AggregateDto;

class Aggregate
{
    private $archived;
    private $channelId;
    private $createdAt;
    private $curated;
    private $dateOfAggregatePublication;
    private $deletedAt;
    private $excerpt;
    private $id;
    private $image;
    private $letterId;
    private $originalUrl;
    private $siteName;
    private $title;
    private $uniqueId;
    private $updatedAt;

    public function __construct(\stdClass $object = null, AggregateDto $dto = null)
    {
        if (!empty($object)) {
            $this->archived = $object->archived;
            $this->channelId = $object->channelId;
            $this->createdAt = $object->createdAt;
            $this->curated = (bool) $object->curated;
            $this->dateOfAggregatePublication = $object->dateOfAggregatePublication;
            $this->deletedAt = $object->deletedAt;
            $this->excerpt = $object->excerpt;
            $this->id = $object->id;
            $this->image = $object->image;
            $this->letterId = $object->letterId;
            $this->originalUrl = $object->originalUrl;
            $this->siteName = $object->siteName;
            $this->title = $object->title;
            $this->uniqueId = $object->uniqueId;
            $this->updatedAt = $object->updatedAt;
        }

        if (!empty($dto)) {
            $this->archived = $dto->archived;
            $this->channelId = $dto->channelId;
            $this->createdAt = $dto->createdAt;
            $this->curated = (bool) $dto->curated;
            $this->dateOfAggregatePublication = $dto->dateOfAggregatePublication;
            $this->deletedAt = $dto->deletedAt;
            $this->excerpt = $dto->excerpt;
            $this->id = $dto->id;
            $this->image = $dto->image;
            $this->letterId = $dto->letterId;
            $this->originalUrl = $dto->originalUrl;
            $this->siteName = $dto->siteName;
            $this->title = $dto->title;
            $this->uniqueId = $dto->uniqueId;
            $this->updatedAt = $dto->updatedAt;
        }
    }

    public function convertToArray(): array
    {
        return [
            'archived' => $this->getArchived(),
            'channelId' => $this->getChannelId(),
            'createdAt' => $this->getCreatedAt(),
            'curated' => $this->getCurated(),
            'dateOfAggregatePublication' => $this->getDateOfAggregatePublication(),
            'deletedAt' => $this->getDeletedAt(),
            'excerpt' => $this->getExcerpt(),
            'id' => $this->getId(),
            'image' => $this->getImage(),
            'letterId' => $this->getLetterId(),
            'originalUrl' => $this->getOriginalUrl(),
            'siteName' => $this->getSiteName(),
            'title' => $this->getTitle(),
            'uniqueId' => $this->getUniqueId(),
            'updatedAt' => $this->getUpdatedAt(),
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
            'archived' => 'required|boolean',
            'channelId' => 'required|integer',
            'curated' => 'required|boolean',
            'dateOfAggregatePublication' => 'required|string',
            'excerpt' => 'required|string',
            'image' => 'required|string',
            'letterId' => 'nullable|integer',
            'originalUrl' => 'required|string',
            'siteName' => 'required|string',
            'title' => 'required|string',
        ];
    }

    public function convertToDto(): AggregateDto
    {
        return new AggregateDto(null, $this);
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getExcerpt(): string
    {
        return $this->excerpt;
    }

    public function getSiteName(): string
    {
        return $this->siteName;
    }

    public function getOriginalUrl(): string
    {
        return $this->originalUrl;
    }

    public function getDateOfAggregatePublication(): string
    {
        return $this->dateOfAggregatePublication;
    }

    public function getUniqueId(): string
    {
        return $this->uniqueId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function getCurated(): bool
    {
        return $this->curated;
    }

    public function getArchived(): bool
    {
        return $this->archived;
    }

    public function getLetterId(): ?int
    {
        return $this->letterId;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setExcerpt(string $excerpt): void
    {
        $this->excerpt = $excerpt;
    }

    public function setSiteName(string $siteName): void
    {
        $this->siteName = $siteName;
    }

    public function setOriginalUrl(string $originalUrl): void
    {
        $this->originalUrl = $originalUrl;
    }

    public function setDateOfAggregatePublication(string $dateOfAggregatePublication): void
    {
        $this->dateOfAggregatePublication = $dateOfAggregatePublication;
    }

    public function setUniqueId(string $uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function setCurated(bool $curated): void
    {
        $this->curated = $curated;
    }

    public function setArchived(bool $archived): void
    {
        $this->archived = $archived;
    }

    public function setLetterId(?int $letterId): void
    {
        $this->letterId = $letterId;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setDeletedAt(string $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }
}
