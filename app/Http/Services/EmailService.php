<?php

namespace App\Http\Services;

use App\Models\Email;
use App\Collections\EmailCollection;
use Illuminate\Support\Collection;
use App\Http\Repositories\EmailRepositoryInterface;

class EmailService implements EmailServiceInterface
{
    private $repository;

    public function __construct(EmailRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createEmail(Email $email): ?Email
    {
        $dto = $this->repository->createEmail($email->convertToDto());

        if (empty($dto)) {
            return null;
        }

        return new Email($dto);
    }

    public function deleteEmail(Email $email): bool
    {
        return $this->repository->deleteEmail($email->convertToDto());
    }

    public function updateEmail(Email $email): ?Email
    {
        $updatedDto = $this->repository->updateEmail($email->convertToDto());
        return empty($updatedDto) ? null : new Email($updatedDto);
    }

    public function getEmailById(int $id): ?Email
    {
        $dto = $this->repository->getEmailById($id);

        if (empty($dto)) {
            return null;
        }

        return new Email($dto);
    }

    public function getEmailsByChannel(int $channelId): ?EmailCollection
    {
        $emails = $this->repository->getEmailsByChannelId($channelId);

        if (empty($emails)) {
            return null;
        }

        return $emails;
    }
}
