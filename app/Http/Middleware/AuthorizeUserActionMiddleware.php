<?php

namespace App\Http\Middleware;

use App\Http\Services\AuthServiceInterface;
use App\Http\Services\UserServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorizeUserActionMiddleware
{
    private $authService;
    private $userService;

    public function __construct(AuthServiceInterface $authService, UserServiceInterface $userService)
    {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function handle(
        Request $request,
        Closure $next,
        string $model,
        string $action,
        string $resourceIdType
    ) {
        $token = $request->bearerToken();

        if (!$request->headers->has('Origin')) {
            $response = new Response('No origin detected', 400);
            return $response;
        }

        if (empty($token)) {
            return new Response('Oops. Remember to send your passport as a bearer token.', 400);
        }

        $origin = $request->headers->get('origin');
        $passportStamp = $this->authService->authenticatePassport($origin, $request->bearerToken());

        if (empty($passportStamp)) {
            return new Response('Your passport may have expired.', 401);
        }

        $route = $request->route();
        $routeParameters = $route[2];
        $resourceId = (int) $routeParameters[$resourceIdType];

        $canUserPerformAction = $this->userService->checkWhetherUserCanPerformAction(
            $action,
            $model,
            $passportStamp,
            $resourceId,
        );

        if (!$canUserPerformAction) {
            return new Response("You are not authorized to ${action} at ${model}: ${resourceId}.", 401);
        }

        $request->setRouteResolver(function () use ($passportStamp, $route) {
            $routeParameters['passport'] = $passportStamp;
            return $route;
        });

        $request->request->set('passportStamp', $passportStamp);

        return $next($request);
    }
}
