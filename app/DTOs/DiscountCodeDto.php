<?php

namespace App\DTOs;

class DiscountCodeDto
{
    public $channelId;
    public $createdAt;
    public $deletedAt;
    public $discountCode;
    public $discountValue;
    public $displayName;
    public $id;
    public $isActive;
    public $updatedAt;

    public function __construct(\stdClass $object = null)
    {
        $this->channelId = isset($object->channelId) ? $object->channelId : 0;
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->deletedAt = isset($object->deleted_at) ? $object->deleted_at : null;
        $this->discountCode = isset($object->discountCode) ? $object->discountCode : '';
        $this->discountValue = isset($object->discountValue) ? $object->discountValue : 0;
        $this->displayName = isset($object->displayName) ? $object->displayName : '';
        $this->id = isset($object->id) ? $object->id : 0;
        $this->isActive = isset($object->isActive) ? (bool) $object->isActive : false;
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
    }

    public function mapDiscountCodeDtoToDatabaseColumns(): array
    {
        return [
            'channelId' => $this->channelId,
            'created_at' => $this->createdAt,
            'deleted_at' => $this->deletedAt,
            'discountCode' => $this->discountCode,
            'discountValue' => $this->discountValue,
            'displayName' => $this->displayName,
            'id' => $this->id,
            'isActive' => (int) $this->isActive,
            'updated_at' => $this->updatedAt,
        ];
    }
}
