<?php

namespace App\Collections;

use App\DTOs\SegmentDto;
use App\Models\Segment;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class SegmentCollection extends BaseCollection
{
    public function __construct($segments = [])
    {
        parent::__construct($segments);
    }

    private function getDtos(array $arrayOfSegmentObjects): array
    {
        return array_map(function ($segmentObject) {
            if (is_a($segmentObject, SegmentDto::class)) {
                return $segmentObject;
            }

            return new SegmentDto($segmentObject);
        }, $arrayOfSegmentObjects);
    }

    private function getModels(array $segmentDtosOrObjects): array
    {
        $dtos = $this->getDtos($segmentDtosOrObjects);

        return array_map(function (SegmentDto $dto) {
            return new Segment($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $segmentModels = $this->getModels($this->items);

        return array_map(function (Segment $segment) {
            return $segment->convertToArray();
        }, $segmentModels);
    }
}
