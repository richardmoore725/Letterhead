<?php

namespace App\Http\Middleware;

use App\Http\Services\AggregateServiceInterface;
use App\Models\Aggregate;
use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;

class ValidateAggregateDateMiddleware
{
    private $aggregateService;

    public function __construct(AggregateServiceInterface $aggregateService)
    {
        $this->aggregateService = $aggregateService;
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

        /** @var $channel Channel|null*/
        $channel = isset($routeParameters['channel']) ? $routeParameters['channel'] : null;

        /**
         * We require that there is a Channel set in the route parameters, which is
         * performed by VerifyChannelMiddleware.
         *
         * @uses VerifyChannelMiddleware::handle()
         */
        if (empty($channel)) {
            return response()->json('This middleware requires that VerifyChannelMiddleware is called first.', 500);
        }

        /**
         * Validate the raw POST request for the things we require.
         */
        $validationRules = Aggregate::getValidationRules();
        $validator = app('validator')->make($request->input(), $validationRules);

        if ($validator->fails()) {
            return response()->json($validator->errors()->all(), 400);
        }

        /**
         * We'll pull the properties we need from the Request object.
         */

        $archived = $request->input('archived');
        //$channelId = $channel->getId();
        $channelId = $request->input('channelId');
        $curated = $request->input('curated');
        $dateOfAggregatePublication = $request->input('dateOfAggregatePublication');
        $excerpt = $request->input('excerpt');
        $image = $request->input('image');
        $letterId = $request->input('letterId');
        $originalUrl = $request->input('originalUrl');
        $title = $request->input('title');
        $siteName = $request->input('siteName');

        $request->setRouteResolver(function () use (
            $archived,
            $channelId,
            $curated,
            $dateOfAggregatePublication,
            $excerpt,
            $image,
            $letterId,
            $originalUrl,
            $title,
            $siteName,
            $route
        ) {
            $route[2]['archived'] = $archived;
            $route[2]['channelId'] = $channelId;
            $route[2]['curated'] = $curated;
            $route[2]['dateOfAggregatePublication'] = $dateOfAggregatePublication;
            $route[2]['excerpt'] = $excerpt;
            $route[2]['image'] = $image;
            $route[2]['letterId'] = $letterId;
            $route[2]['originalUrl'] = $originalUrl;
            $route[2]['title'] = $title;
            $route[2]['siteName'] = $siteName;

            return $route;
        });

        return $next($request);
    }
}
