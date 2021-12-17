<?php

namespace App\DTOs;

/**
 * @package App\DTOs
 */
class PlatformEventDto
{
    public $createdAt;
    public $description;
    public $deletedAt;
    public $eventSlug;
    public $id;
    public $name;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->description = isset($object->description) ? $object->description : '';
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : '';
        $this->eventSlug = isset($object->eventSlug) ? $object->eventSlug : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->name = isset($object->name) ? $object->name : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    public function mapPlatformEventDtoToDatabaseColumns(): array
    {
        return [
            'created_at' => $this->createdAt,
            'description' => $this->description,
            'deleted_at' => $this->deletedAt,
            'eventSlug' => $this->eventSlug,
            'id' => $this->id,
            'name' => $this->name,
            'updated_at' => $this->updatedAt,
        ];
    }
}
