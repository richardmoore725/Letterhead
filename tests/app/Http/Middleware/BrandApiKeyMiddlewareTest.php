<?php

namespace App\Tests;

use App\DTOs\BrandDto;
use App\Http\Middleware\BrandApiKeyMiddleware;
use App\Http\Controllers\BrandController;
use App\Http\Services\BrandServiceInterface;
use App\Models\Brand;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\ParameterBag;


class BrandApiKeyMiddlewareTest extends TestCase
{
    private $brandService;
    private $request;
    private $middleware;

    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->middleware = new BrandApiKeyMiddleware($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotGetABearerToken_returns400JsonResponse()
    {
        $closure = function() {
            return function() {
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
        $this->assertEquals('Oops. Remember to send your brand Api key as a bearer token.', $actualResults->getContent());
    }

    public function testCannotGetBrand_returns404JsonResponse()
    {
        $token = 'testToken';
        $closure = function() {
            return function() {
                return 'hello!';
            };
        };
        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "brandApiKey" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "brandSlug" => "testBrandSlug",
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn('testToken');

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with('testBrandSlug')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('We couldn\'t find this brand.', $actualResults->getContent());
    }

    public function testBearerTokenIsNotEqualToBrandApiKey__returns403JsonResponse()
    {
        $brandDto = new BrandDto();
        $brandDto->id = 5;
        $brand = new Brand($brandDto);
        $token = 'testToken';
        $closure = function() {
            return function() {
                return 'hello!';
            };
        };
        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "brandApiKey" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "brandSlug" => "testBrandSlug",
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn('testToken');

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with('testBrandSlug')
            ->willReturn($brand);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandApiKeyByBrandId')
            ->with(5)
            ->willReturn('brandApiKey');

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
        $this->assertEquals('Your brand Api key is unauthorized.', $actualResults->getContent());
    }

    public function testBearerToken_returnsClosure() {
        $brandDto = new BrandDto();
        $brandDto->id = 5;
        $brand = new Brand($brandDto);
        $token = 'testToken';
        $closure = function() {
            return function() {
                return 'hello!';
            };
        };
        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "brandApiKey" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "brandSlug" => "testBrandSlug",
                "channelSlug" => "testChannelSlug"
            ]
        ];
        $parameterBag = $this->createMock(ParameterBag::class);
        $this->request->request = $parameterBag;

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn($token);

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with('testBrandSlug')
            ->willReturn($brand);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandApiKeyByBrandId')
            ->with(5)
            ->willReturn($token);

        $this->request->request
            ->expects($this->once())
            ->method('set')
            ->with('brand', $brand);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}
