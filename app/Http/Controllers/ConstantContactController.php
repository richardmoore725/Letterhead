<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Repositories\ConstantContactRepositoryInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;

class ConstantContactController extends Controller
{
    private $channelService;
    private $constantContactRepository;
    //
    public function __construct(ChannelServiceInterface $channelService, ConstantContactRepositoryInterface $constantContactRepository)
    {
        $this->channelService = $channelService;
        $this->constantContactRepository = $constantContactRepository;
    }

    public function GetAccessToken(Channel $channel, Request $request): JsonResponse
    {
        $code = $request['code'];
        $token = $this->constantContactRepository->GetAccessToken($code);
        if (isset($token['error'])) {
            return response()->json($token, 400);
        }
        $channel->setCCAccessToken($token['access_token']);
        $channel->setCCRefreshToken($token['refresh_token']);
        $this->channelService->updateChannel($channel);
        return response()->json($token, 200);
    }

    public function getNewAccessToken($channel): array
    {
        $refresh_token = $channel->getCCRefreshToken();
        $token = $this->constantContactRepository->getNewAccessToken($refresh_token);
    }
}
