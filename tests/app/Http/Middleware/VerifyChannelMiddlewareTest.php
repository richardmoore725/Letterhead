<?php

namespace App\Tests;

use App\Http\Middleware\VerifyChannelMiddleware;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyChannelMiddlewareTest extends TestCase
{
    private $brandService;
    private $channelService;
    private $request;
    private $middleware;

    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->middleware = new VerifyChannelMiddleware($this->brandService, $this->channelService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotGetChannel_returns400JsonResponse()
    {
        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "channel" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('testChannelSlug')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('Woops. This channel doesn\'t exist.', $actualResults->getContent());
    }


    public function testCannotGetChannel_brandDoesntExist_returns400JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "channel" ],
                "uses" =>
                    [ "uses"]
            ],
            [
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('testChannelSlug')
            ->willReturn($channel);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('Ouch! This brand doesn\'t exist.', $actualResults->getContent());
    }

    public function testCannotGetChannel_channelDoesntBelongToBrand_returns400JsonResponse()
    {
        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "channel" ],
                "uses" =>
                    [ "uses"]
            ],
            [
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('testChannelSlug')
            ->willReturn($channel);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $brand->expects($this->once())
            ->method('getId')
            ->willReturn(3);

        $channel->expects($this->at(1))
            ->method('getBrandId')
            ->willReturn(4);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
        $this->assertEquals('Alas. The channel doesn\'t belong to this brand.', $actualResults->getContent());
    }

    public function testCanGetChannel_returnsClosure()
    {
        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "channel" ],
                "uses" =>
                    [ "uses"]
            ],
            [
                "channelSlug" => "testChannelSlug"
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('testChannelSlug')
            ->willReturn($channel);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(\Closure::class, $actualResults);
    }
}
