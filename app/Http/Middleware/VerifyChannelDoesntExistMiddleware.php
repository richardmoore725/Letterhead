<?php

namespace App\Http\Middleware;

use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;

class VerifyChannelDoesntExistMiddleware
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
        $channelSlugFromRequest = $request->input('channelSlug', '');
        $channel = $this->channelService->getChannelBySlug($channelSlugFromRequest);

        if (!empty($channel)) {
            return response()->json("Unforunately, a channel with this slug already exists", 409);
        }

        return $next($request);
    }
}
