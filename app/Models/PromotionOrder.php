<?php

namespace App\Models;

class PromotionOrder
{
    public static function getValidationRules(): array
    {
        return [
            "amount" => 'required|int',
            "dateStart" => 'required|string',
            "originalPurchasePrice" => 'required|int',
            "paymentMethod" => 'required|string',
            "promotionTypeId" => 'required|int',
            "userEmail" => 'required|string',
            "userName" => 'required|string'
        ];
    }
}
