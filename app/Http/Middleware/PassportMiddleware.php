<?php

namespace App\Http\Middleware;

use App\Http\Services\AuthServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PassportMiddleware
{
    private $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    public function handle(Request $request, Closure $next)
    {
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

        $request->setRouteResolver(function () use ($passportStamp, $route) {
            $route[2]['passport'] = $passportStamp;
            return $route;
        });

        $request->request->set('passportStamp', $passportStamp);

        return $next($request);
    }
}
