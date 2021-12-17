<?php

namespace App\Http\Services;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Http\Repositories\MessageRepositoryInterface;
use App\Http\Response;
use App\Models\Message;

/**
 * MessageService is responsible for conveying messages - either by the system or by the user -
 * about specific resources. AKA: commenting :).
 *
 * Class MessageService
 * @package App\Http\Services
 */
class MessageService extends BaseService implements MessageServiceInterface
{
    private $repository;

    public function __construct(MessageRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param string $message
     * @param int $resourceId
     * @param string $resourceName
     * @return Response
     * @uses BaseService::generateUniqueIdentifier()
     */
    public function createMessage(string $messageCopy, int $resourceId, string $resourceName, int $userId): Response
    {
        $message = new Message(new MessageDto());
        $message->setCreatedAtToNow();
        $message->setMessage($messageCopy);
        $message->setResourceId($resourceId);
        $message->setResourceName($resourceName);
        $message->setUniqueId($this->generateUniqueIdentifier());
        $message->setUserId($userId);

        return $this->repository->createMessage($message->convertToDto());
    }

    /**
     * @param int $resourceId
     * @param string $resourceName
     * @return MessageCollection
     */
    public function getMessagesByResource(int $resourceId, string $resourceName): MessageCollection
    {
        return $this->repository->getMessagesByResource($resourceId, $resourceName);
    }
}
