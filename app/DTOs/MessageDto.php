<?php

namespace App\DTOs;

/**
 * The `MessageDto` is responsible for transforming the "Message" data
 * from a database query into the format we want for use in our
 * application code.
 *
 * Class MessageDto
 * @package App\DTOs
 */
class MessageDto implements DtoInterface
{
    public $createdAt = '';
    public $deletedAt = null;
    public $id = 0;
    public $message = '';
    public $resourceId = 0;
    public $resourceName = '';
    public $uniqueId = '';
    public $userId = 0;

    public function __construct(\stdClass $databaseObject = null)
    {
        if (empty($databaseObject)) {
            return;
        }

        $this->createdAt = $databaseObject->created_at;
        $this->deletedAt = $databaseObject->deleted_at;
        $this->id = $databaseObject->id;
        $this->message = $databaseObject->message;
        $this->resourceId = $databaseObject->resourceId;
        $this->resourceName = $databaseObject->resourceName;
        $this->uniqueId = $databaseObject->uniqueId;
        $this->userId = $databaseObject->userId;
    }

    public function mapDtoToDatabaseColumnsArray(): array
    {
        return [
            'created_at' => $this->createdAt,
            'deleted_at' => $this->deletedAt,
            'id' => (int) $this->id,
            'message' => $this->message,
            'resourceId' => (int) $this->resourceId,
            'resourceName' => $this->resourceName,
            'uniqueId' => $this->uniqueId,
            'userId' => (int) $this->userId
        ];
    }
}
