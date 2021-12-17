<?php

namespace App\Http\Middleware;

use App\Http\Services\UserServiceInterface;
use App\Models\PassportStamp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class AuthorizeUserMiddleware
 * @package App\Http\Middleware
 * @uses PassportMiddleware::handle()
 */
class AuthorizeUserMiddleware
{
    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * AuthorizeUserMiddleware constructor.
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
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

        /**
         * @var PassportStamp
         */
        $passport = isset($routeParameters['passport']) ? $routeParameters['passport'] : null;

        /**
         * The passport needs to exist before this middleware can be called. In the event it
         * doesn't, let's make sure that the middleware are called in the right order.
         */
        if (empty($passport)) {
            /**
             * No user response makes sense, but we should ensure that ultimately the
             * consuming app gets a 500 response.
             */
            return new Response('', 500);
        }

        $permissions = $this->userService->getPermissionsByUserId($passport);

        $request->setRouteResolver(function () use ($permissions, $route) {
            $route[2]['permissions'] = $permissions;
            return $route;
        });

        return $next($request);
    }
}
