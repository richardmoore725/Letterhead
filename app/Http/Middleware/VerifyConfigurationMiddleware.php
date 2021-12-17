<?php

namespace App\Http\Middleware;

use App\Http\Services\BrandServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyConfigurationMiddleware
{
    private $brandService;

    public function __construct(BrandServiceInterface $brandService)
    {
        $this->brandService = $brandService;
    }

    /**
     * Handle an incoming request. In the event that the channel exists, we will
     * append that configuration _to_ the request, so that it is available in controllers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];
        $configurationSlug = $routeParameters['configurationSlug'];

        $configuration = $this->brandService->getConfigurationBySlug($configurationSlug);

        if (empty($configuration)) {
            return new Response('This setting does not exist', 404);
        }

        $request->setRouteResolver(function () use ($configuration, $route) {
            $route[2]['configuration'] = $configuration;
            return $route;
        });

        return $next($request);
    }
}
