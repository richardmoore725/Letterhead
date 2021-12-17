<?php

namespace App\Http\Repositories;

use App\Http\Response;

interface LetterheadEspRepositoryInterface
{
    public function getSubscribersByChannel(int $channelId): Response;
}
