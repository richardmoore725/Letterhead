<?php

namespace App\Tests;

use App\Http\Middleware\VerifyDiscountCodeMiddleware;
use App\Http\Services\DiscountCodeServiceInterface;
use App\Models\DiscountCode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyDiscountCodeMiddlewareTest extends TestCase
{
    public function setUp() : void
    {
        $this->service = $this->createMock(DiscountCodeServiceInterface::class);
        $this->middleware = new VerifyDiscountCodeMiddleware($this->service);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotGetDiscountCodeById_NoCodeOrIdProvided_returnsResponse()
    {
        $testChannelId = 1;
        $testDiscountCodeId = null;
        $testDiscountCodeString = null;

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCode" => $testDiscountCodeString,
                "discountCodeId" => $testDiscountCodeId

            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testCannotGetDiscountCodeById_returns404JsonResponse()
    {
        $testChannelId = 1;
        $testDiscountCodeId = 1;

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCodeId" => $testDiscountCodeId
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with($testChannelId)
            ->willReturn(null);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with($testChannelId)
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('Are you sure this discount code exists?', $actualResults->getContent());
    }

    public function testCannotGetDiscountCodeById_DiscountCodeChannelIdDoesntMatch_returns404JsonResponse()
    {
        $discountCode = $this->createMock(DiscountCode::class);

        $testChannelId = 1;
        $testDiscountCodeId = 1;

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCodeId" => $testDiscountCodeId
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with($testChannelId)
            ->willReturn($discountCode);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn(5);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('No such discount code is associated with this channel.', $actualResults->getContent());
    }

    public function testCannotGetDiscountCodeById_DiscountCodeWasDeleted_returns404JsonResponse()
    {
        $discountCode = $this->createMock(DiscountCode::class);

        $testChannelId = 1;
        $testDiscountCodeId = 1;

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCodeId" => $testDiscountCodeId
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with($testChannelId)
            ->willReturn($discountCode);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn($testChannelId);

        $discountCode
            ->expects($this->once())
            ->method('getDeletedAt')
            ->willReturn('2021-01-06 23:13:27');

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('Are you sure this discount code exists?', $actualResults->getContent());
    }

    public function testCanGetDiscountCodeById_returnsClosure()
    {
        $discountCode = $this->createMock(DiscountCode::class);

        $testChannelId = 1;
        $testDiscountCodeId = 1;

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCodeId" => $testDiscountCodeId
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with($testDiscountCodeId)
            ->willReturn($discountCode);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn($testChannelId);

        $discountCode
            ->expects($this->once())
            ->method('getDeletedAt')
            ->willReturn('');

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testCannotGetDiscountCodeByCode_returns404JsonResponse()
    {
        $testChannelId = 1;
        $testDiscountCodeCode = 'testDiscountCodeCode';

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCode" => $testDiscountCodeCode
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with($testDiscountCodeCode)
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('Are you sure this discount code exists?', $actualResults->getContent());
    }

    public function testCanGetDiscountCodeByCode_DiscountCodeWasDeleted_returns404JsonResponse()
    {
        $discountCode = $this->createMock(DiscountCode::class);

        $testChannelId = 1;
        $testDiscountCodeCode = 'testDiscountCodeCode';

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCode" => $testDiscountCodeCode
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with($testDiscountCodeCode)
            ->willReturn($discountCode);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn($testChannelId);

        $discountCode
            ->expects($this->once())
            ->method('getDeletedAt')
            ->willReturn('2021-01-06 23:13:27');

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('Are you sure this discount code exists?', $actualResults->getContent());
    }

    public function testCanGetDiscountCodeByCode_returnsClosure()
    {
        $discountCode = $this->createMock(DiscountCode::class);

        $testChannelId = 1;
        $testDiscountCodeCode = 'testDiscountCodeCode';

        $closure = function() {
            return function() {
                return 'catchphrase!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyDiscountCode" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelId" => $testChannelId,
                "discountCode" => $testDiscountCodeCode
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with($testDiscountCodeCode)
            ->willReturn($discountCode);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn($testChannelId);

        $discountCode
            ->expects($this->once())
            ->method('getDeletedAt')
            ->willReturn('');

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}