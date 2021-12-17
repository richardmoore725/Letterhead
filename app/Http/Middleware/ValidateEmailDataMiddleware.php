<?php

namespace App\Http\Middleware;

use App\Http\Services\EmailServiceInterface;
use App\Models\Email;
use Closure;
use Illuminate\Http\Request;

class ValidateEmailDataMiddleware
{
    private $EmailService;

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

        $validator = app('validator')
            ->make($request->input(), Email::getValidationRules());

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        $brandId = $request->input('brandId');
        $channelId = $request->input('channelId');
        $content = $request->input('content');
        $description = $request->input('description');
        $fromEmail = $request->input('fromEmail');
        $fromName = $request->input('fromName');
        $name = $request->input('name');
        $subject = $request->input('subject');

        $request->setRouteResolver(function () use (
            $brandId,
            $channelId,
            $content,
            $description,
            $fromEmail,
            $fromName,
            $name,
            $subject,
            $route
        ) {
            $route[2]['brandId'] = $brandId;
            $route[2]['channelId'] = $channelId;
            $route[2]['content'] = $content;
            $route[2]['description'] = $description;
            $route[2]['fromEmail'] = $fromEmail;
            $route[2]['fromName'] = $fromName;
            $route[2]['name'] = $name;
            $route[2]['subject'] = $subject;

            return $route;
        });

        return $next($request);
    }
}
