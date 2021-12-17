<?php

namespace App\Http\Controllers;

use App\Http\Services\AuthServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\User;
use App\Models\PassportStamp;
use App\Collections\UserPermissionCollection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    private $authService;
    private $userService;

    public function __construct(
        AuthServiceInterface $authService,
        UserServiceInterface $userService
    ) {
        $this->authService = $authService;
        $this->userService = $userService;
    }

    public function getUserFromPassportStamp(Request $request): JsonResponse
    {
        $origin = $request->headers->get('origin', '');
        $passportStamp = $this->authService->authenticatePassport($origin, $request->bearerToken());

        if (empty($passportStamp)) {
            return response()->json('This passportStamp doesn\'t exist.', 404);
        }

        $user = $this->userService->getUserById($passportStamp->getId());

        if (empty($user)) {
            return response()->json('This user doesn\'t exist.', 404);
        }

        return response()->json($user->convertToArray());
    }

    public function getUserById(int $brandId, int $channelId, UserPermissionCollection $permissions, int $customerId): JsonResponse
    {
        if (!$permissions->canUserAdministrateChannel($brandId, $channelId, $permissions)) {
            return response()->json('This user is not authorized.', 403);
        }

        $user = $this->userService->getUserById($customerId);

        if (empty($user)) {
            return response()->json('This user doesn\'t exist.', 404);
        }

        return response()->json($user->convertToArray());
    }

    public function updateUser(
        PassportStamp $passport,
        int $userId,
        Request $request
    ): JsonResponse {
        $isAuthorizedUser = $passport->getId() === $userId;

        if (!$isAuthorizedUser) {
            return response()->json('This user is not authorized.', 403);
        }

        $emailToUpdate = $request->input('email');
        $nameToUpdate = $request->input('name');
        $surnameToUpdate = $request->input('surname');

        $updatedUser = $this->userService->updateUser(
            $emailToUpdate,
            $nameToUpdate,
            $surnameToUpdate,
            $userId
        );

        if (empty($updatedUser)) {
            return response()->json('Sorry, we were unable to update this user.', 500);
        }

        return response()->json($updatedUser->convertToArray(), 200);
    }
}
