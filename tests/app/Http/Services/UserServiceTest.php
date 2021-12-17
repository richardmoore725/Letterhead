<?php

namespace App\Tests\Http;

use App\Collections\UserPermissionCollection;
use App\Collections\UserCollection;
use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Repositories\UserRepositoryInterface;
use App\Http\Response;
use App\Http\Services\UserService;
use App\Models\PassportStamp;
use App\DTOs\PassportStampDto;
use App\Models\User;
use App\DTOs\UserDto;
use App\Tests\TestCase;

class UserServiceTest extends TestCase
{
    private $beaconRepository;
    private $userRepository;
    private $service;

    public function setUp(): void
    {
        $this->beaconRepository = $this->createMock(BeaconRepositoryInterface::class);
        $this->userRepository = $this->createMock(UserRepositoryInterface::class);
        $this->service = new UserService($this->beaconRepository, $this->userRepository);
    }

    public function testCannotGetOrCreateAndChargeUser_returnsNull()
    {
        $applicationFeeAmount = 200;
        $connectedStripeAccountId = "";
        $description = "";
        $finalPriceOfPackage = 1000;
        $paymentMethod = "pm_fake_card";
        $userEmail = "testing@fake.url";
        $userName = "Charles Testing";

        $this->userRepository
            ->expects($this->once())
            ->method('getOrCreateAndChargeUser')
            ->willReturn(null);

        $actualResults = $this->service->getOrCreateAndChargeUser(
            $applicationFeeAmount,
            $connectedStripeAccountId,
            $description,
            $finalPriceOfPackage,
            $paymentMethod,
            $userEmail,
            $userName
        );

        $this->assertEmpty($actualResults);
    }

    public function testCanGetOrCreateAndChargeUser_returnsUser()
    {
        $applicationFeeAmount = 200;
        $connectedStripeAccountId = "";
        $description = "";
        $finalPriceOfPackage = 1000;
        $paymentMethod = "pm_fake_card";
        $userEmail = "testing@fake.url";
        $userName = "Charles Testing";
        $userDto = $this->createMock(UserDto::class);

        $this->userRepository
            ->expects($this->once())
            ->method('getOrCreateAndChargeUser')
            ->willReturn($userDto);

        $actualResults = $this->service->getOrCreateAndChargeUser(
            $applicationFeeAmount,
            $connectedStripeAccountId,
            $description,
            $finalPriceOfPackage,
            $paymentMethod,
            $userEmail,
            $userName
        );

        $this->assertInstanceOf(User::class, $actualResults);
    }

    public function testCanGetPermissionsByUserId_returnsUserPermissionCollection()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->createdAt = '2020-09-29';
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->surname = 'S';
        $passportObject->user->id = 5;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passport = new PassportStamp($dto);

        $permissions = ['wee'];
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromService')
            ->with('https://userservice.local/api/v1/users/5/permissions', '123')
            ->willReturn($permissions);

        $actualResults = $this->service->getPermissionsByUserId($passport);

        $this->assertInstanceOf(UserPermissionCollection::class, $actualResults);
    }

    public function testCanCreateScaffoldResource_returnsTrue()
    {
        $api = 'https://userservice.local/api/v1/permissions';
        $serviceKey = 'wee';
        $signal = [
            'model' => 'brand',
            'resourceId' => 1,
        ];

        $this->beaconRepository
            ->expects($this->once())
            ->method('createBrandChannelResourceFromService')
            ->with($api, $serviceKey, '', '', $signal, false)
            ->willReturn(true);

        $actualResults = $this->service->createScaffoldResource('brand', 1);

        $this->assertTrue($actualResults);
    }

    public function testCanGetUserById_returnUserModel()
    {
        $api = 'https://userservice.local/api/v1/users/1';
        $serviceKey = 'wee';

        $userObject = new \stdClass();
        $userObject->email = 'hello@whereby.us';
        $userObject->name = 'whereby.us';

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromService')
            ->with($api, $serviceKey)
            ->willReturn($userObject);

        $dto = new UserDto($userObject);
        $user = new User($dto);

        $actualResults = $this->service->getUserById(1);

        $this->assertEquals($user, $actualResults);
    }

    public function testCannotGetUserById_returnNull()
    {
        $api = 'https://userservice.local/api/v1/users/1';
        $serviceKey = 'wee';

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromService')
            ->with($api, $serviceKey)
            ->willReturn(null);

        $actualResults = $this->service->getUserById(1);

        $this->assertNull($actualResults);
    }

    public function testCannotUpdateUser_returnsNull()
    {
        $api = 'https://userservice.local/api/v1/users/1';
        $serviceKey = 'wee';
        $signal = [
            'email' => 'testEmail',
            'name' => 'testName',
            'surname' => 'testSurname'
        ];

        $this->beaconRepository
            ->expects($this->once())
            ->method('createBrandChannelResourceFromService')
            ->with($api, $serviceKey, '', '', $signal, false)
            ->willReturn(null);

        $actualResults = $this->service->updateUser('testEmail', 'testName', 'testSurname', 1);

        $this->assertNull($actualResults);
    }

    public function testCanUpdateUser_returnsUser()
    {
        $api = 'https://userservice.local/api/v1/users/1';
        $serviceKey = 'wee';
        $signal = [
            'email' => 'testEmail',
            'name' => 'testName',
            'surname' => 'testSurname'
        ];

        $userObject = new \stdClass();
        $userObject->createdAt = '2020-09-29';
        $userObject->email = 'hello@whereby.us';
        $userObject->id = 1;
        $userObject->name = 'Michael';
        $userObject->surname = 'Schoefield';

        $this->beaconRepository
            ->expects($this->once())
            ->method('createBrandChannelResourceFromService')
            ->with($api, $serviceKey, '', '', $signal, false)
            ->willReturn($userObject);

        $actualResults = $this->service->updateUser('testEmail', 'testName', 'testSurname', 1);

        $this->assertInstanceOf(User::class, $actualResults);
    }

    public function testCanGetBrandAdministrators_returnUserCollection()
    {
        $api = 'https://userservice.local/api/v1/permissions/users';
        $serviceKey = 'wee';
        $signal = [
            'action' => 'create',
            'resource' => 'brand',
            'resourceId' => 1
        ];

        $userObject = new \stdClass();
        $userObject->email = 'hello@whereby.us';
        $userObject->name = 'jun';
        $userObject->surname = 'su';
        $userObject->id = 1;
        $userObject->createdAt = '2020-09-29';

        $userObjects = [$userObject, ];

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromServiceWithRequestData')
            ->with($api, $serviceKey, $signal, false)
            ->willReturn($userObjects);

        $actualResults = $this->service->getBrandAdministrators(1);

        $this->assertInstanceOf(UserCollection::class, $actualResults);
    }

    public function testCannotGetUsersByPermission_returnEmptyUserCollection()
    {
        $api = 'https://userservice.local/api/v1/permissions/users';
        $serviceKey = 'wee';
        $signal = [
            'action' => 'create',
            'resource' => 'brand',
            'resourceId' => 1
        ];

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromServiceWithRequestData')
            ->with($api, $serviceKey, $signal, false)
            ->willReturn(null);

            $actualResults = $this->service->getBrandAdministrators(1);

            $this->assertInstanceOf(UserCollection::class, $actualResults);
    }

    public function testCanGetUsersByUserIds_returnUserCollection()
    {
        $api = 'https://userservice.local/api/v1/users';
        $serviceKey = 'wee';
        $signal = [
            'userIds' => [1 ,2]
        ];

        $userObject1 = new \stdClass();
        $userObject1->email = 'junsu@whereby.us';
        $userObject1->name = 'jun';
        $userObject1->surname = 'su';
        $userObject1->id = 1;
        $userObject1->createdAt = '2020-09-29';

        $userObject2 = new \stdClass();
        $userObject2->email = 'jojo@whereby.us';
        $userObject2->name = 'jojo';
        $userObject2->surname = 'pico';
        $userObject2->id = 2;
        $userObject2->createdAt = '2020-10-29';

        $userObjects = [$userObject1, $userObject2];

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromServiceWithRequestData')
            ->with($api, $serviceKey, $signal, false)
            ->willReturn($userObjects);

        $actualResults = $this->service->getUsersByUserIds([1, 2]);

        $this->assertInstanceOf(UserCollection::class, $actualResults);
    }

    public function testCanGetUsersByUserIds_returnEmptyUserCollection()
    {
        $api = 'https://userservice.local/api/v1/users';
        $serviceKey = 'wee';
        $signal = [
            'userIds' => [1 ,2]
        ];

        $userObjects = [];

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromServiceWithRequestData')
            ->with($api, $serviceKey, $signal, false)
            ->willReturn($userObjects);

        $actualResults = $this->service->getUsersByUserIds([1, 2]);

        $this->assertInstanceOf(UserCollection::class, $actualResults);
    }

    public function testCanCheckWhetherUserCanPerformAction_returnsTrue()
    {
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->userRepository
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'channel', $passport, 3)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $response->expects($this->once())
            ->method('getBooleanFromResponseBody')
            ->willReturn(true);

        $actualResults = $this->service->checkWhetherUserCanPerformAction(
            'create',
            'channel',
            $passport,
            3
        );

        $this->assertTrue($actualResults);
    }

    public function testCanCheckWhetherUserCanPerformAction_returnsFalse()
    {
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->userRepository
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'channel', $passport, 3)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->service->checkWhetherUserCanPerformAction(
            'create',
            'channel',
            $passport,
            3
        );

        $this->assertFalse($actualResults);
    }
}
