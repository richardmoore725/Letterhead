<?php

namespace App\Tests;

use App\Http\Middleware\VerifyPromotionOrderMiddleware;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Validator;

class VerifyPromotionOrderMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->middleware = new VerifyPromotionOrderMiddleware();
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotValidatePromotionOrder__returns400Repsonse()
    {

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $messageBag = $this->createMock(MessageBag::class);

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "amount" => null,
                "dateStart" => null,
                "discountCode" => null,
                "originalPurchasePrice" => null,
                "paymentMethod" => null,
                "promotionTypeId" => null,
                "userEmail" => null,
                "userName" => null
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

    public function testCanValidatePromotionOrder__returnsClosure()
    {
        $amount = 5000;
        $dateStart = "2021-02-15";
        $discountCode = "";
        $originalPurchasePrice = 5000;
        $paymentMethod = "pm_fake_payment";
        $promotionTypeId = 42;
        $userEmail = "test@ing.url";
        $userName = "Test Test";

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $messageBag = $this->createMock(MessageBag::class);

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "amount" => $amount,
                "dateStart" => $dateStart,
                "discountCode" => $discountCode,
                "originalPurchasePrice" => $originalPurchasePrice,
                "paymentMethod" => $paymentMethod,
                "promotionTypeId" => $promotionTypeId,
                "userEmail" => $userEmail,
                "userName" => $userName
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
            ->andReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('amount')
            ->willReturn($amount);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('dateStart')
            ->willReturn($dateStart);

        $this->request
            ->expects($this->at(4))
            ->method('input')
            ->with('discountCode')
            ->willReturn($discountCode);

        $this->request
            ->expects($this->at(5))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn($originalPurchasePrice);

        $this->request
            ->expects($this->at(6))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn($paymentMethod);

        $this->request
            ->expects($this->at(7))
            ->method('input')
            ->with('promotionTypeId')
            ->willReturn($promotionTypeId);


        $this->request
            ->expects($this->at(8))
            ->method('input')
            ->with('userEmail')
            ->willReturn($userEmail);

        $this->request
            ->expects($this->at(9))
            ->method('input')
            ->with('userName')
            ->willReturn($userName);


        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}