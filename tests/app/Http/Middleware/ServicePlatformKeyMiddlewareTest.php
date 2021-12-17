<?php

namespace App\Tests;

use App\Http\Middleware\ServicePlatformKeyMiddleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServicePlatformKeyMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->middleware = new ServicePlatformKeyMiddleware();
        $this->request = $this->createMock(Request::class);
    }

    public function testDoesNotHaveBearerToken__returns400JsonResponse()
    {
        $closure = function() {
            return function () {
                return 'hello!';
            };
        };

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('Oops. Remember to send your service platform key as a bearer token.', $actualResults->getContent());
    }

    public function testBearerTokenDoesNotMatchServicePlatformKey__returns403JsonResponse()
    {
        $token = 'testToken';

        $closure = function() {
            return function () {
                return 'hello!';
            };
        };

        $this->request
        ->expects($this->once())
        ->method('bearerToken')
        ->willReturn($token);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
        $this->assertEquals('Your service platform key is unauthorized.', $actualResults->getContent());
    }

    public function testCanGetServiePlatformKey__returnsClosure()
    {
        $token = 'randomServicePlatformKey';

        $closure = function() {
            return function () {
                return 'hello!';
            };
        };

        $this->request
        ->expects($this->once())
        ->method('bearerToken')
        ->willReturn($token);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}