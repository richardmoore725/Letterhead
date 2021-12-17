<?php

namespace App\Http\Controllers;

use App\Http\Services\PlatformEventServiceInterface;
use App\Models\PlatformEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformEventController extends Controller
{
    private $platformEventService;

    public function __construct(PlatformEventServiceInterface $platformEventService)
    {
        $this->platformEventService = $platformEventService;
    }

    public function createPlatformEvent(
        string $name,
        string $description,
        string $eventSlug
    ): JsonResponse {
        $eventToCreate = new PlatformEvent();
        $eventToCreate->setName($name);
        $eventToCreate->setDescription($description);
        $eventToCreate->setEventSlug($eventSlug);

        $newlyCreatedPlatformEvent = $this->platformEventService->createPlatformEvent($eventToCreate);

        if (empty($newlyCreatedPlatformEvent)) {
            return response()->json('We were not able to create this platform event', 500);
        }

        return response()->json($newlyCreatedPlatformEvent->convertToArray());
    }

    public function deletePlatformEvent(PlatformEvent $platformEvent): JsonResponse
    {
        $wasPlatformEventDeleted = $this->platformEventService->deletePlatformEvent($platformEvent);

        if ($wasPlatformEventDeleted) {
            return response()->json('We have deleted this platform event.', 200);
        }

        return response()->json('We were not able to delete this platform event.', 500);
    }

    public function updatePlatformEvent(
        PlatformEvent $platformEvent,
        string $description,
        string $eventSlug,
        string $name
    ): JsonResponse {
        $platformEvent->setDescription($description);
        $platformEvent->setEventSlug($eventSlug);
        $platformEvent->setName($name);

        $updatedPlatformEvent = $this->platformEventService->updatePlatformEvent($platformEvent);

        if (empty($updatedPlatformEvent)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedPlatformEvent->convertToArray());
    }
}
