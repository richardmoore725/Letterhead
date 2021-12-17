<?php

namespace App\Models;

use App\DTOs\ChannelSubscriberDto;

class ChannelSubscriber
{
    private $channelId;
    /**
     * This will come from a join with channel_subscriptions table.
     */
    private $channelSubscriberStatus;
    private $createdAt;
    private $deletedAt;
    private $email;
    private $id;
    private $name;
    private $updatedAt;
    private $userId;

    public function __construct(ChannelSubscriberDto $dto)
    {
        $this->channelId = $dto->channelId;
        $this->channelSubscriberStatus = $dto->channelSubscriberStatus;
        $this->createdAt = $dto->createdAt;
        $this->deletedAt = $dto->deletedAt;
        $this->email = $dto->email;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->updatedAt = $dto->updatedAt;
        $this->userId = $dto->userId;
    }

    public function convertToArray(): array
    {
        return [
            'channelId' => $this->channelId,
            'channelSubscriberStatus' => $this->channelSubscriberStatus,
            'createdAt' => $this->createdAt,
            'deletedAt' => $this->deletedAt,
            'email' => $this->email,
            'id' => $this->id,
            'name' => $this->name,
            'updatedAt' => $this->updatedAt,
            'userId' => $this->userId
        ];
    }
}
