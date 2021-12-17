<?php

namespace App\Http\Repositories;

use App\Collections\PlatformEventCollection;
use App\DTOs\PlatformEventDto;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class PlatformEventRepository implements PlatformEventRepositoryInterface
{
    public const TABLE_PLATFORM_EVENTS = 'platform_events';

    public function createPlatformEvent(PlatformEventDto $dto): ?PlatformEventDto
    {
        try {
            $dto->createdAt = CarbonImmutable::now()->toDateTimeString();
            $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();

            $id = $this->insertPlatformEventIntoDatabase($dto);
            return $this->getPlatformEventById($id);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function insertPlatformEventIntoDatabase(PlatformEventDto $dto): int
    {
        $id = app('db')
          ->table(self::TABLE_PLATFORM_EVENTS)
          ->insertGetId($dto->mapPlatformEventDtoToDatabaseColumns());
        return $id;
    }

    public function getPlatformEventById(int $id): ?PlatformEventDto
    {
        try {
            $platformEventFromDatabase = $this->getPlatformEventRowFromDatabaseById($id);

            if (empty($platformEventFromDatabase)) {
                return null;
            }

            $dto = new PlatformEventDto($platformEventFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    private function getPlatformEventRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
        ->table(self::TABLE_PLATFORM_EVENTS)
        ->where('id', '=', $id)
        ->first();
    }

    public function deletePlatformEvent(PlatformEventDto $dto): bool
    {
        try {
            return app('db')
            ->table(self::TABLE_PLATFORM_EVENTS)
            ->where('id', $dto->id)
            ->update(['deleted_at' => CarbonImmutable::now()]);
        } catch (\Exception $e) {
            return false;
        }
    }

    public function updatePlatformEvent(PlatformEventDto $dto): ?PlatformEventDto
    {
        $dto->updatedAt = CarbonImmutable::now()->toDateTimeString();
        $dto->deletedAt = null;

        try {
            app('db')
            ->table(self::TABLE_PLATFORM_EVENTS)
            ->where('id', $dto->id)
            ->update($dto->mapPlatformEventDtoToDatabaseColumns());

            $updatedPlatformEvent = $this->getPlatformEventById($dto->id);

            return $updatedPlatformEvent;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
