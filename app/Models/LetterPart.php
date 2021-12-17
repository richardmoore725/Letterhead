<?php

namespace App\Models;

use App\DTOs\LetterPartDto;

class LetterPart
{
    /**
     * The content :). It may include some html.
     * @var string
     */
    private $copy;

    private $createdAt;

    private $deletedAt;

    /**
     * The optional heading of this piece of content,
     * @var string
     */
    private $heading;

    /**
     * The ID of this LetterPart.
     * @var int
     */
    private $id;

    /**
     * The ID of the Letter this part belongs to.
     * @var int
     */
    private $letterId;

    private $updatedAt;

    public function __construct(LetterPartDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->copy = $dto->copy;
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->heading = $dto->heading;
        $this->id = $dto->id;
        $this->letterId = $dto->letterId;
        $this->updatedAt = $dto->updatedAt;
    }

    public function convertToArray(): array
    {
        return [
            'copy' => $this->getCopy(),
            'createdAt' => $this->getCreatedAt(),
            'heading' => $this->getHeading(),
            'id' => $this->getId(),
            'updatedAt' => $this->getUpdatedAt(),
        ];
    }

    public function getCopy(): string
    {
        return $this->copy;
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }

    public function getDeletedAt(): ?string
    {
        return $this->deletedAt;
    }

    public function getHeading(): string
    {
        return $this->heading;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLetterId(): int
    {
        return $this->letterId;
    }

    public function getUpdatedAt(): string
    {
        return $this->updatedAt;
    }

    public function setCreatedAt(string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setCopy(string $copy): void
    {
        $this->copy = $copy;
    }

    public function setHeading(string $heading): void
    {
        $this->heading = $heading;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setLetterId(int $letterId): void
    {
        $this->letterId = $letterId;
    }

    public function setUpdatedAt(string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
