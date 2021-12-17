<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServicePlatformKeyMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return new Response('Oops. Remember to send your service platform key as a bearer token.', 400);
        }

        if ($token !== env('SERVICE_PLATFORM_KEY')) {
            return new Response('Your service platform key is unauthorized.', 403);
        }

        $response = $next($request);

        return $response;
    }
}
