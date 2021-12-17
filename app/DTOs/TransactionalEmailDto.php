<?php

namespace App\DTOs;

/**
 * @package App\DTOS
 */
class TransactionalEmailDto
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

    public function __construct(\stdClass $object = null)
    {
        $this->brandId = isset($object->brandId) ? $object->brandId : null;
        $this->channelId = isset($object->channelId) ? $object->channelId : null;
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : '';
        $this->description = isset($object->description) ? $object->description : '';
        $this->emailId = isset($object->emailId) ? $object->emailId : null;
        $this->eventId = isset($object->eventId) ? $object->eventId : null;
        $this->id = isset($object->id) ? $object->id : null;
        $this->isActive = isset($object->isActive) ? (bool) $object->isActive : true;
        $this->name = isset($object->name) ? $object->name : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    public function mapTransactionalEmailDtoToDatabaseColumns(): array
    {
        return [
        'brandId' => $this->brandId,
        'channelId' => $this->channelId,
        'created_at' => $this->createdAt,
        'deleted_at' => $this->deletedAt,
        'description' => $this->description,
        'emailId' => $this->emailId,
        'eventId' => $this->eventId,
        'id' => $this->id,
        'isActive' => (int) $this->isActive,
        'name' => $this->name,
        'updated_at' => $this->updatedAt,
        ];
    }
}
