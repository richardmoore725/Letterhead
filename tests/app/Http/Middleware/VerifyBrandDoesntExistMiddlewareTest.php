<?php

namespace App\Tests;

use App\Http\Middleware\VerifyBrandDoesntExistMiddleware;
use App\Http\Services\BrandServiceInterface;
use App\Models\Brand;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerifyBrandDoesntExistMiddlwareTest extends TestCase
{
    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->middleware = new VerifyBrandDoesntExistMiddleware($this->brandService);
        $this->request = $this->createMock(Request::class);
    }

    public function testBrandDoesNotExist__returnsClosure()
    {
        $testBrandSlug = "test-brand-slug";

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyBrandDoesntExist" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('brandSlug', '')
            ->willReturn($testBrandSlug);

            $this->brandService
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with($testBrandSlug)
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testBrandDoesExist__returns409JsonResponse()
    {
        $testBrandSlug = "test-brand-slug";

        $brand = $this->createMock(Brand::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyBrandDoesntExist" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('brandSlug', '')
            ->willReturn($testBrandSlug);

            $this->brandService
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with($testBrandSlug)
            ->willReturn($brand);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(409, $actualResults->getStatusCode());
        $this->assertEquals('"Unforunately, a brand with this slug already exists"', $actualResults->getContent());
    }
}