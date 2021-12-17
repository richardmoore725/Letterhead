<?php

namespace App\Http\Middleware;

use App\Http\Services\MailChimpFacade;
use App\Http\Services\ChannelServiceInterface;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Channel;
use Carbon\CarbonImmutable;

class VerifyMailChimpMiddleware
{
    private $channelService;

    public function __construct(ChannelServiceInterface $channelService)
    {
        $this->channelService = $channelService;
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
        $channel = isset($routeParameters['channel']) ? $routeParameters['channel'] : null;

        if (empty($channel)) {
            return new Response('Woops. We are trying to connect to MailChimp without a channel.', 500);
        }

        $mcApiKey = $channel->getChannelConfigurations()->getMcApiKey();

        if (empty($mcApiKey)) {
            return new Response('Remember to set a valid MailChimp API key so we can connect.', 400);
        }

        $mailChimp = MailChimpFacade::createFromChannel($channel);

        if (empty($mailChimp)) {
            return new Response('We couldn\'t get the MailChimp with provided mailchimp api key', 400);
        }

        if ($mailChimp->checkMailChimpHealthIfNeeded($channel)) {
            $mcResponse = $mailChimp->ping();
            $channel->setTimeSinceMailChimpStatusPinged(CarbonImmutable::now()->toDateTimeString());
            $isValidMcApiKey = $mcResponse->isSuccess();
            $channel->setHasValidMailChimpKey($isValidMcApiKey);


            $updatedChannelWithValidatingProperties = $this->channelService->updateChannel($channel);
            if (empty($updatedChannelWithValidatingProperties)) {
                return response()->json("Failed to update channel with id #{$channel->getId()}.", 500);
            }
        }

        $request->setRouteResolver(function () use ($mailChimp, $route) {
            $route[2]['mailChimp'] = $mailChimp;

            return $route;
        });

        return $next($request);
    }
}
