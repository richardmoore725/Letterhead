<?php

namespace App\Http\Services;

use App\Collections\UserPermissionCollection;
use App\Collections\UserCollection;
use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Repositories\UserRepositoryInterface;
use App\Models\PassportStamp;
use App\Models\User;
use App\DTOs\UserDto;

class UserService implements UserServiceInterface
{
    /**
     * @var BeaconRepositoryInterface
     */
    private $beaconRepository;

    /**
     * @var UserRepositoryInterface
     */
    private $userRepository;

    public function __construct(BeaconRepositoryInterface $beaconRepository, UserRepositoryInterface $userRepository)
    {
        $this->beaconRepository = $beaconRepository;
        $this->userRepository = $userRepository;
    }

    public function checkWhetherUserCanPerformAction(string $action, string $model, PassportStamp $passport, int $resourceId): bool
    {
        $response = $this->userRepository->checkWhetherUserCanPerformAction($action, $model, $passport, $resourceId);

        if ($response->isError()) {
            return false;
        }

        return $response->getBooleanFromResponseBody();
    }

    public function getOrCreateAndChargeUser(
        int $applicationFeeAmount,
        string $connectedStripeAccountId,
        string $description,
        int $finalPriceOfPackage,
        string $paymentMethod,
        string $userEmail,
        string $userName
    ): ?User {
        $userDto = $this->userRepository->getOrCreateAndChargeUser(
            $applicationFeeAmount,
            $connectedStripeAccountId,
            $description,
            $finalPriceOfPackage,
            $paymentMethod,
            $userEmail,
            $userName
        );

        if (empty($userDto)) {
            return null;
        }

        return new User($userDto);
    }

    public function getPermissionsByUserId(PassportStamp $passport): UserPermissionCollection
    {
        $api = "{$this->getServiceEndpoint()}/users/{$passport->getId()}/permissions";
        $permissions = $this->beaconRepository->getResourceFromService($api, $passport->getToken());

        return new UserPermissionCollection(collect($permissions));
    }

    public function createScaffoldResource(string $model, int $resourceId): bool
    {
        $api = "{$this->getServiceEndpoint()}/permissions";
        $serviceKey = $this->getServiceKey();
        $signal = [
            'model' => $model,
            'resourceId' => $resourceId,
        ];

        $permissions = $this->beaconRepository->createBrandChannelResourceFromService(
            $api,
            $serviceKey,
            '',
            '',
            $signal,
            false
        );

        if (empty($permissions)) {
            return false;
        }

        return filter_var($permissions, FILTER_VALIDATE_BOOLEAN);
    }

    private function getServiceEndpoint(): string
    {
        return env('SERVICE_USERS_ENDPOINT');
    }

    private function getServiceKey(): string
    {
        return env('SERVICE_USERS_KEY');
    }

    public function getUserById(int $userId): ?User
    {
        $api = "{$this->getServiceEndpoint()}/users/{$userId}";
        $serviceKey = $this->getServiceKey();

        $userObject = $this->beaconRepository->getResourceFromService($api, $serviceKey);

        if (empty($userObject)) {
            return null;
        }

        $userDto = new UserDto($userObject);

        return new User($userDto);
    }

    public function updateUser(
        $email,
        $name,
        $surname,
        $userId
    ): ?User {
        $api = "{$this->getServiceEndpoint()}/users/{$userId}";
        $serviceKey = $this->getServiceKey();
        $signal = [
            'email' => $email,
            'name' => $name,
            'surname' => $surname
        ];

        $userObject = $this->beaconRepository->createBrandChannelResourceFromService(
            $api,
            $serviceKey,
            '',
            '',
            $signal,
            false
        );

        if (empty($userObject)) {
            return null;
        }

        $userDto = new UserDto($userObject);

        return new User($userDto);
    }

    private function getUsersByPermission(string $action, string $resource, int $resourceId): UserCollection
    {
        $api = "{$this->getServiceEndpoint()}/permissions/users";
        $serviceKey = $this->getServiceKey();
        $signal = [
            'action' => $action,
            'resource' => $resource,
            'resourceId' => $resourceId
        ];

        $userObjects = $this->beaconRepository->getResourceFromServiceWithRequestData($api, $serviceKey, $signal, false);

        if (empty($userObjects)) {
            return new UserCollection([]);
        }

        $users = array_map(function (object $userObject) {
            $userDto = new UserDto($userObject);
            return new User($userDto);
        }, $userObjects);

        return new UserCollection($users);
    }

    public function getBrandAdministrators(int $brandId): UserCollection
    {
        $users = $this->getUsersByPermission(
            'create',
            'brand',
            $brandId
        );

        return $users;
    }

    public function getUsersByUserIds(array $ids): UserCollection
    {
        $api = "{$this->getServiceEndpoint()}/users";
        $serviceKey = $this->getServiceKey();
        $signal = ['userIds' => $ids];

        $userObjects = $this->beaconRepository->getResourceFromServiceWithRequestData($api, $serviceKey, $signal, false);

        if (empty($userObjects)) {
            return new UserCollection([]);
        }

        $users = array_map(function (object $userObject) {
            $userDto = new UserDto($userObject);
            return new User($userDto);
        }, $userObjects);

        return new UserCollection($users);
    }
}
