<?php

namespace App\Http\Controllers;

use App\Http\Services\AggregateServiceInterface;
use App\Models\Aggregate;
use Illuminate\Http\JsonResponse;

class AggregateController extends Controller
{
    private $aggregateService;

    public function __construct(AggregateServiceInterface $aggregateService)
    {
        $this->aggregateService  = $aggregateService;
    }

    public function getAggregateById(Aggregate $aggregate): JsonResponse
    {
        return response()->json($aggregate->convertToArray());
    }

    public function getAggregates(): JsonResponse
    {
        $aggregates = $this->aggregateService->getAggregates();

        return response()->json($aggregates->getPublicArray());
    }

    public function createAggregate(
        bool $archived,
        int $channelId,
        bool $curated,
        string $dateOfAggregatePublication,
        string $excerpt,
        string $image,
        int $letterId,
        string $originalUrl,
        string $title,
        string $siteName
    ): JsonResponse {
        $aggregateToCreate = new Aggregate();
        $aggregateToCreate->setArchived($archived);
        $aggregateToCreate->setChannelId($channelId);
        $aggregateToCreate->setCurated($curated);
        $aggregateToCreate->setDateOfAggregatePublication($dateOfAggregatePublication);
        $aggregateToCreate->setExcerpt($excerpt);
        $aggregateToCreate->setImage($image);
        $aggregateToCreate->setLetterId($letterId);
        $aggregateToCreate->setOriginalUrl($originalUrl);
        $aggregateToCreate->setTitle($title);
        $aggregateToCreate->setSiteName($siteName);

        $newlyCreatedAggregate = $this->aggregateService->createAggregate($aggregateToCreate);

        if (empty($newlyCreatedAggregate)) {
            return response()->json('We were not able to create this aggregate', 500);
        }

        return response()->json($newlyCreatedAggregate->convertToArray(), 201);
    }

    public function updateAggregate(
        Aggregate $aggregate,
        bool $archived,
        int $channelId,
        bool $curated,
        string $dateOfAggregatePublication,
        string $excerpt,
        string $image,
        int $letterId,
        string $originalUrl,
        string $title,
        string $siteName
    ): JsonResponse {
        $aggregate->setArchived($archived);
        $aggregate->setChannelId($channelId);
        $aggregate->setCurated($curated);
        $aggregate->setDateOfAggregatePublication($dateOfAggregatePublication);
        $aggregate->setExcerpt($excerpt);
        $aggregate->setImage($image);
        $aggregate->setLetterId($letterId);
        $aggregate->setOriginalUrl($originalUrl);
        $aggregate->setTitle($title);
        $aggregate->setSiteName($siteName);

        $updatedEmail = $this->aggregateService->updateAggregate($aggregate);

        if (empty($updatedEmail)) {
            return response()->json('Something went wrong', 500);
        }

        return response()->json($updatedEmail->convertToArray());
    }

    public function deleteAggregateById(Aggregate $aggregate): JsonResponse
    {
        $wasChannelDeleted = $this->aggregateService->deleteAggregate($aggregate);

        return response()->json($wasChannelDeleted);
    }
}
