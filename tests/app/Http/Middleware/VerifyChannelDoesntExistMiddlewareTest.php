<?php

namespace App\Tests;

use App\Http\Middleware\VerifyChannelDoesntExistMiddleware;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class VerifyChannelDoesntExistMiddlwareTest extends TestCase
{
    public function setUp() : void
    {
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->middleware = new VerifyChannelDoesntExistMiddleware($this->channelService);
        $this->request = $this->createMock(Request::class);
    }

    public function testChannelDoesNotExist__returnsClosure()
    {
        $testChannelSlug = "test-channel-slug";

        $closure = function() {
            return function() {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                [ "verifyChannelDoesntExist" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelSlug', '')
            ->willReturn($testChannelSlug);

            $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with($testChannelSlug)
            ->willReturn(null);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(Closure::class, $actualResults);
    }

    public function testChannelDoesExist__returns409JsonResponse()
    {
        $testChannelSlug = "test-channel-slug";

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
                [ "verifyChannelDoesntExist" ],
                "uses" =>
                [ "uses"]
            ],
            [
                "channelSlug" => $testChannelSlug
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelSlug', '')
            ->willReturn($testChannelSlug);

            $this->channelService
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with($testChannelSlug)
            ->willReturn($channel);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(409, $actualResults->getStatusCode());
        $this->assertEquals('"Unforunately, a channel with this slug already exists"', $actualResults->getContent());
    }
}