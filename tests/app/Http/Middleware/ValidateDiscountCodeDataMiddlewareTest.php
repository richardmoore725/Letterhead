<?php

namespace App\Tests;

use App\Http\Middleware\ValidateDiscountCodeDataMiddleware;
use App\Http\Services\DiscountCodeServiceInterface;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

class ValidateDiscountCodeMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->discountCodeService = $this->createMock(DiscountCodeServiceInterface::class);
        $this->middleware = new ValidateDiscountCodeDataMiddleware($this->discountCodeService);
        $this->request = $this->createMock(Request::class);
    }

    public function testValidatorFails__returns400response()
    {
        $testChannelId = 1;

        $messageBag = $this->createMock(MessageBag::class);

        $closure = function () {
            return function () {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateDiscountCode" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "channelId" => $testChannelId
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


    public function testDiscountCodeChannelIdDoesntMatchChannelId_returnsClosure()
    {
        $testDiscountCode = "testDiscountCode";
        $testDiscountValue = 42;
        $testDisplayName = "testDisplayName";
        $testIsActive = true;
        $testChannelId = 1;
        $testChannelIdFromRoute = 20;

        $closure = function () {
            return function () {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateDiscountCode" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "channelId" => $testChannelIdFromRoute
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

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
            ->method('input')
            ->with('channelId')
            ->willReturn($testChannelId);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('displayName')
            ->willReturn($testDisplayName);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('discountCode')
            ->willReturn($testDiscountCode);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('discountValue')
            ->willReturn($testDiscountValue);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('isActive')
            ->willReturn($testIsActive);



        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testDiscountCodeIsValid_returnsClosure()
    {
        $testDiscountCode = "testDiscountCode";
        $testDiscountValue = 42;
        $testDisplayName = "testDisplayName";
        $testIsActive = true;
        $testChannelId = 1;

        $closure = function () {
            return function () {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateDiscountCode" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "channelId" => $testChannelId
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

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
            ->method('input')
            ->with('channelId')
            ->willReturn($testChannelId);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('displayName')
            ->willReturn($testDisplayName);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('discountCode')
            ->willReturn($testDiscountCode);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('discountValue')
            ->willReturn($testDiscountValue);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('isActive')
            ->willReturn($testIsActive);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}