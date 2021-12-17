<?php

namespace App\Http\Middleware;

use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;
use Closure;
use DrewM\MailChimp\MailChimp;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The role of this middleware is to verify that the channel actually exists
 * upon request.
 *
 * Class VerifyChannelMiddleware
 * @package App\Http\Middleware
 */
class VerifyChannelMiddleware
{
    private $brandService;
    private $channelService;

    public function __construct(BrandServiceInterface $brandService, ChannelServiceInterface $channelService)
    {
        $this->brandService = $brandService;
        $this->channelService = $channelService;
    }

    /**
     * Handle an incoming request. In the event that the channel exists, we will
     * append that channel _to_ the request, so that it is available in controllers.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $route = $request->route();
        $routeParameters = $route[2];
        $brand = isset($routeParameters['brand']) ? $routeParameters['brand'] : null;
        $channelId = isset($routeParameters['channelId']) ? $routeParameters['channelId'] : null;
        $channelSlug = isset($routeParameters['channelSlug']) ? $routeParameters['channelSlug'] : null;

        $channel = (empty($channelId)) ?
            $this->channelService->getChannelBySlug($channelSlug) :
            $this->channelService->getChannelById($channelId);

        /**
         * If the channel doesn't exist, return a 404.
         */
        if (empty($channel)) {
            return new Response('Woops. This channel doesn\'t exist.', 400);
        }

        if (empty($brand)) {
            $brand = $this->brandService->getBrandById($channel->getBrandId());

            /**
             * In the event that the brand also doesn't exist by the ID given, then we'll say so.
             */
            if (empty($brand)) {
                return new Response('Ouch! This brand doesn\'t exist.', 400);
            }
        }

        if ($brand->getId() !== $channel->getBrandId()) {
            return new Response('Alas. The channel doesn\'t belong to this brand.', 400);
        }

        $request->setRouteResolver(function () use ($brand, $channel, $route) {
            $route[2]['brand'] = $brand;
            $route[2]['channel'] = $channel;

            return $route;
        });

        return $next($request);
    }
}
