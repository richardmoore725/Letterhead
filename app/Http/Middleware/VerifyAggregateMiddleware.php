<?php

namespace App\Http\Middleware;

use App\Http\Services\AggregateServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyAggregateMiddleware
{
    private $aggregateService;

    public function __construct(AggregateServiceInterface $aggregateService)
    {
        $this->aggregateService = $aggregateService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];
        $aggregateId = $routeParameters['aggregateId'];

        $aggregate = $this->aggregateService->getAggregateById($aggregateId);

        if (empty($aggregate)) {
            return new Response('Woops. This aggregate does not exist', 404);
        }

        $request->setRouteResolver(function () use ($aggregate, $route) {
            $route[2]['aggregate'] = $aggregate;

            return $route;
        });

        return $next($request);
    }
}
