<?php

namespace App\Http\Repositories;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Http\Response;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as Cache;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;

class MessageRepository implements MessageRepositoryInterface
{
    /**
     * The name of the table in our MySql database.
     */
    public const TABLE_MESSAGES = 'messages';

    /**
     * An instance of Illuminate's Cache factory, which provides
     * a number of useful methods for storing a cache.
     *
     * @var Cache
     */
    private $cache;

    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    public function createMessage(MessageDto $dto): Response
    {
        try {
            $newlyCreatedMessageId = app('db')
                ->table(self::TABLE_MESSAGES)
                ->insertGetId($dto->mapDtoToDatabaseColumnsArray());

            $this->forgetMessageCache($dto->resourceId, $dto->resourceName);

            $newlyCreatedMessage = $this->getMessageById($newlyCreatedMessageId);

            $dto = new MessageDto($newlyCreatedMessage);

            return new Response('', 201, $dto);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500, $e);
        }
    }

    private function getMessageById(int $id): ?\stdClass
    {
        return app('db')
            ->table(self::TABLE_MESSAGES)
            ->where('id', '=', $id)
            ->first();
    }

    public function getMessagesByResource(int $resourceId, string $resourceName): MessageCollection
    {
        $key = $this->getMessagesCacheKeyByResource($resourceId, $resourceName);

        $collectionFromCache = $this->cache->get($key);

        if (!empty($collectionFromCache)) {
            return $collectionFromCache;
        }

        return $this->getMessagesByResourceFromDatabase($resourceId, $resourceName);
    }

    public function getMessagesByResourceFromDatabase(int $resourceId, string $resourceName): MessageCollection
    {
        try {
            $messagesFromDataBase = app('db')
                ->table(self::TABLE_MESSAGES)
                ->where('resourceId', '=', $resourceId)
                ->where('resourceName', '=', $resourceName)
                ->whereNull('deleted_at')
                ->get();

            $messageCollection = new MessageCollection($messagesFromDataBase);

            if (!empty($messageCollection->all())) {
                $this->cacheMessageByResource($messageCollection, $this->getMessagesCacheKeyByResource($resourceId, $resourceName));
            }

            return $messageCollection;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return new MessageCollection([]);
        }
    }

    private function cacheMessageByResource(MessageCollection $messages, string $key): bool
    {
        $cacheExpiration = CarbonImmutable::now()->addMonth()->toDateTime();
        return $this->cache->put("{$key}", $messages, $cacheExpiration);
    }

    private function getMessagesCacheKeyByResource(int $resourceId, string $resourceName): string
    {
        return "{$resourceName}_{$resourceId}_letters";
    }

    private function forgetMessageCache(int $resourceId, string $resourceName)
    {
        $cacheKey = $this->getMessagesCacheKeyByResource($resourceId, $resourceName);
        return $this->cache->forget($cacheKey);
    }
}
