<?php

namespace App\Tests;

use App\Http\Middleware\VerifyBrandMiddleware;
use App\Models\Brand;
use App\Http\Services\BrandServiceInterface;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyBrandMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->middleware = new VerifyBrandMiddleware($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotGetBrand_returns404JsonResponse()
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
                    [ 'brand' ],
                'uses' =>
                    [ 'uses' ]
            ],
            [
                'brandId' => '14'
            ]
        ];


        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('Are you sure that brand exists?', $actualResults->getContent());
    }

    public function testCanGetBrand_returnsClosure()
    {
        $brand = $this->createMock(Brand::class);

        $closure = function() {
            return function() {
              return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                'middleware' =>
                    [ 'brand' ],
                'uses' =>
                    [ 'uses' ]
            ],
            [
                'brandId' => '14'
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->request
            ->expects($this->once())
            ->method('setRouteResolver')
            ->willReturn($this->request);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(\Closure::class, $actualResults);
    }
}