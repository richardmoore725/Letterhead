<?php

namespace App\Http\Middleware;

use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyBrandMiddleware
{
    private $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Handle an incoming request. In the event that the channel exists, we will
     * append that channel _to_ the request, so that it is available in controllers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];
        $brandId = (int) $routeParameters['brandId'];

        $brand = $this->brandService->getBrandById($brandId);

        if (empty($brand)) {
            return new Response('Are you sure that brand exists?', 404);
        }

        $request->setRouteResolver(function () use ($brand, $route) {
            $route[2]['brand'] = $brand;
            return $route;
        });

        return $next($request);
    }
}
