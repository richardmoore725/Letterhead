<?php

namespace App\Tests;

use App\Http\Controllers\AuthController;
use App\Http\Services\AuthServiceInterface;
use App\Http\Services\UserServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\HeaderBag;
use App\Models\PassportStamp;
use App\DTOs\PassportStampDto;
use App\Models\User;
use App\DTOs\UserDto;
use App\Collections\UserPermissionCollection;

class AuthControllerTest extends TestCase
{
    private $authService;
    private $userService;
    private $controller;
    private $request;
    private $headerBag;

    public function setUp() : void
    {
        $this->authService = $this->createMock(AuthServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->controller = new AuthController($this->authService, $this->userService);
        $this->request = $this->createMock(Request::class);
        $this->headerBag = $this->createMock(HeaderBag::class);
    }

    public function testCannotGetUserFromPassportStamp_noPassporst_returns404JsonResponse()
    {
        $this->request->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn('bearerToken');

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with('https://google.com', 'bearerToken')
            ->willReturn(null);

        $actualResults = $this->controller->getUserFromPassportStamp($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCannotGetUserFromPassportStamp__noUser_returns404JsonResponse()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->id = 4;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passportStamp = new PassportStamp($dto);

        $this->request->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn('bearerToken');

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with('https://google.com', 'bearerToken')
            ->willReturn($passportStamp);

        $this->userService
            ->expects($this->once())
            ->method('getUserById')
            ->with($passportStamp->getId())
            ->willReturn(null);

        $actualResults = $this->controller->getUserFromPassportStamp($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetUserFromPassportStamp_returns404JsonResponse()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->id = 4;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passportStamp = new PassportStamp($dto);

        $user = $this->createMock(User::class);

        $this->request->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn('bearerToken');

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with('https://google.com', 'bearerToken')
            ->willReturn($passportStamp);

        $this->userService
            ->expects($this->once())
            ->method('getUserById')
            ->with($passportStamp->getId())
            ->willReturn($user);

        $actualResults = $this->controller->getUserFromPassportStamp($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetUserById_return403JsonResponse()
    {
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $userPermissionCollection
        ->expects($this->once())
        ->method('canUserAdministrateChannel')
        ->willReturn(false);

        $actualResults = $this->controller->getUserById(1, 1, $userPermissionCollection, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotGetUserById_return404JsonResponse()
    {
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $userPermissionCollection
        ->expects($this->once())
        ->method('canUserAdministrateChannel')
        ->willReturn(true);

        $this->userService
        ->expects($this->once())
        ->method('getUserById')
        ->with(1)
        ->willReturn(null);

        $actualResults = $this->controller->getUserById(1, 1, $userPermissionCollection, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetUserById_returnJsonResponse()
    {
        $userObject = new \stdClass();
        $userObject->email = 'hello@whereby.us';
        $userObject->name = 'whereby.us';

        $dto = new UserDto($userObject);
        $user = new User($dto);

        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $userPermissionCollection
        ->expects($this->once())
        ->method('canUserAdministrateChannel')
        ->willReturn(true);

        $this->userService
        ->expects($this->once())
        ->method('getUserById')
        ->with(1)
        ->willReturn($user);

        $actualResults = $this->controller->getUserById(1, 1, $userPermissionCollection, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotUpdateUser_unauthorizedUser_returns403()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->id = 4;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passportStamp = new PassportStamp($dto);

        $actualResults = $this->controller->updateUser($passportStamp, 1, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotUpdateUser_returns500()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->surname = 'Schoefield';
        $passportObject->user->id = 4;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passportStamp = new PassportStamp($dto);

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->willReturn('michael@whereby.us');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->willReturn('Michael');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->willReturn('Schoefield');

        $this->userService
            ->expects($this->once())
            ->method('updateUser')
            ->with('michael@whereby.us', 'Michael', 'Schoefield', 4)
            ->willReturn(null);

        $actualResults = $this->controller->updateUser($passportStamp, 4, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateUser_returns200()
    {
        $passportObject = new \stdClass();
        $passportObject->acc = '123';
        $passportObject->exp = '1213232323';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'michael@whereby.us';
        $passportObject->user->name = 'Michael';
        $passportObject->user->surname = 'Schoefield';
        $passportObject->user->id = 4;
        $passportObject->user->permissions = [];
        $passportObject->ref = '2j29292';

        $dto = new PassportStampDto($passportObject, '123');
        $passportStamp = new PassportStamp($dto);

        $userObject = new \stdClass();
        $userObject->email = 'hello@whereby.us';
        $userObject->name = 'Michael';
        $userObject->surname = 'Schoefield';

        $dto = new UserDto($userObject);
        $user = new User($dto);

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->willReturn('michael@whereby.us');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->willReturn('Michael');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->willReturn('Schoefield');

        $this->userService
            ->expects($this->once())
            ->method('updateUser')
            ->with('michael@whereby.us', 'Michael', 'Schoefield', 4)
            ->willReturn($user);

        $actualResults = $this->controller->updateUser($passportStamp, 4, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}
