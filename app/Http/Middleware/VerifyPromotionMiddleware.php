<?php

namespace App\Http\Middleware;

use App\Http\Services\AdServiceInterface;
use App\Models\Promotion;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyPromotionMiddleware
{
    private $adService;

    public function __construct(AdServiceInterface $adService)
    {
        $this->adService = $adService;
    }

    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();

        $routeParameters = $route[2];

        $adId = $routeParameters['adId'];
        $renderMjml = isset($routeParameters['mjml']) ? $routeParameters['mjml'] : '';

        $mjml = $renderMjml === 'true' ? true : false;

        $promotion = $this->adService->getPromotionByPromotionId($adId, $mjml);

        if (empty($promotion)) {
            return new Response('Are you sure this promotion exists?', 404);
        }

        $request->setRouteResolver(function () use ($promotion, $route) {
            $route[2]['promotion'] = $promotion;

            return $route;
        });

        return $next($request);
    }
}
