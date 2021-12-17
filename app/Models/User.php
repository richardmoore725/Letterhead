<?php

namespace App\Models;

use App\DTOs\UserDto;

class User
{
    private $createdAt;
    private $email;
    private $id;
    private $name;
    private $surname;

    public function __construct(UserDto $dto)
    {
        $this->createdAt = $dto->createdAt;
        $this->email = $dto->email;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->surname = $dto->surname;
    }

    public function convertToArray(): array
    {
        return [
            'createdAt' => $this->createdAt,
            'email' => $this->email,
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname
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

    public function getSurname(): string
    {
        return $this->surname;
    }

    public function getFullName(): string
    {
        return $this->getName() . ' ' . $this->getSurname();
    }

    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
