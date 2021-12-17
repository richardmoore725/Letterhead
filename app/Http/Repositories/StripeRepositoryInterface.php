<?php

namespace App\Http\Repositories;

use App\DTOs\StripeConnectedAccountDto;

interface StripeRepositoryInterface
{
    public function connectStripeAccount(string $code): ?StripeConnectedAccountDto;
}
