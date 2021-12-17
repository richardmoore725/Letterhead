<?php

namespace App\Http\Services;

use App\Http\Response;

interface SubscriberServiceInterface
{
    public function getSubscribersByChannel(int $channelId): Response;
}
