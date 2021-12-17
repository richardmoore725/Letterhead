<?php

namespace App\DTOs;

use App\Models\User;

class UserDto
{
    public $createdAt;
    public $email;
    public $id;
    public $name;
    public $surname;

    public function __construct(\stdClass $object = null, User $user = null)
    {
        if (!empty($object)) {
            $this->createdAt = isset($object->created_at) ? $object->created_at : '';
            $this->email = isset($object->email) ? $object->email : '';
            $this->id = isset($object->id) ? $object->id : null;
            $this->name = isset($object->name) ? $object->name : '';
            $this->surname = isset($object->surname) ? $object->surname : '';
        }

        if (!empty($user)) {
            $this->createdAt = $user->getCreatedAt();
            $this->email = $user->getEmail();
            $this->id = $user->getId();
            $this->name = $user->getName();
            $this->surname = $user->getSurname();
        }
    }
}
