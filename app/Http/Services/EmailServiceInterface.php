<?php

namespace App\Http\Services;

use App\Models\Email;
use Illuminate\Support\Collection;
use App\Collections\EmailCollection;

interface EmailServiceInterface
{
    public function createEmail(Email $email): ?Email;
    public function deleteEmail(Email $email): bool;
    public function updateEmail(Email $email): ?Email;
    public function getEmailById(int $id): ?Email;
    public function getEmailsByChannel(int $channelId): ?EmailCollection;
}
