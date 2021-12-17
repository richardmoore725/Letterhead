<?php

namespace App\Http\Middleware;

use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The ApiKeyMiddleware can be used to determine the validity of an API request
 * based off the key that's passed, and suss out the first corresponding Channel
 * that key has access to. Unless the middleware rejects the request, it will pass a
 * $channel on to the next middleware or controller.
 *
 * Class ApiKeyMiddleware
 * @package App\Http\Middleware
 */
class ApiKeyMiddleware
{
    private $channelService;

    public function __construct(ChannelServiceInterface $channelService)
    {
        $this->channelService = $channelService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            return new Response('Looks like you\'re missing your key.', 400);
        }

        $response = $this->channelService->getChannelByBrandApiKey($token);

        if ($response->isError()) {
            return new Response($response->getEndUserMessage(), $response->getStatus());
        }

        /**
         * @var Channel
         */
        $channel = $response->getData();
        $route = $request->route();
        $routeParameters = $route[2];
        $request->setRouteResolver(function () use ($channel, $route) {
            $route[2]['channel'] = $channel;

            return $route;
        });

        return $next($request);
    }
}
