<?php

namespace App\Collections;

use App\DTOs\LetterDto;
use App\Models\Letter;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class LetterCollection extends BaseCollection
{
    public function __construct($letters = [])
    {
        parent::__construct($letters);
    }

    public function getModelArray()
    {
        return $this->getModels($this->items);
    }

    private function getDtos(array $dtos): array
    {
        return array_map(function ($object) {
            if (is_a($object, LetterDto::class)) {
                return $object;
            }

            return new LetterDto($object);
        }, $dtos);
    }

    private function getModels(array $dtosOrObjects): array
    {
        $dtos = $this->getDtos($dtosOrObjects);

        return array_map(function (LetterDto $dto) {
            return new Letter($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $models = $this->getModels($this->items);

        return array_map(function (Letter $letter) {
            return $letter->convertToArray();
        }, $models);
    }
}
