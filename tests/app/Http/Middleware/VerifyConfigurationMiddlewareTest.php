<?php

namespace App\Tests;

use App\Http\Middleware\VerifyConfigurationMiddleware;
use App\Http\Services\BrandServiceInterface;
use App\Models\Configuration;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyConfigurationMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->middleware = new VerifyConfigurationMiddleware($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotFindConfiguration__returns404JsonResponse()
    {
        $closure = function() {
            return function() {
              return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                'middleware' =>
                    [ 'verifyConfiguration' ],
                'uses' =>
                    [ 'uses' ]
            ],
            [
                'configurationSlug' => ''
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getConfigurationBySlug')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('This setting does not exist', $actualResults->getContent());
    }

    public function testCanFindConfiguration__returns404JsonResponse()
    {
        $configuration = $this->createMock(Configuration::class);

        $closure = function() {
            return function() {
              return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                'middleware' =>
                    [ 'verifyConfiguration' ],
                'uses' =>
                    [ 'uses' ]
            ],
            [
                'configurationSlug' => ''
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getConfigurationBySlug')
            ->willReturn($configuration);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}