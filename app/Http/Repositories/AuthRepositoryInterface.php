<?php

namespace App\Http\Repositories;

use App\DTOs\PassportStampDto;

interface AuthRepositoryInterface
{
    public function authenticatePassport(string $origin, string $passportToken): ?PassportStampDto;
}
