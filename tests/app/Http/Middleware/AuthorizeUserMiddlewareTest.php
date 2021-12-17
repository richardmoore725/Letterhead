<?php

namespace App\Tests;

use App\Http\Middleware\AuthorizeUserMiddleware;
use App\Http\Services\UserServiceInterface;
use App\Collections\UserPermissionCollection;
use App\Models\PassportStamp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthorizeUserMiddlewareTest extends TestCase
{

    private $userService;

    public function setUp() : void
    {
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->middleware = new AuthorizeUserMiddleware($this->userService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotAuthorizeUser_returns500JsonResponse()
    {
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $passportStamp = $this->createMock(PassportStamp::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "passport" ],
                "uses" =>
                [ "uses" ]
            ],
            [
                "passport" => null
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
        $this->assertEquals('', $actualResults->getContent());
    }

    public function testCanAuthorizeUser_returnsClosure()
    {
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);
        $passportStamp = $this->createMock(PassportStamp::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "passport" ],
                "uses" =>
                [ "uses" ]
            ],
            [
                "passport" => $passportStamp
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->userService
            ->expects($this->once())
            ->method('getPermissionsByUserId')
            ->willReturn($userPermissionCollection);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}