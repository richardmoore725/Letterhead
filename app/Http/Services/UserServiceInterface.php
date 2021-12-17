<?php

namespace App\Http\Services;

use App\Collections\UserPermissionCollection;
use App\Collections\UserCollection;
use App\Models\PassportStamp;
use App\Models\User;

interface UserServiceInterface
{
    public function checkWhetherUserCanPerformAction(string $action, string $model, PassportStamp $passport, int $resourceId): bool;
    public function getPermissionsByUserId(PassportStamp $passport): UserPermissionCollection;
    public function createScaffoldResource(string $model, int $resourceId): bool;
    public function getOrCreateAndChargeUser(
        int $applicationFeeAmount,
        string $connectedStripeAccountId,
        string $description,
        int $finalPriceOfPackage,
        string $paymentMethod,
        string $userEmail,
        string $userName
    ): ?User;
    public function getUserById(int $userId): ?User;
    public function updateUser(
        $email,
        $name,
        $surname,
        $userId
    ): ?User;
    public function getBrandAdministrators(int $brandId): UserCollection;
    public function getUsersByUserIds(array $ids): UserCollection;
}
