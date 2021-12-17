<?php

namespace App\DTOs;

class ChannelSubscriptionDto
{
    public $channelId;
    public $channelSubscriberId;
    public $createdAt;
    public $deletedAt;
    public $id;
    public $status;
    public $tier;
    public $updatedAt;

    public function __construct(object $object = null)
    {
        if (empty($object)) {
            return;
        }

        $this->setDtoPropertiesForNotNullColumns($this->mapDtoPropertiesToColumns(), $object);
    }

    private function setDtoPropertiesForNotNullColumns(array $arrayOfDtoColumns, object $databaseObject)
    {
        foreach ($arrayOfDtoColumns as $column => $value) {
            if (!isset($this->$column) || !isset($databaseObject->$column)) {
                return;
            }

            $this->$column = $databaseObject->$column->$value;
        }
    }

    private function mapDtoPropertiesToColumns(): array
    {
        return [
            'channelId' => $this->channelId,
            'channelSubscriberId' => $this->channelSubscriberId,
            'created_at' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'id' => $this->id,
            'status' => $this->status,
            'tier' => $this->tier,
            'updated_at' => $this->updatedAt,
        ];
    }
}
