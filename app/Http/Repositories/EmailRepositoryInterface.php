<?php

namespace App\Http\Repositories;

use App\DTOs\EmailDto;
use Illuminate\Support\Collection;
use App\Collections\EmailCollection;

interface EmailRepositoryInterface
{
    public function createEmail(EmailDto $dto): ?EmailDto;
    public function getEmailById(int $id): ?EmailDto;
    public function deleteEmail(EmailDto $dto): bool;
    public function updateEmail(EmailDto $dto): ?EmailDto;
    public function getEmailsByChannelId(int $channelId): ?EmailCollection;
}
