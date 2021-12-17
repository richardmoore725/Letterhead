<?php

namespace App\DTOs;

class PassportStampDto
{
    public $accessToken;
    public $email;
    public $expiration;
    public $id;
    public $name;

    /**
     * @var string The original encrypted token.
     */
    public $originalToken;
    public $refreshToken;

    public function __construct(\stdClass $object, string $originalToken)
    {
        $this->accessToken = $object->acc;
        $this->email = $object->user->email;
        $this->expiration = $object->exp;
        $this->id = $object->user->id;
        $this->name = $object->user->name;
        $this->originalToken = $originalToken;
        $this->refreshToken = $object->ref;
    }
}
