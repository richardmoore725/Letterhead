<?php

namespace App\Models;

class PromotionCredit
{
    private $orderId;
    private $userId;
    private $userEmail;
    private $userName;

    public function __construct()
    {
    }

    public function convertToArray(): array
    {
        return [
            'orderId' => $this->getOrderId(),
            'userEmail' => $this->getUserEmail(),
            'userName' => $this->getUserName(),
        ];
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }


    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function setOrderId(int $orderId): void
    {
        $this->orderId = $orderId;
    }


    public function setUserEmail(string $name): void
    {
        $this->userEmail = $name;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function setUserName(string $email): void
    {
        $this->userName = $email;
    }
}
