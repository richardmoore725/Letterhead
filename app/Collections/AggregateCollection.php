<?php

namespace App\Collections;

use App\DTOs\AggregateDto;
use App\Models\Aggregate;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class AggregateCollection extends BaseCollection
{
    /**
     * AggregateCollection constructor.
     * @param AggregateDto[]|Aggregate[] $arrayOfAggregates
     */
    public function __construct($arrayOfAggregates = [])
    {
        parent::__construct($arrayOfAggregates);
    }

    private function getDtos(array $arrayOfAggregateObjects): array
    {
        return array_map(function ($aggregateObject) {
            if (is_a($aggregateObject, AggregateDto::class)) {
                return $aggregateObject;
            }

            return new AggregateDto($aggregateObject);
        }, $arrayOfAggregateObjects);
    }

    public function getModels(array $aggregateDtosOrObjects): array
    {
        $dtos = $this->getDtos($aggregateDtosOrObjects);

        return array_map(function (AggregateDto $dto) {
            return new Aggregate(null, $dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $aggregateModels = $this->getModels($this->items);

        return array_map(function (Aggregate $aggregate) {
            return $aggregate->convertToArray();
        }, $aggregateModels);
    }
}
