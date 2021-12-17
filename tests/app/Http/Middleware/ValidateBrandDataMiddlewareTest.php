<?php

namespace App\Tests;

use App\Http\Middleware\ValidateBrandDataMiddleware;
use App\Models\Brand;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;


class ValidateBrandDataMiddlewareTest extends TestCase
{
    private $request;
    private $middleware;

    public function setUp() : void
    {
        $this->middleware = new ValidateBrandDataMiddleware();
        $this->file = $this->createMock(UploadedFile::class);
        $this->request = $this->createMock(Request::class);
    }

    public function testValidatorFails_returns400JsonResponse()
    {
        $testBrandName = "testBrandName";
        $testBrandSlug = "testBrandSlug";
        $messageBag = $this->createMock(MessageBag::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateBrand" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brandName" => $testBrandName,
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);

        $validator
            ->shouldReceive('errors')
            ->once()
            ->andReturn($messageBag);

        $messageBag
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testBrandisValidWithoutBrandHorizontalLogo_returnsClosure()
    {
        $testBrandName = "testBrandName";
        $testBrandSlug = "testBrandSlug";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateBrand" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brandName" => $testBrandName,
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
        ->expects($this->at(0))
        ->method('route')
        ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('hasFile')
            ->with('brandHorizontalLogo')
            ->willReturn(false);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('brandName')
            ->willReturn($testBrandName);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('brandSlug')
            ->willReturn($testBrandSlug);

        $this->request
            ->expects($this->at(5))
            ->method('hasFile')
            ->with('brandSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(6))
            ->method('file')
            ->with('brandSquareLogo')
            ->willReturn($uploadedFile);
        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testBrandisValidWithoutBrandSquareLogo_returnsClosure()
    {
        $testBrandName = "testBrandName";
        $testBrandSlug = "testBrandSlug";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateBrand" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brandName" => $testBrandName,
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
        ->expects($this->at(0))
        ->method('route')
        ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('hasFile')
            ->with('brandHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(3))
            ->method('file')
            ->with('brandHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('brandName')
            ->willReturn($testBrandName);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('brandSlug')
            ->willReturn($testBrandSlug);

        $this->request
            ->expects($this->at(6))
            ->method('hasFile')
            ->with('brandSquareLogo')
            ->willReturn(false);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testBrandisValidWithAllLogos_returnsClosure()
    {
        $testBrandName = "testBrandName";
        $testBrandSlug = "testBrandSlug";
        $messageBag = $this->createMock(MessageBag::class);
        $uploadedFile = $this->createMock(UploadedFile::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateBrand" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "brandName" => $testBrandName,
                "brandSlug" => $testBrandSlug
            ]
        ];

        $this->request
        ->expects($this->at(0))
        ->method('route')
        ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('hasFile')
            ->with('brandHorizontalLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(3))
            ->method('file')
            ->with('brandHorizontalLogo')
            ->willReturn($uploadedFile);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('brandName')
            ->willReturn($testBrandName);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('brandSlug')
            ->willReturn($testBrandSlug);

        $this->request
            ->expects($this->at(6))
            ->method('hasFile')
            ->with('brandSquareLogo')
            ->willReturn(true);

        $this->request
            ->expects($this->at(7))
            ->method('file')
            ->with('brandSquareLogo')
            ->willReturn($uploadedFile);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}