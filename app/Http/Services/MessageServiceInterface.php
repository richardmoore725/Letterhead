<?php

namespace App\Http\Services;

use App\Collections\MessageCollection;
use App\Http\Response;

interface MessageServiceInterface
{
    public function createMessage(string $message, int $resourceId, string $resourceName, int $userId): Response;
    public function getMessagesByResource(int $resourceId, string $resourceName): MessageCollection;
}
