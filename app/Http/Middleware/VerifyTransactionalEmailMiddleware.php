<?php

namespace App\Http\Middleware;

use App\Http\Services\TransactionalEmailServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyTransactionalEmailMiddleware
{
    private $transactionalEmailService;

    public function __construct(TransactionalEmailServiceInterface $transactionalEmailService)
    {
        $this->transactionalEmailService = $transactionalEmailService;
    }

  /**
   * Handle an incoming request.
   *
   * @param \Illuminate\Http\Request $request
   * @param \Closure $next
   * @return mixed
   */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];

        $id = isset($routeParameters['id']) ? $routeParameters['id'] : null;

        $transactionalEmail = (empty($id)) ? null : $this->transactionalEmailService->getTransactionalEmailById($id);

        if (empty($transactionalEmail)) {
            return new Response('Are you sure that transactional email exists?', 404);
        }

        $request->setRouteResolver(function () use ($transactionalEmail, $route) {
            $route[2]['transactionalEmail'] = $transactionalEmail;

            return $route;
        });

        return $next($request);
    }
}
