<?php

namespace App\Http\Middleware;

use App\Models\PromotionOrder;
use Closure;
use Illuminate\Http\Request;

class VerifyPromotionOrderMiddleware
{
    public function __construct()
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        $validator = app('validator')
            ->make(
                $request->input(),
                PromotionOrder::getValidationRules()
            );

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $amount = $request->input('amount');
        $dateStart = $request->input('dateStart');
        $discountCode = $request->input('discountCode');
        $originalPurchasePrice = $request->input('originalPurchasePrice');
        $paymentMethod = $request->input('paymentMethod');
        $promotionTypeId = $request->input('promotionTypeId');
        $userEmail = $request->input('userEmail');
        $userName = $request->input('userName');

        $request->setRouteResolver(function () use (
            $amount,
            $dateStart,
            $discountCode,
            $originalPurchasePrice,
            $paymentMethod,
            $promotionTypeId,
            $userEmail,
            $userName,
            $route
        ) {
            $route[2]['amount'] = $amount;
            $route[2]['dateStart'] = $dateStart;
            $route[2]['discountCode'] = $discountCode;
            $route[2]['originalPurchasePrice'] = $originalPurchasePrice;
            $route[2]['paymentMethod'] = $paymentMethod;
            $route[2]['promotionTypeId'] = $promotionTypeId;
            $route[2]['userEmail'] = $userEmail;
            $route[2]['userName'] = $userName;

            return $route;
        });

        return $next($request);
    }
}
