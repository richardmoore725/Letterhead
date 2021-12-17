<?php

namespace App\Http\Middleware;

use App\Http\Services\DiscountCodeServiceInterface;
use App\Models\DiscountCode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ValidateDiscountCodeDataMiddleware
{
    private $discountCodeServiceInterface;

    public function __construct(DiscountCodeServiceInterface $discountCodeServiceInterface)
    {
        $this->discountCodeServiceInterface = $discountCodeServiceInterface;
    }

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];

        $validator = app('validator')
            ->make($request->input(), DiscountCode::getValidationRules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $routeParameters = $route[2];

        $channelIdFromRoute = $routeParameters['channelId'];
        $channelId = $request->input('channelId');
        $displayName = $request->input('displayName');
        $discountCode = $request->input('discountCode');
        $discountValue = $request->input('discountValue');
        $isActive = $request->input('isActive');

        if ((int)$channelIdFromRoute !== (int)$channelId) {
            return new Response('This discount code is unavailable to that channel.', 403);
        }

        $request->setRouteResolver(function () use (
            $channelId,
            $displayName,
            $discountCode,
            $discountValue,
            $isActive,
            $route
        ) {
            $route[2]['channelId'] = $channelId;
            $route[2]['displayName'] = $displayName;
            $route[2]['discountCode'] = $discountCode;
            $route[2]['discountValue'] = $discountValue;
            $route[2]['isActive'] = $isActive;

            return $route;
        });

        return $next($request);
    }
}
