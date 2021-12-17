<?php

namespace App\Tests;

use App\Collections\UserCollection;
use App\DTOs\UserDto;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class UserCollectionTest extends TestCase
{
    private $userCollection;

    public function setUp() : void
    {
        $this->userCollection = new UserCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new UserDto(new \stdClass);
        $dto->email = 'hello@whereby.us';
        $dto->id = 1;
        $dto->name = 'jun';
        $dto->surname = 'su';
        $dto->created_at = '2020-09-29';

        $arrayOfUserDtos = [ $dto ];

        $this->userCollection = new UserCollection($arrayOfUserDtos);

        $actualResults = $this->userCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $userObject = new \stdClass();
       $userObject->email = 'hello@whereby.us';
       $userObject->id = 1;
       $userObject->name = 'jun';
       $userObject->surname = 'su';
       $userObject->created_at = '2020-09-29';
       
       $userObjectsFromDatabase = collect([ $userObject ]);

       $this->userCollection = new UserCollection($userObjectsFromDatabase);

       $actualResults = $this->userCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }

    public function testCanGetArrayOfUserFullNames()
    {
        $dto = new UserDto(new \stdClass);
        $dto->email = 'hello@whereby.us';
        $dto->id = 1;
        $dto->name = 'jun';
        $dto->surname = 'su';
        $dto->created_at = '2020-09-29';
        $userModel = new User($dto);

        $arrayOfUserModels = [ $userModel ];
        $this->userCollection = new UserCollection($arrayOfUserModels);

        $actualResults = $this->userCollection->getArrayOfUserFullNames();
        $this->assertIsArray($actualResults);
    }
}
