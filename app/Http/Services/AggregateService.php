<?php

namespace App\Http\Services;

use App\Collections\AggregateCollection;
use App\Http\Repositories\AggregateRepositoryInterface;
use App\Models\Aggregate;
use Carbon\CarbonImmutable;
use RandomLib\Factory;

class AggregateService implements AggregateServiceInterface
{
    private $repository;

    public function __construct(AggregateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAggregateById(int $id): ?Aggregate
    {
        $dto = $this->repository->getAggregateById($id);

        if (empty($dto)) {
            return null;
        }

        return new Aggregate(null, $dto);
    }

    public function getAggregates(): AggregateCollection
    {
        return $this->repository->getAggregates();
    }

    public function createAggregate(Aggregate $aggregate): ?Aggregate
    {
        $creationTime = CarbonImmutable::now()->toDateTimeString();
        $aggregate->setCreatedAt($creationTime);
        $aggregate->setUpdatedAt($creationTime);
        $aggregate->setUniqueId($this->generateUniqueIdentifier());

        $dto = $this->repository->createAggregate($aggregate->convertToDto());

        if (empty($dto)) {
            return null;
        }

        return new Aggregate(null, $dto);
    }

    public function updateAggregate(Aggregate $aggregate): ?Aggregate
    {
        $updateTime = CarbonImmutable::now()->toDateTimeString();
        $aggregate->setUpdatedAt($updateTime);
        $updatedDto = $this->repository->updateAggregate($aggregate->convertToDto());

        return empty($updatedDto) ? null : new Aggregate(null, $updatedDto);
    }

    public function deleteAggregate(Aggregate $aggregate): bool
    {
        return $this->repository->deleteAggregate($aggregate->convertToDto());
    }

    /**
     * Generate a 10 character long random string to serve as the
     * Letter's uniqueId.
     *
     * @return string
     */
    private function generateUniqueIdentifier(): string
    {
        $charactersToComposeKey = 'abcdefghiklmnopqrstuvwxyz0123456789';
        $randomStringFactory = new Factory();
        $randomStringGenerator = $randomStringFactory->getMediumStrengthGenerator();

        return $randomStringGenerator->generateString(10, $charactersToComposeKey);
    }
}
