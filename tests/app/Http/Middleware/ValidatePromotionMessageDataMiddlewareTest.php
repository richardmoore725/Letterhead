<?php

namespace App\Tests;

use App\Http\Middleware\ValidatePromotionMessageDataMiddleware;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ValidatePromotionMessageDataMiddlewareTest extends TestCase
{
    private $request;
    private $middleware;

    public function setUp() : void
    {
        $this->middleware = new ValidatePromotionMessageDataMiddleware();
        $this->request = $this->createMock(Request::class);
    }

    public function testCanHandlePromotionMessageValidation_returnsClosure()
    {
        $testMessage = 'test';
        $testPromotionId = 1;

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validatePromotionMessage" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                'message' => $testMessage,
                'promotionId' => $testPromotionId,
            ],
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
            ->with('message')
            ->willReturn($testMessage);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('promotionId')
            ->willReturn($testPromotionId);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(\Closure::class, $actualResults);
    }

    public function testValidatorFails_returns400JsonResponse()
    {
        $testMessage = 'test';
        $testPromotionId = 1;

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
                    [ "validateLetter" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "message" => $testMessage,
                "promotionId" => $testPromotionId,
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
}
