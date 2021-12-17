<?php

namespace App\Http\Services;

use App\Models\PlatformEvent;

interface PlatformEventServiceInterface
{
    public function createPlatformEvent(PlatformEvent $platformEvent): ?PlatformEvent;
    public function deletePlatformEvent(PlatformEvent $platformEvent): bool;
    public function updatePlatformEvent(PlatformEvent $platformEvent): ?PlatformEvent;
    public function getPlatformEventById(int $id): ?PlatformEvent;
}
