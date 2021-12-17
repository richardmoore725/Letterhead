<?php

namespace App\Http\Repositories;

use App\DTOs\TransactionalEmailDto;
use Illuminate\Support\Collection;
use App\Collections\TransactionalEmailCollection;

interface TransactionalEmailRepositoryInterface
{
    public function createTransactionalEmail(TransactionalEmailDto $dto): ?TransactionalEmailDto;
    public function getTransactionalEmailById(int $id): ?TransactionalEmailDto;
    public function deleteTransactionalEmail(TransactionalEmailDto $dto): bool;
    public function updateTransactionalEmail(TransactionalEmailDto $dto): ?TransactionalEmailDto;
    public function getTransactionalEmailsByChannelId(int $channelId): ?TransactionalEmailCollection;
    public function getTransactionalEmailByChannelAndEventSlug(int $channelId, string $eventSlug): ?TransactionalEmailDto;
}
