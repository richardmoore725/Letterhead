<?php

namespace App\Models;

use App\DTOs\ChannelSubscribersListDto;

class ChannelSubscribersList
{
    private $createdAt;
    private $deletedAt;
    private $description;
    private $id;
    private $name;
    private $updatedAt;
    private $uniqueId;

    public function __construct(ChannelSubscribersListDto $dto)
    {
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->description = $dto->description;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->updatedAt = $dto->updatedAt;
        $this->uniqueId = $dto->uniqueId;
    }

    public function convertToArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'description' => $this->description,
            'id' => $this->id,
            'name' => $this->name,
            'updatedAt' => $this->updatedAt,
            'uniqueId' => $this->uniqueId
        ];
    }
}
