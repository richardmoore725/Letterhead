<?php

namespace App\Collections;

use App\DTOs\PlatformEventDto;
use App\Models\PlatformEvent;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class PlatformEventCollection extends BaseCollection
{
    public function __construct(Collection $platformEventDatabaseResults)
    {
        $dtos = $this->getDtos($platformEventDatabaseResults);
        $platformEvents = $this->getModels($dtos);

        parent::__construct($platformEvents);
    }

    private function getDtos(Collection $platformEventDatabaseResults): array
    {
        return array_map(function ($platformEventObject) {
            return new PlatformEventDto($platformEventObject);
        }, $platformEventDatabaseResults->toArray());
    }

    private function getModels(array $platformEventDtos): array
    {
        return array_map(function (platformEventDto $dto) {
            return new PlatformEvent($dto);
        }, $platformEventDtos);
    }
}
