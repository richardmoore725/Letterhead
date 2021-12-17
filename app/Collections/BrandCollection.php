<?php

namespace App\Collections;

use App\DTOs\BrandDto;
use App\Models\Brand;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class BrandCollection extends BaseCollection
{
    /**
     * BrandCollection constructor.
     * @param BrandDto[]|Brand[] $items
     */
    public function __construct($arrayOfBrands = [])
    {
        parent::__construct($arrayOfBrands);
    }

    private function getDtos(array $arrayOfBrandObjects): array
    {
        return array_map(function ($brandObject) {
            if (is_a($brandObject, BrandDto::class)) {
                return $brandObject;
            }

            return new BrandDto($brandObject);
        }, $arrayOfBrandObjects);
    }

    private function getModels(array $brandDtosOrObjects): array
    {
        $dtos = $this->getDtos($brandDtosOrObjects);

        return array_map(function (BrandDto $dto) {
            return new Brand($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $brandModels = $this->getModels($this->items);

        return array_map(function (Brand $brand) {
            return $brand->convertToArray();
        }, $brandModels);
    }
}
