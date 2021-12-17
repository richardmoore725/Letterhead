<?php

namespace App\Collections;

use App\DTOs\UserDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class UserCollection extends BaseCollection
{
    public function __construct($users = [])
    {
        parent::__construct($users);
    }

    private function getDtos(array $arrayOfuserObjects): array
    {
        return array_map(function ($userObject) {
            if (is_a($userObject, UserDto::class)) {
                return $userObject;
            }

            if (is_a($userObject, User::class)) {
                return new UserDto(null, $userObject);
            }

            return new UserDto($userObject);
        }, $arrayOfuserObjects);
    }

    private function getModels(array $userDtosOrObjects): array
    {
        $dtos = $this->getDtos($userDtosOrObjects);

        return array_map(function (UserDto $dto) {
            return new User($dto);
        }, $dtos);
    }

    public function getPublicArray(): array
    {
        $userModels = $this->getModels($this->items);

        return array_map(function (User $user) {
            return $user->convertToArray();
        }, $userModels);
    }

    public function getArrayOfUserFullNames(): array
    {
        $arrayOfUserModels = $this->toArray();

        return array_map(function (User $user) {
            return $user->getFullName();
        }, $arrayOfUserModels);
    }
}
