<?php

use App\Http\Middleware\VerifyPromotionMiddleware;
use App\Http\Services\AdServiceInterface;
use App\Models\Promotion;
use App\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyPromotionMiddlewareTest extends TestCase
{
    private $service;
    private $request;
    private $middleware;

    public function setUp(): void
    {
        $this->service = $this->createMock(AdServiceInterface::class);
        $this->middleware = new VerifyPromotionMiddleware($this->service);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotVerifyPromotion()
    {
        $adId = 5;
        $closure = function() {
            return function() {
                return 'hello';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [
                    "verifyPromotion"
                ],
                "uses" =>
                [
                    "uses"
                ]
                ],
                [
                    "adId" => $adId,
                ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getPromotionByPromotionId')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanVerifyPromotion()
    {
        $adId = 5;
        $promotion = $this->createMock(Promotion::class);

        $closure = function() {
            return function() {
                return 'hello';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [
                    "verifyPromotion"
                ],
                "uses" =>
                [
                    "uses"
                ]
                ],
                [
                    "adId" => $adId,
                ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->service
            ->expects($this->once())
            ->method('getPromotionByPromotionId')
            ->willReturn($promotion);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }
}
