<?php

namespace App\Http\Middleware;

use Closure;
use App\Http\Services\ChannelServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\Channel;
use Carbon\CarbonImmutable;
use App\Http\Repositories\ConstantContactRepositoryInterface;

class VerifyConstantContactMiddleware
{
    private $channelService;
    private $constantContactRepository;

    public function __construct(ChannelServiceInterface $channelService, ConstantContactRepositoryInterface $constantContactRepository)
    {
        $this->channelService = $channelService;
        $this->constantContactRepository = $constantContactRepository;
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
        $channelId = (int)$routeParameters['channelId'];

        $channel = $this->channelService->getChannelById($channelId);

        // get new refresh token if expired
        if ($channel->isAccessTokenExpired()) {
            $token = $this->constantContactRepository->getNewAccessToken($channel->getCCRefreshToken());
            if (isset($token['error'])) {
                $channel->clearCCTokens();
                $this->channelService->updateChannel($channel);
                return response()->json("Invalid Token, Try again to connect ConstantContact in your setting.", 401);
            }
            $channel->setCCAccessToken($token['access_token']);
            $channel->setCCRefreshToken($token['refresh_token']);
            $channel->setCCAccessTokenLastUsed();
            $updateChannel = $this->channelService->updateChannel($channel);
        } else {
            $channel->setCCAccessTokenLastUsed();
            $updateChannel = $this->channelService->updateChannel($channel);
        }

        if (!$updateChannel) {
            return response()->json("Internal error!. Try again.", 500);
        }

        $response = $next($request);

        // Post-Middleware Action

        return $response;
    }
}
