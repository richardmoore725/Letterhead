<?php

namespace App\Collections;

use App\DTOs\ChannelSubscribersListDto;
use App\Models\ChannelSubscribersList;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ChannelSubscribersListCollection extends BaseCollection
{
    public function __construct($list = [])
    {
        parent::__construct($list);
    }

    private function getDtos(array $arrayOfListObjects): array
    {
        return array_map(function ($listObject) {
            if (is_a($listObject, ChannelSubscribersListDto::class)) {
                return $listObject;
            }

            if (is_a($listObject, ChannelSubscribersList::class)) {
                return new ChannelSubscribersListDto(null, $listObject);
            }

            return new ChannelSubscribersListDto($listObject);
        }, $arrayOfListObjects);
    }

    private function getModels(array $listDtosOrObjects): array
    {
        $dtos = $this->getDtos($listDtosOrObjects);

        return array_map(function (ChannelSubscribersListDto $dto) {
            return new ChannelSubscribersList($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $listModels = $this->getModels($this->items);

        return array_map(function (ChannelSubscribersList $list) {
            return $list->convertToArray();
        }, $listModels);
    }
}
