<?php

namespace App\Collections;

use App\DTOs\MailChimpListDto;
use App\Models\MailChimpList;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ListCollection extends BaseCollection
{
    public function __construct($lists = [])
    {
        parent::__construct($lists);
    }

    private function getDtos(array $arrayOfListObjects): array
    {
        return array_map(function ($listObject) {
            if (is_a($listObject, MailChimpListDto::class)) {
                return $listObject;
            }

            return new MailChimpListDto($listObject);
        }, $arrayOfListObjects);
    }

    private function getModels(array $listDtosOrObjects): array
    {
        $dtos = $this->getDtos($listDtosOrObjects);

        return array_map(function (MailChimpListDto $dto) {
            return new MailChimpList($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $listModels = $this->getModels($this->items);

        return array_map(function (MailChimpList $list) {
            return $list->convertToArray();
        }, $listModels);
    }
}
