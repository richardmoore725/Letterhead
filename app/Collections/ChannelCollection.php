<?php

namespace App\Collections;

use App\DTOs\ChannelDto;
use App\Models\Channel;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ChannelCollection extends BaseCollection
{
    /**
     * ChannelCollection constructor.
     * @param ChannelDto[]|Channel[] $items
     */
    public function __construct($arrayOfChannels = [])
    {
        parent::__construct($arrayOfChannels);
    }

    private function getDtos(array $arrayOfChannelObjects): array
    {
        return array_map(function ($channelObject) {
            if (is_a($channelObject, ChannelDto::class)) {
                return $channelObject;
            }

            return new ChannelDto($channelObject);
        }, $arrayOfChannelObjects);
    }

    public function getModels(array $channelDtosOrObjects): array
    {
        $dtos = $this->getDtos($channelDtosOrObjects);

        return array_map(function (ChannelDto $dto) {
            return new Channel($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $channelModels = $this->getModels($this->items);

        return array_map(function (Channel $channel) {
            return $channel->convertToArray();
        }, $channelModels);
    }
}
