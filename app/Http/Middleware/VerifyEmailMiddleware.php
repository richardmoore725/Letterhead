<?php

namespace App\Http\Middleware;

use App\Http\Services\EmailServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyEmailMiddleware
{
    private $emailService;

    public function __construct(EmailServiceInterface $emailService)
    {
        $this->emailService = $emailService;
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

        $email = (empty($id)) ? null : $this->emailService->getEmailById($id);

        if (empty($email)) {
            return new Response('Are you sure that email exists?', 404);
        }

        $request->setRouteResolver(function () use ($email, $route) {
            $route[2]['email'] = $email;

            return $route;
        });

        return $next($request);
    }
}
