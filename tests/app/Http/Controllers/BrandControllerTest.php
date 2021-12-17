<?php

namespace App\Tests;

use App\Collections\BrandCollection;
use App\Collections\UserPermissionCollection;
use App\DTOs\BrandDto;
use App\DTOs\ChannelDto;
use App\Http\Controllers\BrandController;
use App\Http\Services\AuthServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\PassportStamp;
use App\Models\ChannelConfiguration;
use App\Models\Configuration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Collections\ChannelConfigurationCollection;

class BrandControllerTest extends TestCase
{
    private $authService;
    private $brandService;
    private $beaconService;
    private $channelService;
    private $controller;
    private $request;

    public function setUp() : void
    {
        $this->authService = $this->createMock(AuthServiceInterface::class);
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->controller = new BrandController($this->authService, $this->beaconService, $this->brandService, $this->channelService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotCreateBrand_returns500Error()
    {
        $brand = $this->createMock(Brand::class);

        $this->brandService
        ->expects($this->once())
        ->method('createBrand')
        ->willReturn(null);

        $actualResults = $this->controller->createBrand('name', null, 'slug', null);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanCreateBrand_returnsJsonResponse()
    {
        $brand = $this->createMock(Brand::class);

        $this->brandService
        ->expects($this->once())
        ->method('createBrand')
        ->willReturn($brand);

        $actualResults = $this->controller->createBrand('name', null, 'slug', null);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCanDeleteBrandById_returnsSuccessfulJsonResponse()
    {
        $brandDto = new BrandDto();
        $brand = new Brand($brandDto);

        $brandId = 5;

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->with($brandId)
            ->willReturn($brand);

        $this->brandService
            ->expects($this->once())
            ->method('deleteBrand')
            ->with($brand)
            ->willReturn(true);

        $actualResults = $this->controller->deleteBrandById($this->request, $brandId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
        $this->assertEquals('true', $actualResults->getContent());
    }

    public function testCannotDeleteBrandById_returnsUnsucessfulJsonResponse()
    {
        $brandDto = new BrandDto();

        $brandId = 5;

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->with($brandId)
            ->willReturn(null);

        $actualResults = $this->controller->deleteBrandById($this->request, $brandId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCannotDeleteBrandById_error_returnsSuccessfulJsonResponse()
    {
        $brandDto = new BrandDto();
        $brand = new Brand($brandDto);

        $brandId = 5;

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->with($brandId)
            ->willReturn($brand);

        $this->brandService
            ->expects($this->once())
            ->method('deleteBrand')
            ->with($brand)
            ->willReturn(false);

        $actualResults = $this->controller->deleteBrandById($this->request, $brandId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
        $this->assertEquals('false', $actualResults->getContent());
    }

    public function testCanGetBrands_returnsJsonResponse()
    {
        $brands = $this->createMock(BrandCollection::class);

        $this->brandService
        ->expects($this->once())
        ->method('getBrands')
        ->willReturn($brands);


        $actualResults = $this->controller->getBrands();

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetChannels_returns404JsonResponse()
    {
        $brandDto = new BrandDto();
        $brand = new Brand($brandDto);

        $brandSlug = 'testBrandSlug';
        $channelSlug = 'testChannelSlug';

        $this->request
        ->expects($this->once())
        ->method('get')
        ->willReturn($brand);

        $actualResults = $this->controller->getBrandChannelsAds($this->request, $brandSlug, $channelSlug);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('"This channel is not part of this brand."', $actualResults->getContent());
    }

    public function testCanGetBrandChannelsAds_returnsSuccessfulJsonResponse()
    {
        $brandDto = new BrandDto();
        $brandDto->id = 5;
        $brand = new Brand($brandDto);

        $channelDto = new ChannelDto();
        $channelDto->brandId = 4;
        $channelDto->channelConfigurations = [];
        $channelDto->channelSlug = 'testChannelSlug';
        $channelDto->id = 5;
        $channelDto->title = 'Wee';
        $channel = new Channel($channelDto);

        $brand->setChannels([$channel]);
        $brandSlug = 'testBrandSlug';
        $channelSlug = $channelDto->channelSlug;
        $ads = [];

        $this->request
        ->expects($this->once())
        ->method('get')
        ->willReturn($brand);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('date', '')
            ->willReturn('');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('resolveContent', 'false')
            ->willReturn('false');

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 5, 5, 'brands/5/channels/5/ads/?resolveContent=false')
            ->willReturn($ads);

        $actualResults = $this->controller->getBrandChannelsAds($this->request, $brandSlug, $channelSlug, $ads);

        $this->assertEquals('[]', $actualResults->getContent());
    }

    public function testCannotGetBrandChannelPackage_returnEmptyChannel()
    {
        $brandId = 5;
        $channelId = 5;
        $packageId = 5;
        $package = [];

        $this->channelService
        ->expects($this->once())
        ->method('getChannelById')
        ->with(5)
        ->willReturn(null);

        $actualResults = $this->controller->getBrandChannelPackageById($this->request, $brandId, $channelId,$packageId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('"We couldn\'t find this channel."', $actualResults->getContent());
    }

    public function testCannotGetBrandChannelPackageById_returnsEmptyPackage()
    {
        $dto = new ChannelDto();
        $dto->id = 2;
        $dto->channelConfigurations = new ChannelConfigurationCollection(collect([]));
        $channel = new Channel($dto);

        $brandId = 5;
        $channelId = 5;
        $packageId = 5;
        $package = [];

        $this->channelService
        ->expects($this->once())
        ->method('getChannelById')
        ->with(5)
        ->willReturn($channel);

        $resourcePath = "brands/5/channels/5/packages/5/?list_size=0";

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 5, 5, $resourcePath)
            ->willReturn(null);

        $actualResults = $this->controller->getBrandChannelPackageById($this->request, 5, 5, 5);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
        $this->assertEquals('"We couldn\'t find this package."', $actualResults->getContent());
    }

    public function testCanGetBrandChannelPackageById_returnsPackage()
    {
        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);
        $channel = $this->createMock(Channel::class);
        $brandId = 5;
        $channelId = 5;
        $packageId = 5;
        $list_size = 50;
        $package = 'someJsonString';

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with(5)
            ->willReturn($channel);

        $channel->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $channelConfigurations->expects($this->once())
            ->method('getTotalSubscribers')
            ->willReturn(50);

        $resourcePath = "brands/5/channels/5/packages/5/?list_size=50";

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 5, 5, $resourcePath)
            ->willReturn('someJsonString');

        $actualResults = $this->controller->getBrandChannelPackageById($this->request, 5, 5, 5);

        $this->assertEquals('"someJsonString"', $actualResults->getContent());
    }

    public function testCanGetBrandsUserAdministrates_returnsJsonResponse()
    {
        $brand = $this->createMock(Brand::class);
        $brandCollection = $this->createMock(BrandCollection::class);
        $userPermissionCollection = $this->createMock(UserPermissionCollection::class);
        $filteredPermissionCollection = $this->createMock(UserPermissionCollection::class);

        $arrayOfBrands = [$brand];
        $arrayOfBrandIds = [1, 2, 3];

        $brandCollection->expects($this->once())
            ->method('getPublicArray')
            ->willReturn($arrayOfBrands);

        $filteredPermissionCollection
            ->expects($this->once())
            ->method('all')
            ->willReturn($arrayOfBrandIds);

        $userPermissionCollection
            ->expects($this->once())
            ->method('getBrandIdsUserAdministrates')
            ->willReturn($filteredPermissionCollection);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandsByIds')
            ->with($arrayOfBrandIds)
            ->willReturn($brandCollection);

        $actualResults = $this->controller->getBrandsUserAdministrates($userPermissionCollection);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotUpdateBrand_returns500Error()
    {
        $brand = $this->createMock(Brand::class);

        $this->brandService
        ->expects($this->once())
        ->method('updateBrand')
        ->willReturn(null);

        $actualResults = $this->controller->updateBrand($brand, 'name', null, 'slug', null);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateBrand_returnsJsonResponse()
    {
        $brand = $this->createMock(Brand::class);

        $this->brandService
        ->expects($this->once())
        ->method('updateBrand')
        ->willReturn($brand);

        $actualResults = $this->controller->updateBrand($brand, 'name', null, 'slug', null);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetBrandById_returnsJsonResponse()
    {
        $brand = $this->createMock(Brand::class);

        $actualResults = $this->controller->getBrandById($brand);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetBrandBySlug_emptyPassport_returns500Error()
    {
        $this->request
        ->expects($this->once())
        ->method('get')
        ->with('passportStamp')
        ->willReturn(null);

        $actualResults = $this->controller->getBrandBySlug($this->request, 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCannotGetBrandBySlug__emptyBrand_returns404Error()
    {
        $this->request
        ->expects($this->once())
        ->method('get')
        ->with('passportStamp')
        ->willReturn('passport');

        $this->brandService
        ->expects($this->once())
        ->method('getBrandBySlug')
        ->with('slug')
        ->willReturn(null);

        $actualResults = $this->controller->getBrandBySlug($this->request, 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCannotGetBrandBySlug__notAuthorized_returns403Error()
    {
        $brand = $this->createMock(Brand::class);
        $passport = $this->createMock(PassportStamp::class);

        $this->request
        ->expects($this->once())
        ->method('get')
        ->with('passportStamp')
        ->willReturn($passport);

        $this->brandService
        ->expects($this->once())
        ->method('getBrandBySlug')
        ->with('slug')
        ->willReturn($brand);

        $this->authService
        ->expects($this->once())
        ->method('authorizeActionFromPassportStamp')
        ->with($passport, 'read', 'brand', 0)
        ->willReturn(false);

        $actualResults = $this->controller->getBrandBySlug($this->request, 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotGetBrandBySlug_returnsJsonResponse()
    {
        $brand = $this->createMock(Brand::class);
        $passport = $this->createMock(PassportStamp::class);

        $this->request
        ->expects($this->once())
        ->method('get')
        ->with('passportStamp')
        ->willReturn($passport);

        $this->brandService
        ->expects($this->once())
        ->method('getBrandBySlug')
        ->with('slug')
        ->willReturn($brand);

        $this->authService
        ->expects($this->once())
        ->method('authorizeActionFromPassportStamp')
        ->with($passport, 'read', 'brand', 0)
        ->willReturn(true);

        $actualResults = $this->controller->getBrandBySlug($this->request, 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanUpdateChannelConfigurationValue_returnsJsonResponse()
    {
        $configuration = $this->createMock(Configuration::class);
        $channelConfiguration = $this->createMock(ChannelConfiguration::class);

        $this->request
        ->expects($this->once())
        ->method('input')
        ->willReturn(['channelConfigurations']);

        $this->brandService
        ->expects($this->any())
        ->method('getConfigurationBySlug')
        ->willReturn($configuration);

        $this->brandService
        ->expects($this->any())
        ->method('updateChannelConfiguration')
        ->willReturn(true);

        $actualResults = $this->controller->updateChannelConfigurationValue($this->request, 0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanCreateBrandAndChannel_returns201()
    {
        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $this->brandService
        ->expects($this->once())
        ->method('createBrand')
        ->willReturn($brand);

        $this->channelService
        ->expects($this->once())
        ->method('createChannel')
        ->willReturn($channel);

        $actualResults = $this->controller->createBrandAndChannel('name', 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCannotCreateBrandAndChannel_emptyNewBrand()
    {
        $this->brandService
        ->expects($this->once())
        ->method('createBrand')
        ->willReturn(null);

        $actualResults = $this->controller->createBrandAndChannel('name', 'slug');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }
}
