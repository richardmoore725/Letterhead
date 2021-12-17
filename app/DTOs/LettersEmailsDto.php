<?php

namespace App\DTOs;

use App\Collections\LetterPartCollection;
use App\Models\Letter;
use App\Models\LettersEmails;

/**
 * @package App\DTOs
 */
class LettersEmailsDto
{
    public $id;
    public $letterId;
    public $emailId;

    public function __construct(\stdClass $object = null, LettersEmails $lettersEmails)
    {
        if (!empty($object)) {
            $this->id = $object->id;
            $this->letterId = $object->letterId;
            $this->emailId = $object->emailId;
        }

        if (!empty($lettersEmails)) {
            $this->id = $lettersEmails->getId();
            $this->letterId = $lettersEmails->getLetterId();
            $this->emailId = $lettersEmails->getEmailId();
        }
    }

    public function mapChannelDtoToDatabaseColumns(): array
    {
        return [
            'id' => $this->id,
            'letterId' => $this->letterId,
            'emailId' => $this->emailId,
        ];
    }
}
