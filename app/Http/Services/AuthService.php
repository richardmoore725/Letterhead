<?php

namespace App\Http\Services;

use App\Http\Repositories\AuthRepositoryInterface;
use App\Models\PassportStamp;
use Illuminate\Http\Request;

class AuthService implements AuthServiceInterface
{
    private $repository;

    public function __construct(AuthRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function authenticatePassport(string $origin, string $token): ?PassportStamp
    {
        $dto = $this->repository->authenticatePassport($origin, $token);
        if (empty($dto)) {
            return null;
        }

        return new PassportStamp($dto);
    }

    /**
     * @deprecated
     */

    public function authorizeActionFromPassportStamp(
        PassportStamp $passportStamp,
        string $action,
        string $resource,
        int $resourceId
    ): bool {
        $passportAuthorizesAction = false;

        foreach ($passportStamp->getPermissions() as $permission) {
            if (
                $permission['action'] === $action &&
                $permission['resource'] === $resource &&
                $permission['resourceId'] === $resourceId
            ) {
                $passportAuthorizesAction = true;
                break;
            }
        }

        return $passportAuthorizesAction;
    }
}
