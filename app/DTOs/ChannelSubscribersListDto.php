<?php

namespace App\DTOs;

class ChannelSubscribersListDto
{
    public $createdAt;
    public $deletedAt;
    public $description;
    public $id;
    public $name;
    public $updatedAt;
    /**
     * This uniqueId will be generated automatically as a random alphanumic string.
     * This is required in SparkPost Api calls.
     */
    public $uniqueId;

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
            'created_at' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'description' => $this->description,
            'id' => $this->id,
            'name' => $this->name,
            'updated_at' => $this->updatedAt,
            'uniqueId' => $this->uniqueId,
        ];
    }
}
