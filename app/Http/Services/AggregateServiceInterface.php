<?php

namespace App\Http\Services;

use App\Collections\AggregateCollection;
use App\Models\Aggregate;

interface AggregateServiceInterface
{
    public function getAggregates(): AggregateCollection;
    public function getAggregateById(int $id): ?Aggregate;
    public function createAggregate(Aggregate $aggregate): ?Aggregate;
    public function updateAggregate(Aggregate $aggregate): ?Aggregate;
    public function deleteAggregate(Aggregate $aggregate): bool;
}
