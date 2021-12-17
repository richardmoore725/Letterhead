<?php

namespace App\Tests;

use App\Http\Middleware\AuthorizeUserActionMiddleware;
use App\Http\Services\AuthServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\PassportStamp;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\ParameterBag;

class AuthorizeUserActionMiddlewareTest extends TestCase
{
    private $authService;
    private $userService;

    public function setUp() : void
    {
        $this->authService = $this->createMock(AuthServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->middleware = new AuthorizeUserActionMiddleware($this->authService, $this->userService);
        $this->request = $this->createMock(Request::class);
        $this->headerBag = $this->createMock(HeaderBag::class);
    }

    public function testDoesntHaveOrigin__return400JsonResponse()
    {
        $token = 'testToken';

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $model = 'channel';
        $action = 'read';
        $resourceIdType = 'channelId';

        $this->request->headers = $this->headerBag;

        $this->headerBag
            ->expects($this->once())
            ->method('has')
            ->with('Origin')
            ->willReturn(false);

        $actualResults = $this->middleware->handle($this->request, $closure, $model, $action, $resourceIdType);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('No origin detected', $actualResults->getContent());
    }

    public function testDoesntHaveBearerToken__return400JsonResponse()
    {
        $token = 'testToken';

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $model = 'channel';
        $action = 'read';
        $resourceIdType = 'channelId';

        $this->request->headers = $this->headerBag;

        $this->headerBag
            ->expects($this->once())
            ->method('has')
            ->with('Origin')
            ->willReturn(true);

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure, $model, $action, $resourceIdType);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('Oops. Remember to send your passport as a bearer token.', $actualResults->getContent());
    }

    public function testDoesntHavePassportStamp__return400JsonResponse()
    {
        $token = 'testToken';

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $model = 'channel';
        $action = 'read';
        $resourceIdType = 'channelId';

        $this->request
            ->expects($this->any())
            ->method('bearerToken')
            ->willReturn($token);

        $this->request->headers = $this->headerBag;

        $this->headerBag
            ->expects($this->once())
            ->method('has')
            ->with('Origin')
            ->willReturn('https://google.com');

        $this->headerBag
            ->expects($this->once())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with('https://google.com', $token)
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure, $model, $action, $resourceIdType);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(401, $actualResults->getStatusCode());
        $this->assertEquals('Your passport may have expired.', $actualResults->getContent());
    }

    public function testCannotPerformAction__return401JsonResponse()
    {
        $token = 'testToken';
        $origin = 'https://google.com';
        $passportStamp = $this->createMock(PassportStamp::class);
        $parameterBag = $this->createMock(ParameterBag::class);
        $this->request->request = $parameterBag;

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $model = 'channel';
        $action = 'read';
        $resourceIdType = 'channelId';

        $this->request
            ->expects($this->any())
            ->method('bearerToken')
            ->willReturn($token);

        $this->request->headers = $this->headerBag;

        $this->headerBag
            ->expects($this->once())
            ->method('has')
            ->with('Origin')
            ->willReturn($origin);

        $this->headerBag
            ->expects($this->once())
            ->method('get')
            ->with('origin')
            ->willReturn($origin);

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with($origin, $token)
            ->willReturn($passportStamp);

        $mockRoute = [
                [1],
                [
                    "middleware" =>
                    [ "passport" ],
                    "uses" =>
                    [ "uses"]
                ],
                [
                    "channelId" => "1"
                ]
            ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with($action, $model, $passportStamp, 1)
            ->willReturn(false);

        $actualResults = $this->middleware->handle($this->request, $closure, $model, $action, $resourceIdType);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(401, $actualResults->getStatusCode());
    }

    public function testCanSetPassportStamp__return400JsonResponse()
    {
        $token = 'testToken';
        $origin = 'https://google.com';
        $passportStamp = $this->createMock(PassportStamp::class);
        $parameterBag = $this->createMock(ParameterBag::class);
        $this->request->request = $parameterBag;

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $model = 'channel';
        $action = 'read';
        $resourceIdType = 'channelId';

        $this->request
            ->expects($this->any())
            ->method('bearerToken')
            ->willReturn($token);

        $this->request->headers = $this->headerBag;

        $this->headerBag
            ->expects($this->once())
            ->method('has')
            ->with('Origin')
            ->willReturn($origin);

        $this->headerBag
            ->expects($this->once())
            ->method('get')
            ->with('origin')
            ->willReturn($origin);

        $this->authService
            ->expects($this->once())
            ->method('authenticatePassport')
            ->with($origin, $token)
            ->willReturn($passportStamp);

        $mockRoute = [
                [1],
                [
                    "middleware" =>
                    [ "passport" ],
                    "uses" =>
                    [ "uses"]
                ],
                [
                    "channelId" => "1"
                ]
            ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with($action, $model, $passportStamp, 1)
            ->willReturn(true);

        $this->request->request
            ->expects($this->once())
            ->method('set')
            ->with('passportStamp', $passportStamp);

        $actualResults = $this->middleware->handle($this->request, $closure, $model, $action, $resourceIdType);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}
