<?php

namespace App\Models;

use App\DTOs\EmailDto;

class Email
{
    public $brandId;
    public $channelId;
    public $content;
    public $createdAt;
    public $description;
    public $deletedAt;
    public $fromEmail;
    public $fromName;
    public $id;
    public $name;
    public $subject;
    public $updatedAt;

    public function __construct(EmailDto $dto = null)
    {
        if (empty($dto)) {
            return;
        }

        $this->brandId = $dto->brandId;
        $this->channelId = $dto->channelId;
        $this->content = $dto->content;
        $this->createdAt = $dto->createdAt;
        $this->description = $dto->description;
        $this->deletedAt = $dto->deletedAt;
        $this->fromEmail = $dto->fromEmail;
        $this->fromName = $dto->fromName;
        $this->id = $dto->id;
        $this->name = $dto->name;
        $this->subject = $dto->subject;
        $this->updatedAt = $dto->updatedAt;
    }

    public function convertToDto(): EmailDto
    {
        $dto = new EmailDto();
        $dto->brandId = $this->brandId;
        $dto->channelId = $this->channelId;
        $dto->content = $this->content;
        $dto->createdAt = $this->createdAt;
        $dto->description = $this->description;
        $dto->deletedAt = $this->deletedAt;
        $dto->fromEmail = $this->fromEmail;
        $dto->fromName = $this->fromName;
        $dto->id = $this->id;
        $dto->name = $this->name;
        $dto->subject = $this->subject;
        $dto->updatedAt = $this->updatedAt;

        return $dto;
    }

    public function getBrandId(): int
    {
        return $this->brandId;
    }

    public function getChannelId(): int
    {
        return $this->channelId;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFromEmail(): string
    {
        return $this->fromEmail;
    }

    public function getFromName(): string
    {
        return $this->fromName;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setBrandId(int $brandId): void
    {
        $this->brandId = $brandId;
    }

    public function setChannelId(int $channelId): void
    {
        $this->channelId = $channelId;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setDeletedAt($deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function setFromEmail(string $fromEmail): void
    {
        $this->fromEmail = $fromEmail;
    }

    public function setFromName(string $fromName): void
    {
        $this->fromName = $fromName;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function convertToArray(): array
    {
        return [
            'brandId' => $this->brandId,
            'channelId' => $this->channelId,
            'content' => $this->content,
            'createdAt' => $this->createdAt,
            'description' => $this->description,
            'deletedAt' => $this->deletedAt,
            'fromEmail' => $this->fromEmail,
            'fromName' => $this->fromName,
            'id' => $this->id,
            'name' => $this->name,
            'subject' => $this->subject,
            'updatedAt' => $this->updatedAt,
        ];
    }

    public static function getValidationRules(): array
    {
        return [
            'brandId' => 'required|int',
            'channelId' => 'required|int',
            'content' => 'required|string',
            'description' => 'required|string',
            'fromEmail' => 'required|string',
            'fromName' => 'required|string',
            'name' => 'required|string',
            'subject' => 'required|string',
        ];
    }
}
