<?php

namespace App\DTOs;

class ChannelSubscriptionTierDto
{
    public $channelId;
    public $createdAt;
    public $deletedAt;
    public $id;
    public $title;
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
            'created_at' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'id' => $this->id,
            'title' => $this->title,
            'updated_at' => $this->updatedAt,
        ];
    }
}
