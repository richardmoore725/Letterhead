<?php

namespace App\Http\Middleware;

use App\Http\Services\PlatformEventServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyPlatformEventMiddleware
{
    private $platformEventService;

    public function __construct(PlatformEventServiceInterface $platformEventService)
    {
        $this->platformEventService = $platformEventService;
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

        $id = isset($routeParameters['id']) ? $routeParameters['id'] : null;

        $platformEvent = (empty($id)) ? null : $this->platformEventService->getPlatformEventById($id);

        if (empty($platformEvent)) {
            return new Response('Are you sure that platform event exists?', 404);
        }

        $request->setRouteResolver(function () use ($platformEvent, $route) {
            $route[2]['platformEvent'] = $platformEvent;

            return $route;
        });

        return $next($request);
    }
}
