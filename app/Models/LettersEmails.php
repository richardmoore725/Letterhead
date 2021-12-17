<?php

namespace App\Models;

use App\DTOs\LettersEmailsDto;

class LettersEmails
{
    private $emailId;
    private $id;
    private $letterId;

    public function __construct(LettersEmailsDto $dto = null)
    {
        $this->emailId = $dto->emailId;
        $this->id = $dto->id;
        $this->letterId = $dto->letterId;
    }

    public function convertToArray(): array
    {
        return [];
    }

    public function getEmailId(): int
    {
        return $this->emailId;
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
