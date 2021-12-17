<?php

namespace App\Http\Middleware;

use App\Http\Services\LetterServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyLetterMiddleware
{
    private $letterService;

    public function __construct(LetterServiceInterface $letterService)
    {
        $this->letterService = $letterService;
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
        $letterId = $routeParameters['letterId'];

        $letter = $this->letterService->getLetterById($letterId);

        if (empty($letter)) {
            return new Response('Woops. This letter does not exist', 404);
        }

        $request->setRouteResolver(function () use ($letter, $route) {
            $route[2]['letter'] = $letter;

            return $route;
        });

        return $next($request);
    }
}
