<?php

namespace App\Http\Middleware;

use App\Http\Services\DiscountCodeServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyDiscountCodeMiddleware
{
    private $discountCodeService;

    public function __construct(DiscountCodeServiceInterface $discountCodeService)
    {
        $this->discountCodeService = $discountCodeService;
    }

    public function handle(Request $request, Closure $next)
    {

        $route = $request->route();
        $routeParameters = $route[2];

        $channelId = $routeParameters['channelId'];
        $code = isset($routeParameters['discountCode']) ? $routeParameters['discountCode'] : null;
        $id = isset($routeParameters['discountCodeId']) ? $routeParameters['discountCodeId'] : null;

        /**
         * We need to be able to bypass this if nothing is provided.
         */
        if (empty($code) && empty($id)) {
            return $next($request);
        }

        $discountCode = (empty($id)) ?
            $this->discountCodeService->getDiscountCodeByCode($code) :
            $this->discountCodeService->getDiscountCodeById($id);

        if (empty($discountCode)) {
            return new Response('Are you sure this discount code exists?', 404);
        }

        if ((int)$channelId !== $discountCode->getChannelId()) {
            return new Response('No such discount code is associated with this channel.', 404);
        }

        $deletedAt = $discountCode->getDeletedAt();

        if (empty($deletedAt) === false) {
            return new Response('Are you sure this discount code exists?', 404);
        }

        $request->setRouteResolver(function () use ($discountCode, $route) {
            $route[2]['discountCodeObject'] = $discountCode;

            return $route;
        });

        return $next($request);
    }
}
