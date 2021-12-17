<?php

namespace App\Http\Services;

use App\Models\TransactionalEmail;
use App\Models\Channel;
use App\Collections\TransactionalEmailCollection;
use Illuminate\Support\Collection;
use App\Http\Repositories\TransactionalEmailRepositoryInterface;

class TransactionalEmailService implements TransactionalEmailServiceInterface
{
    private $repository;

    public function __construct(TransactionalEmailRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function createTransactionalEmail(TransactionalEmail $transactionalEmail): ?TransactionalEmail
    {
        $dto = $this->repository->createTransactionalEmail($transactionalEmail->convertToDto());

        if (empty($dto)) {
            return null;
        }

        return new TransactionalEmail($dto);
    }

    public function deleteTransactionalEmail(TransactionalEmail $transactionalEmail): bool
    {
        return $this->repository->deleteTransactionalEmail($transactionalEmail->convertToDto());
    }

    public function updateTransactionalEmail(TransactionalEmail $transactionalEmail): ?TransactionalEmail
    {
        $updatedDto = $this->repository->updateTransactionalEmail($transactionalEmail->convertToDto());
        return empty($updatedDto) ? null : new TransactionalEmail($updatedDto);
    }

    public function getTransactionalEmailById(int $id): ?TransactionalEmail
    {
        $dto = $this->repository->getTransactionalEmailById($id);

        if (empty($dto)) {
            return null;
        }

        return new TransactionalEmail($dto);
    }

    public function getTransactionalEmailsByChannel(int $channelId): ?TransactionalEmailCollection
    {
        $transactionalEmails = $this->repository->getTransactionalEmailsByChannelId($channelId);

        if (empty($transactionalEmails)) {
            return null;
        }

        return $transactionalEmails;
    }

    public function getTransactionalEmailByChannelAndEventSlug(Channel $channel, string $eventSlug): ?TransactionalEmail
    {
        $transactionalEmailDto = $this->repository->getTransactionalEmailByChannelAndEventSlug($channel->getId(), $eventSlug);

        if (empty($transactionalEmailDto)) {
            return null;
        }

        return new TransactionalEmail($transactionalEmailDto);
    }
}
