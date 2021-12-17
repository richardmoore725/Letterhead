<?php

namespace App\Collections;

use App\DTOs\MessageDto;
use App\Models\Message;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class MessageCollection extends BaseCollection
{
    /**
     * AggregateCollection constructor.
     * @param MessageDto[]|Message[] $arrayOfMessages
     */
    public function __construct($arrayOfMessages = [])
    {
        parent::__construct($arrayOfMessages);
    }

    private function getDtos(array $arrayOfMessageObjects): array
    {
        return array_map(function ($messageObject) {
            if (is_a($messageObject, MessageDto::class)) {
                return $messageObject;
            }

            return new MessageDto($messageObject);
        }, $arrayOfMessageObjects);
    }

    public function getModels(array $messageDtosOrObjects): array
    {
        $dtos = $this->getDtos($messageDtosOrObjects);

        return array_map(function (MessageDto $dto) {
            return new Message($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $messageModels = $this->getModels($this->items);

        return array_map(function (Message $message) {
            return $message->convertToArray();
        }, $messageModels);
    }
}
