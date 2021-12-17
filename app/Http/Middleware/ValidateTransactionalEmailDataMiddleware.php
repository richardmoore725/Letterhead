<?php

namespace App\Http\Middleware;

use App\Http\Services\TransactionalEmailServiceInterface;
use App\Models\TransactionalEmail;
use Closure;
use Illuminate\Http\Request;

class ValidateTransactionalEmailDataMiddleware
{
    private $transactionalEmailService;

    public function __construct(TransactionalEmailServiceInterface $transationalEmailService)
    {
        $this->transationalEmailService = $transationalEmailService;
    }

    /**
     * Handle an incoming request
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];

        $validator = app('validator')
            ->make($request->input(), TransactionalEmail::getValidationRules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $brandId = $request->input('brandId');
        $channelId = $request->input('channelId');
        $description = $request->input('description');
        $emailId = $request->input('emailId');
        $eventId = $request->input('eventId');
        $isActive = $request->input('isActive');
        $name = $request->input('name');

        $request->setRouteResolver(function () use (
            $brandId,
            $channelId,
            $description,
            $emailId,
            $eventId,
            $isActive,
            $name,
            $route
        ) {
            $route[2]['brandId'] = $brandId;
            $route[2]['channelId'] = $channelId;
            $route[2]['description'] = $description;
            $route[2]['emailId'] = $emailId;
            $route[2]['eventId'] = $eventId;
            $route[2]['isActive'] = $isActive;
            $route[2]['name'] = $name;

            return $route;
        });

        return $next($request);
    }
}
