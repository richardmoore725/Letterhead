<?php

namespace App\Http\Services;

use App\Models\TransactionalEmail;
use App\Models\Channel;
use Illumunate\Support\Collection;
use App\Collections\TransactionalEmailCollection;

interface TransactionalEmailServiceInterface
{
    public function createTransactionalEmail(TransactionalEmail $transactionalEmail): ?TransactionalEmail;
    public function deleteTransactionalEmail(TransactionalEmail $transactionalEmail): bool;
    public function updateTransactionalEmail(TransactionalEmail $transactionalEmail): ?TransactionalEmail;
    public function getTransactionalEmailById(int $id): ?TransactionalEmail;
    public function getTransactionalEmailsByChannel(int $channelId): ?TransactionalEmailCollection;
    public function getTransactionalEmailByChannelAndEventSlug(Channel $channel, string $eventSlug): ?TransactionalEmail;
}
