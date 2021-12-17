<?php

namespace App\Tests;

use App\Http\Services\SubscriberServiceInterface;
use App\Collections\ChannelSubscriberCollection;
use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\AuthoringSubscriberController;
use App\Http\Response;

class AuthoringSubscriberControllerTest extends TestCase
{
    private $controller;
    private $service;
    private $request;

    public function setUp() : void
    {
        $this->service = $this->createMock(SubscriberServiceInterface::class);
        $this->controller = new AuthoringSubscriberController($this->service);
    }

    public function testCanGetSubscribersByChannel_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())->method('getId')->willReturn(5);

        $subscriberCollection = $this->createMock(ChannelSubscriberCollection::class);
        $subscriberCollection->expects($this->once())->method('getPublicArray')->willReturn([]);

        $serviceResponse = new Response('', 200, $subscriberCollection);

        $this->service->expects($this->once())
            ->method('getSubscribersByChannel')
            ->with(5)
            ->willReturn($serviceResponse);

        $actualResults = $this->controller->getSubscribersByChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('[]', $actualResults->getContent());
    }

    public function testCannotGetSubscribersByChannel_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())->method('getId')->willReturn(5);

        $subscriberCollection = $this->createMock(ChannelSubscriberCollection::class);
        $serviceResponse = new Response('Something is wrong.', 500, $subscriberCollection);

        $this->service->expects($this->once())
            ->method('getSubscribersByChannel')
            ->with(5)
            ->willReturn($serviceResponse);

        $actualResults = $this->controller->getSubscribersByChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
}
