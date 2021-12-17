<?php

namespace App\Collections;

use App\DTOs\LettersUsersDto;
use App\Models\LettersUsers;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class LettersUsersCollection extends BaseCollection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    public function getLettersUsersMappedToDatabaseColumns(): array
    {
        $dtos = $this->getDtos($this->items);

        return array_map(function (LettersUsersDto $dto) {
            return $dto->mapChannelDtoToDatabaseColumns();
        }, $dtos);
    }

    private function getDtos(array $dtos): array
    {
        return array_map(function ($object) {
            if (is_a($object, LettersUsersDto::class)) {
                return $object;
            }

            return new LettersUsersDto($object);
        }, $dtos);
    }

    private function getModels(array $dtosOrObjects): array
    {
        $dtos = $this->getDtos($dtosOrObjects);

        return array_map(function (LettersUsersDto $dto) {
            return new LettersUsers($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $models = $this->getModels($this->items);

        return array_map(function (LettersUsers $lettersUsers) {
            return $lettersUsers->convertToArray();
        }, $models);
    }

    public function getArrayOfUserIds()
    {
        $models = $this->getModels($this->items);

        $arrayOfUserIds = array_map(function (LettersUsers $model) {
            return $model->getUserId();
        }, $models);

        return collect($arrayOfUserIds);
    }
}
