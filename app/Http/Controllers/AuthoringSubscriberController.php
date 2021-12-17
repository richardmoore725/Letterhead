<?php

namespace App\Http\Controllers;

use App\Http\Services\SubscriberServiceInterface;
use App\Collections\ChannelSubscriberCollection;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use App\Http\Response;

class AuthoringSubscriberController extends Controller
{
    private $service;

    public function __construct(SubscriberServiceInterface $service)
    {
        $this->service = $service;
    }

    public function getSubscribersByChannel(Channel $channel): JsonResponse
    {
        $serviceResponse = $this->service->getSubscribersByChannel($channel->getId());

        if ($serviceResponse->isError()) {
            return $serviceResponse->getJsonResponse();
        }

        $subscribers = $serviceResponse->getData();

        return response()->json($subscribers->getPublicArray());
    }
}
