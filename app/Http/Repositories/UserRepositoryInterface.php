<?php

namespace App\Http\Repositories;

use App\DTOs\UserDto;
use App\Http\Response;
use App\Models\PassportStamp;

interface UserRepositoryInterface
{
    public function checkWhetherUserCanPerformAction(
        string $action,
        string $model,
        PassportStamp $passport,
        int $resourceId
    ): Response;

    public function getOrCreateAndChargeUser(
        int $applicationFeeAmount,
        string $connectedStripeAccountId,
        string $description,
        int $finalPriceOfPackage,
        string $paymentMethod,
        string $userEmail,
        string $userName
    ): ?UserDto;
}
