<?php

namespace App\Http\Repositories;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Http\Response;

interface MessageRepositoryInterface
{
    public function createMessage(MessageDto $dto): Response;
    public function getMessagesByResource(int $resourceId, string $resourceName): MessageCollection;
}
