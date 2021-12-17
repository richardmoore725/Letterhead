<?php

namespace App\Collections;

use App\DTOs\LetterPartDto;
use App\DTOs\LettersEmailsDto;
use App\Models\LetterPart;
use App\Models\LettersEmails;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class LettersEmailsCollection extends BaseCollection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    private function getDtos(array $dtos): array
    {
        return array_map(function ($object) {
            if (is_a($object, LettersEmailsDto::class)) {
                return $object;
            }

            return new LettersEmailsDto($object);
        }, $dtos);
    }

    private function getModels(array $dtosOrObjects): array
    {
        $dtos = $this->getDtos($dtosOrObjects);

        return array_map(function (LettersEmailsDto $dto) {
            return new LettersEmails($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $models = $this->getModels($this->items);

        return array_map(function (LettersEmails $lettersEmails) {
            return $lettersEmails->convertToArray();
        }, $models);
    }
}
