<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;
use App\Http\Controllers\AdTypeController;
use App\Http\Controllers\PackageController;
use App\Http\Services\AdTypeServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\PackageServiceInterface;

use App\Models\Channel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PackageControllerTest extends TestCase
{
    private $beaconService;
    private $channel;
    private $channelConfigurations;
    private $controller;
    private $packageService;
    private $request;

    public function setUp() : void
    {
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->channel = $this->createMock(Channel::class);
        $this->channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);
        $this->packageService = $this->createMock(PackageServiceInterface::class);

        $this->controller = new PackageController($this->beaconService, $this->packageService);

        $this->request = $this->createMock(Request::class);
    }

    public function testCanDeletePackage_returns200Response()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('deleteResourceFromService')
            ->with('ads', 'packages/19')
            ->willReturn(true);

        $actualResults = $this->controller->deletePackage(19);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotDeletePackage_returns500Response()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('deleteResourceFromService')
            ->with('ads', 'packages/19')
            ->willReturn(false);

        $actualResults = $this->controller->deletePackage(19);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanCreatePackage_returns500Error()
    {
        $channel = $this->createMock(Channel::class);

        $this->packageService
            ->expects($this->once())
            ->method('getPackageRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn([]);

        $actualResults = $this->controller->createPackage($channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanCreatePackage_returns201()
    {
        $channel = $this->createMock(Channel::class);

        $this->packageService
            ->expects($this->once())
            ->method('getPackageRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn(['adTypeObjectHereWee']);

        $actualResults = $this->controller->createPackage($channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCannotUpdatePackageService_returns500Error()
    {
        $channel = $this->createMock(Channel::class);

        $this->packageService
            ->expects($this->once())
            ->method('getPackageRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn([]);

        $actualResults = $this->controller->updatePackage($channel, 3, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdatePackage_returns201()
    {
        $channel = $this->createMock(Channel::class);

        $this->packageService
            ->expects($this->once())
            ->method('getPackageRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn(['adTypeObjectHereWee']);

        $actualResults = $this->controller->updatePackage($channel, 30, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanGetPackages_returnsJsonResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('displayHidden', 'false')
            ->willReturn('true');

        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($this->channelConfigurations);

        $this->channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->channelConfigurations
            ->expects($this->once())
            ->method('getTotalSubscribers')
            ->willReturn(1000);

        $this->packageService
            ->expects($this->once())
            ->method('getPackageResourcesFromAdService')
            ->with(5, 2, '?displayHidden=true&list_size=1000')
            ->willReturn([]);

        $actualResults = $this->controller->getPackages($this->channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }
}
