<?php

namespace App\Http\Repositories;

use App\DTOs\PlatformEventDto;

interface PlatformEventRepositoryInterface
{
    public function createPlatformEvent(PlatformEventDto $dto): ?PlatformEventDto;
    public function getPlatformEventById(int $id): ?PlatformEventDto;
    public function deletePlatformEvent(PlatformEventDto $dto): bool;
    public function updatePlatformEvent(PlatformEventDto $dto): ?PlatformEventDto;
}
