<?php

namespace App\Collections;

use App\DTOs\PromotionDto;
use App\Models\Promotion;
use Illuminate\Database\Eloquent\Collection as BaseCollection;

class PromotionCollection extends BaseCollection
{
    /**
     * PromotionCollection constructor.
     * @param PromotionDto[]|Promotion[] $arrayOfPromotions
     */
    public function __construct($arrayOfPromotions = [])
    {
        parent::__construct($arrayOfPromotions);
    }

    private function getDtos(array $arrayOfPromotionObjects): array
    {
        return array_map(function ($promotionObject) {
            if (is_a($promotionObject, PromotionDto::class)) {
                return $promotionObject;
            }

            return new PromotionDto(null, $promotionObject);
        }, $arrayOfPromotionObjects);
    }

    public function getModels(array $promotionDtosOrObjects = []): array
    {
        $dtosOrObjects = empty($promotionDtosOrObjects) ? $this->items : $promotionDtosOrObjects;
        $dtos = $this->getDtos($dtosOrObjects);

        return array_map(function (PromotionDto $dto) {
            return new Promotion($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $promotionModels = $this->getModels($this->items);

        return array_map(function (Promotion $promotion) {
            return $promotion->convertToArray();
        }, $promotionModels);
    }
}
