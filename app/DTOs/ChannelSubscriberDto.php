<?php

namespace App\DTOs;

class ChannelSubscriberDto
{
    public $channelId;
    /**
     * This will come from a join with channel_subscriptions table.
     */
    public $channelSubscriberStatus;
    public $createdAt;
    public $deletedAt;
    public $email;
    public $id;
    public $name;
    public $updatedAt;
    public $userId;

    public function __construct(\stdClass $object = null)
    {
        $this->channelId = isset($object->channelId) ? $object->channelId : null;
        $this->channelSubscriberStatus = isset($object->status) ? $object->status : 0;
        $this->createdAt = isset($object->created_at) ? $object->created_at : '';
        $this->deletedAt = isset($object->deletedAt) ? $object->deletedAt : null;
        $this->email = isset($object->email) ? $object->email : '';
        $this->id = isset($object->id) ? $object->id : null;
        $this->name = isset($object->name) ? $object->name : '';
        $this->updatedAt = isset($object->updated_at) ? $object->updated_at : '';
        $this->userId = isset($object->userId) ? $object->userId : null;
    }

    public function mapDtoPropertiesToColumns(): array
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
            'userId' => $this->userId,
        ];
    }
}
