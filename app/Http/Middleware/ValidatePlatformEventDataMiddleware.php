<?php

namespace App\Http\Middleware;

use App\Http\Services\PlatformEventServiceInterface;
use App\Models\PlatformEvent;
use Closure;
use Illuminate\Http\Request;

class ValidatePlatformEventDataMiddleware
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

        $validator = app('validator')
            ->make($request->input(), PlatformEvent::getValidationRules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $description = $request->input('description');
        $eventSlug = $request->input('eventSlug');
        $name = $request->input('name');

        $request->setRouteResolver(function () use (
            $description,
            $eventSlug,
            $name,
            $route
        ) {
            $route[2]['description'] = $description;
            $route[2]['eventSlug'] = $eventSlug;
            $route[2]['name'] = $name;

            return $route;
        });

        return $next($request);
    }
}
