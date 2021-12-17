<?php

namespace App\Http\Services;

use App\Models\PlatformEvent;
use App\Http\Repositories\PlatformEventRepositoryInterface;

class PlatformEventService implements PlatformEventServiceInterface
{
    private $repository;

    public function __construct(PlatformEventRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createPlatformEvent(PlatformEvent $platformEvent): ?PlatformEvent
    {
        $dto = $this->repository->createPlatformEvent($platformEvent->convertToDto());

        if (empty($dto)) {
            return null;
        }

        return new PlatformEvent($dto);
    }

    public function deletePlatformEvent(PlatformEvent $platformEvent): bool
    {
        return $this->repository->deletePlatformEvent($platformEvent->convertToDto());
    }

    public function updatePlatformEvent(PlatformEvent $platformEvent): ?PlatformEvent
    {
        $updatedDto = $this->repository->updatePlatformEvent($platformEvent->convertToDto());
        return empty($updatedDto) ? null : new PlatformEvent($updatedDto);
    }

    public function getPlatformEventById(int $id): ?PlatformEvent
    {
        $dto = $this->repository->getPlatformEventById($id);

        if (empty($dto)) {
            return null;
        }

        return new PlatformEvent($dto);
    }
}
