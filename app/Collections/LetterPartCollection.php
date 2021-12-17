<?php

namespace App\Collections;

use App\DTOs\LetterPartDto;
use App\Models\LetterPart;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class LetterPartCollection extends BaseCollection
{
    public function __construct($lists = [])
    {
        parent::__construct($lists);
    }

    public function getLetterPartsMappedToDatabaseColumns(): array
    {
        $dtos = $this->getDtos($this->items);

        return array_map(function (LetterPartDto $dto) {
            return $dto->mapChannelDtoToDatabaseColumns();
        }, $dtos);
    }

    private function getDtos(array $arrayOfLetterPartDtos): array
    {
        return array_map(function ($letterPartObject) {
            if (is_a($letterPartObject, LetterPartDto::class)) {
                return $letterPartObject;
            }

            return new LetterPartDto($letterPartObject);
        }, $arrayOfLetterPartDtos);
    }

    private function getModels(array $letterPartDtosOrObjects): array
    {
        $dtos = $this->getDtos($letterPartDtosOrObjects);

        return array_map(function (LetterPartDto $dto) {
            return new LetterPart($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $letterPartModels = $this->getModels($this->items);

        return array_map(function (LetterPart $letter) {
            return $letter->convertToArray();
        }, $letterPartModels);
    }

    public function getArrayOfModels(): array
    {
        return  $this->getModels($this->items);
    }
}
