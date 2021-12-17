<?php

namespace App\Models;

use App\DTOs\PassportStampDto;

class PassportStamp
{
    private $accessToken;
    private $email;
    private $expiration;
    private $id;
    private $name;
    private $originalToken;
    private $refreshToken;

    public function __construct(PassportStampDto $dto)
    {
        $this->accessToken = $dto->accessToken;
        $this->email = $dto->email;
        $this->expiration = $dto->expiration;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->originalToken = $dto->originalToken;
        $this->refreshToken = $dto->refreshToken;
    }

    public function convertToArray(): array
    {
        return [
            'email' => $this->email,
            'id' => $this->id,
            'name' => $this->name,
        ];
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getToken(): string
    {
        return $this->originalToken;
    }
}
