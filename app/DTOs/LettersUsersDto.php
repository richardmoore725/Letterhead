<?php

namespace App\DTOs;

use App\Collections\LetterPartCollection;
use App\Models\Letter;
use App\Models\LettersEmails;
use App\Models\LettersUsers;

/**
 * @package App\DTOs
 */
class LettersUsersDto
{
    public $id;
    public $letterId;
    public $userId;

    public function __construct(\stdClass $object = null, LettersUsers $lettersUsers = null)
    {
        if (!empty($object)) {
            $this->id = $object->id;
            $this->letterId = $object->letterId;
            $this->userId = $object->userId;
        }

        if (!empty($lettersUsers)) {
            $this->id = $lettersUsers->getId();
            $this->letterId = $lettersUsers->getLetterId();
            $this->userId = $lettersUsers->getUserId();
        }
    }

    public function mapChannelDtoToDatabaseColumns(): array
    {
        return [
            'id' => $this->id,
            'letterId' => $this->letterId,
            'userId' => $this->userId,
        ];
    }
}
