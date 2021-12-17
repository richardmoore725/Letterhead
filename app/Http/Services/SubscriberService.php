<?php

namespace App\Http\Services;

use App\Http\Repositories\LetterheadEspRepositoryInterface;
use App\Http\Response;

class SubscriberService implements SubscriberServiceInterface
{
    private $repository;

    public function __construct(LetterheadEspRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getSubscribersByChannel(int $channelId): Response
    {
        return $this->repository->getSubscribersByChannel($channelId);
    }
}
