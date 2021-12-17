<?php

namespace App\Collections;

use App\DTOs\ChannelSubscriberDto;
use App\Models\ChannelSubscriber;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ChannelSubscriberCollection extends BaseCollection
{
    public function __construct($subscribers = [])
    {
        parent::__construct($subscribers);
    }

    private function getDtos(array $arrayOfSubscriberObjects): array
    {
        return array_map(function ($subscriberObject) {
            if (is_a($subscriberObject, ChannelSubscriberDto::class)) {
                return $subscriberObject;
            }

            if (is_a($subscriberObject, ChannelSubscriber::class)) {
                return new ChannelSubscriberDto(null, $subscriberObject);
            }

            return new ChannelSubscriberDto($subscriberObject);
        }, $arrayOfSubscriberObjects);
    }

    private function getModels(array $subscriberDtosOrObjects): array
    {
        $dtos = $this->getDtos($subscriberDtosOrObjects);

        return array_map(function (ChannelSubscriberDto $dto) {
            return new ChannelSubscriber($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $subscriberModels = $this->getModels($this->items);

        return array_map(function (ChannelSubscriber $subscriber) {
            return $subscriber->convertToArray();
        }, $subscriberModels);
    }
}
