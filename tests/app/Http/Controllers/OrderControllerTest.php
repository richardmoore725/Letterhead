<?php

namespace App\Tests;

use App\Models\Channel;
use App\DTOs\ChannelDto;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Controllers\OrderController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderControllerTest extends TestCase
{
    private $beaconService;
    private $controller;
    private $request;

    public function setUp() : void
    {
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->controller = new OrderController($this->beaconService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotGetAds_returns404JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->willReturn(null);

        $actualResults = $this->controller->getAds($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetAds_returns200JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->willReturn('ads', 0, 0, 'brands/0/channels/0/ads');

        $actualResults = $this->controller->getAds($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotGetOrders_returns404JsonResponse()
    {
        $dto = new ChannelDto();
        $dto->id = 2;
        $dto->brandId = 2;
        $dto->title = 'testing channel title';
        $channel = new Channel($dto);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 2, 2, 'brands/2/channels/2/orders')
            ->willReturn(null);

        $actualResults = $this->controller->getOrders($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetOrders_returns200JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->willReturn('ads', 0, 0, 'brands/0/channels/0/orders');

        $actualResults = $this->controller->getOrders($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}
