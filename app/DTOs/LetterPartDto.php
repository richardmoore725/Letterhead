<?php

namespace App\DTOs;

use App\Models\LetterPart;

/**
 * @package App\DTOs
 */
class LetterPartDto
{
    public $id;
    public $copy;
    public $createdAt;
    public $deletedAt;
    public $heading;
    public $letterId;
    public $updatedAt;

    public function __construct(\stdClass $object = null, LetterPart $letterPart = null)
    {
        if (!empty($object)) {
            $this->id = $object->id;
            $this->copy = $object->copy;
            $this->createdAt = $object->created_at;
            $this->deletedAt = $object->deleted_at;
            $this->heading = $object->heading;
            $this->letterId = $object->letterId;
            $this->updatedAt = $object->updated_at;
        }

        if (!empty($letterPart)) {
            $this->id = $letterPart->getId();
            $this->copy = $letterPart->getCopy();
            $this->createdAt = $letterPart->getCreatedAt();
            $this->deletedAt = $letterPart->getDeletedAt();
            $this->heading = $letterPart->getHeading();
            $this->letterId = $letterPart->getLetterId();
            $this->updatedAt = $letterPart->getUpdatedAt();
        }
    }

    public function mapChannelDtoToDatabaseColumns(): array
    {
        return [
            'id' => $this->id,
            'copy' => $this->copy,
            'created_at' => $this->createdAt,
            'deleted_at' => $this->deletedAt,
            'heading' => $this->heading,
            'letterId' => $this->letterId,
            'updated_at' => $this->updatedAt,
        ];
    }
}
