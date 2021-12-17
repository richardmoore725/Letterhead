<?php

namespace App\Http\Repositories;

use App\Collections\AggregateCollection;
use App\DTOs\AggregateDto;

interface AggregateRepositoryInterface
{
    public function getAggregateById(int $aggregateId): ?AggregateDto;
    public function getAggregates(): AggregateCollection;
    public function createAggregate(AggregateDto $dto): ?AggregateDto;
    public function updateAggregate(AggregateDto $dto): ?AggregateDto;
    public function deleteAggregate(AggregateDto $dto): bool;
}
