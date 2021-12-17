<?php

namespace App\Http\Services;

use App\Models\PassportStamp;
use Illuminate\Http\Request;

interface AuthServiceInterface
{
    public function authenticatePassport(string $origin, string $token): ?PassportStamp;
    public function authorizeActionFromPassportStamp(
        PassportStamp $passportStamp,
        string $action,
        string $resource,
        int $resourceId
    ): bool;
}
