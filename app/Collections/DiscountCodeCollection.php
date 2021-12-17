<?php

namespace App\Collections;

use App\DTOs\DiscountCodeDto;
use App\Models\DiscountCode;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class DiscountCodeCollection extends BaseCollection
{
    public function __construct($discountCodeDatabaseResults = [])
    {
        $dtos = $this->getDtos($discountCodeDatabaseResults);

        $discountCodes = $this->getModels($dtos);

        parent::__construct($discountCodes);
    }

    private function getDtos(array $discountCodeDatabaseResults): array
    {
        return array_map(function ($discountCodeObject) {
            return new DiscountCodeDto($discountCodeObject);
        }, $discountCodeDatabaseResults);
    }

    private function getModels(array $discountCodeDtos): array
    {
        return array_map(function ($discountCodeObject) {
            if (is_a($discountCodeObject, DiscountCode::class)) {
                return $discountCodeObject;
            }

            return new DiscountCode($discountCodeObject);
        }, $discountCodeDtos);
    }

    public function getPublicArrays(): array
    {
        $discountCodeModels = $this->getModels($this->items);

        return array_map(function (DiscountCode $discountCode) {
            return $discountCode->convertToArray();
        }, $discountCodeModels);
    }
}
