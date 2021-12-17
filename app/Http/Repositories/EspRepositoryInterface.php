<?php

namespace App\Http\Repositories;

use App\Http\Response;
use App\Models\Letter;

interface EspRepositoryInterface
{
    public function send(
        string $accessToken,
        Letter $letter,
        string $senderEmailAddress,
        string $senderFromName,
        string $subject,
        string $template
    ): Response;

    public function test(
        string $accessToken,
        string $emailAddress,
        Letter $letter,
        string $senderEmailAddress,
        string $senderFromName,
        string $subject,
        string $template
    ): Response;
}
