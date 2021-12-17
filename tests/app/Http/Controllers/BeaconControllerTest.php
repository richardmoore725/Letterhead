<?php

namespace App\Tests;

use App\Http\Controllers\BeaconController;
use App\Http\Services\AuthServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\PassportStamp;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BeaconControllerTest extends TestCase
{
    private $authService;
    private $beaconService;
    private $controller;
    private $request;

    public function setUp() : void
    {
        $this->authService = $this->createMock(AuthServiceInterface::class);
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->controller = new BeaconController($this->authService, $this->beaconService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotCreateResource_cannotCreateOnBrand_returns403Error()
    {
        $passport = $this->createMock(PassportStamp::class);

        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with('passportStamp')
            ->willReturn($passport);

        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('signal')
            ->willReturn('signal');

        $this->authService
            ->expects($this->once())
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(false);

        $actualResults = $this->controller->createResource($this->request, 0, 0, 'ads', '');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotCreateResource_cannotCreateOnChannel_returns403Error()
    {
        $passport = $this->createMock(PassportStamp::class);

        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with('passportStamp')
            ->willReturn($passport);

        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('signal')
            ->willReturn('signal');

        $this->authService
            ->expects($this->at(0))
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(true);
        
        $this->authService
            ->expects($this->at(1))
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'channel', 0)
            ->willReturn(false);

        $actualResults = $this->controller->createResource($this->request, 0, 0, 'ads', '');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCanCreateResource_returnsJsonResponse()
    {
        $passport = $this->createMock(PassportStamp::class);

        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with('passportStamp')
            ->willReturn($passport);

        $this->request
            ->expects($this->at(1))
            ->method('get')
            ->with('signal')
            ->willReturn('signal');

        $this->authService
            ->expects($this->at(0))
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(true);
        
        $this->authService
            ->expects($this->at(1))
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'channel', 0)
            ->willReturn(true);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->with('ads', 0, 0, '', 'signal', false)
            ->willReturn(true);

        $actualResults = $this->controller->createResource($this->request, 0, 0, 'ads', '');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetResource_cannotCreateOnBrand_returns403Error()
    {
        $passport = $this->createMock(PassportStamp::class);
        $channel = $this->createMock(Channel::class);
        $brand = $this->createMock(Brand::class);

        $this->authService
            ->expects($this->once())
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(false);

        $actualResults = $this->controller->getResource($this->request, $brand, 'ads', $channel, $passport, '');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotGetResource_emptyResource_returns404Error()
    {
        $passport = $this->createMock(PassportStamp::class);
        $channel = $this->createMock(Channel::class);
        $brand = $this->createMock(Brand::class);

        $this->authService
            ->expects($this->once())
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(true);
        
        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/')
            ->willReturn('');

        $actualResults = $this->controller->getResource($this->request, $brand, 'ads', $channel, $passport, '');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetResource_returnsJsonResponse()
    {
        $passport = $this->createMock(PassportStamp::class);
        $channel = $this->createMock(Channel::class);
        $brand = $this->createMock(Brand::class);

        $this->authService
            ->expects($this->once())
            ->method('authorizeActionFromPassportStamp')
            ->with($passport, 'create', 'brand', 0)
            ->willReturn(true);
        
        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/resource')
            ->willReturn('resource');

        $actualResults = $this->controller->getResource($this->request, $brand, 'ads', $channel, $passport, 'resource');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
}
