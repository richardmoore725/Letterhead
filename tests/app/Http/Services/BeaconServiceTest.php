<?php

use App\Tests\TestCase;
use App\Http\Services\beaconService;
use App\Http\Repositories\BeaconRepositoryInterface;

class BeaconServiceTest extends TestCase
{
    private $repository;
    private $service;

    public function setUp(): void
    {
        $this->repository = $this->createMock(BeaconRepositoryInterface::class);
        $this->service = new BeaconService($this->repository);
    }

    public function testCanCreateResourceByBeaconSlug()
    {
        $beaconSlug = 'ADS';
        $endpoint = 'https://adservice.local';
        $key = 'wee';

        $resource = new \stdClass();
        $resource->hello = 'world';
        $jsonEncodedContents = json_encode($resource);

        $this->repository
        ->expects($this->once())
        ->method('createBrandChannelResourceFromService')
        ->with('https://adservice.local/ads', 'wee', 1, 1, 'packages', false)
        ->Willreturn($jsonEncodedContents);

        $actualResults = $this->service->createResourceByBeaconSlug('ADS', 1, 1, 'ads', 'packages', false);

        $this->assertEquals($jsonEncodedContents, $actualResults);
    }

    public function testCanDeleteResourceFromService()
    {
        $beaconSlug = 'ADS';
        $endpoint = 'https://adservice.local';
        $key = 'wee';

        $resource = new \stdClass();
        $resource->hello = 'world';
        $jsonEncodedContents = json_encode($resource);

        $this->repository
        ->expects($this->once())
        ->method('deleteResourceFromService')
        ->with('https://adservice.local/ads', 'wee')
        ->Willreturn($jsonEncodedContents);

        $actualResults = $this->service->deleteResourceFromService('ADS', 'ads');

        $this->assertEquals($jsonEncodedContents, $actualResults);
    }

    public function testCanGetResourceByBeaconSlug()
    {
        $beaconSlug = 'ADS';
        $endpoint = 'https://adservice.local';
        $key = 'wee';

        $resource = new \stdClass();
        $resource->hello = 'world';
        $jsonEncodedContents = json_encode($resource);

        $this->repository
        ->expects($this->once())
        ->method('getBrandChannelResourceFromService')
        ->with('https://adservice.local/ads', 'wee', 1, 1, 'ads')
        ->Willreturn($jsonEncodedContents);

        $actualResults = $this->service->getResourceByBeaconSlug('ADS', 1, 1, 'ads');

        $this->assertEquals($jsonEncodedContents, $actualResults);
    }

    public function testCanGetAdResourceByBeaconSlug()
    {
        $beaconSlug = 'ADS';
        $endpoint = 'https://adservice.local';
        $key = 'wee';

        $resource = new \stdClass();
        $resource->hello = 'world';
        $jsonEncodedContents = json_encode($resource);

        $this->repository
        ->expects($this->once())
        ->method('getAdResourceFromService')
        ->with('https://adservice.local/ads', 'wee', 'ads')
        ->Willreturn($jsonEncodedContents);

        $actualResults = $this->service->getAdResourceByBeaconSlug('ADS', 'ads');

        $this->assertEquals($jsonEncodedContents, $actualResults);
    }
}
