<?php

namespace App\Http\Repositories;

use App\Collections\AggregateCollection;
use App\DTOs\AggregateDto;
use Carbon\CarbonImmutable;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class AggregateRepository implements AggregateRepositoryInterface
{
    public const TABLE_AGGREGATES = 'aggregates';

    public function getAggregateById(int $id): ?AggregateDto
    {
        try {
            $aggregateFromDatabase = $this->getAggregateRowFromDatabaseById($id);

            if (empty($aggregateFromDatabase)) {
                return null;
            }

            $dto = new AggregateDto($aggregateFromDatabase);

            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    private function getAggregateRowFromDatabaseById(int $id): ?\stdClass
    {
        return app('db')
            ->table(self::TABLE_AGGREGATES)
            ->find($id);
    }

    public function getAggregates(): AggregateCollection
    {
        try {
            $aggregateDatabaseResult = app('db')
                ->table(self::TABLE_AGGREGATES)
                ->whereNull('deleted_at')
                ->get();

            $aggregatesDtos = array_map(function (object $aggregateDatabaseObject) {
                $aggregateDto = new AggregateDto($aggregateDatabaseObject);

                return $aggregateDto;
            }, $aggregateDatabaseResult->toArray());

            return new AggregateCollection($aggregatesDtos);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new AggregateCollection([]);
        }
    }

    public function createAggregate(AggregateDto $dto): ?AggregateDto
    {
        try {
            $aggregateId = $this->insertChannelIntoDatabase($dto);
            return $this->getAggregateById($aggregateId);
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }

    /**
     * @throws \Exception
     */
    private function insertChannelIntoDatabase(AggregateDto $dto): int
    {
        $aggregateId = app('db')
            ->table(self::TABLE_AGGREGATES)
            ->insertGetId($dto->mapToDatabaseColumns());

        return $aggregateId;
    }

    public function updateAggregate(AggregateDto $dto): ?AggregateDto
    {
        try {
            app('db')->beginTransaction();

            app('db')
                ->table(self::TABLE_AGGREGATES)
                ->where('id', $dto->id)
                ->update($dto->mapToDatabaseColumns());

            $updatedAggregate = $this->getAggregateById($dto->id);

            app('db')->commit();

            return $updatedAggregate;
        } catch (\Exception $e) {
            app('db')->rollBack();
            return null;
        }
    }

    public function deleteAggregate(AggregateDto $dto): bool
    {
        try {
            return app('db')
                ->table(self::TABLE_AGGREGATES)
                ->where('id', $dto->id)
                ->update([
                    'deleted_at' => CarbonImmutable::now(),
                ]);
        } catch (\Exception $e) {
            return false;
        }
    }
}
