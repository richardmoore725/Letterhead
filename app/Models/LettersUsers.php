<?php

namespace App\Models;

use App\DTOs\LettersUsersDto;

class LettersUsers
{
    private $userId;
    private $id;
    private $letterId;

    public function __construct(LettersUsersDto $dto = null)
    {
        $this->userId = $dto->userId;
        $this->id = $dto->id;
        $this->letterId = $dto->letterId;
    }

    public function convertToArray(): array
    {
        return [
            'authorId' => $this->getUserId(),
        ];
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLetterId(): int
    {
        return $this->letterId;
    }
}
