<?php

namespace App\Tests\Http;

use App\Collections\BrandCollection;
use App\Collections\BrandConfigurationCollection;
use App\DTOs\BrandDto;
use App\DTOs\ChannelConfigurationDto;
use App\DTOs\ChannelDto;
use App\DTOs\ConfigurationDto;
use App\DTOs\StripeConnectedAccountDto;
use App\Http\Repositories\BrandRepositoryInterface;
use App\Http\Repositories\ChannelRepositoryInterface;
use App\Http\Repositories\ConfigurationRepositoryInterface;
use App\Http\Repositories\StripeRepositoryInterface;
use Stripe\StripeObject;
use App\Http\Services\BrandService;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\ChannelConfiguration;
use App\Models\Configuration;
use App\Tests\TestCase;

class BrandServiceTest extends TestCase
{
    private $brand;
    private $channel;
    private $channelDto;
    private $channelConfigurationDto;
    private $channelConfiguration;
    private $configurationDto;
    private $stripeConnectedAccountDto;
    private $configuration;
    private $dto;
    private $repository;
    private $channelRepository;
    private $configRepository;
    private $service;
    private $stripeRepository;

    public function setUp(): void
    {
        $brandConfigurations = collect([]);
        $this->dto = new BrandDto();
        $this->dto->brandConfigurations = new BrandConfigurationCollection($brandConfigurations);
        $this->dto->brandName = 'Black Bitter Coffee';
        $this->dto->brandSlug = 'black-bitter-coffee';
        $this->dto->channels = [];
        $this->dto->createdAt = '2020-01-28';
        $this->dto->id = 5;
        $this->dto->updatedAt = '2020-01-28';

        $this->channelDto = new ChannelDto();
        $this->channelDto->brandId = 5;
        $this->channelDto->channelConfigurations = [];
        $this->channelDto->title = 'Black Bitter Coffee Times';
        $this->channelDto->createdAt = '';
        $this->channelDto->id = 1;
        $this->channelDto->updatedAt = '';
        $this->channel = new Channel($this->channelDto);

        $this->channelConfigurationDto = new ChannelConfigurationDto();
        $this->channelConfigurationDto->channelId = 3;
        $this->channelConfigurationDto->configurationId = 9;
        $this->channelConfigurationDto->id = 5;
        $this->channelConfigurationDto->configurationName = 'API';
        $this->channelConfigurationDto->configurationSlug = 'api-key';
        $this->channelConfiguration = new ChannelConfiguration($this->channelConfigurationDto);

        $this->configurationDto = new ConfigurationDto();
        $this->configurationDto->configurationSlug = 'api-key';
        $this->configurationDto->configurationName = 'API key';
        $this->configurationDto->id = 3;
        $this->configuration = new Configuration($this->configurationDto);

        $this->stripeConnectedAccountObject = new StripeObject();
        $this->stripeConnectedAccountObject->stripe_user_id = 1;
        $this->stripeConnectedAccountObject->stripe_publishable_key = 2;
        $this->stripeConnectedAccountObject->access_token = 3;
        $this->stripeDto = new StripeConnectedAccountDto($this->stripeConnectedAccountObject);

        $permissionObject = new \stdClass();
        $permissionObject->resource = 'brand';
        $permissionObject->resourceId = 9;
        $permissionObject->action = 'create';

        $this->dto->channels = [$this->channelDto];
        $this->brand = new Brand($this->dto);

        $this->repository = $this->createMock(BrandRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->configRepository = $this->createMock(ConfigurationRepositoryInterface::class);
        $this->stripeRepository = $this->createMock(stripeRepositoryInterface::class);
        $this->service = new BrandService($this->repository, $this->channelRepository, $this->configRepository, $this->stripeRepository);
    }

    public function testCanCreateBrand_returnsBrandModel()
    {
        $this->repository
            ->expects($this->once())
            ->method('createBrandFeaturesAndConfigurations')
            ->with($this->brand->convertToDto())
            ->willReturn($this->dto);

        $actualResults = $this->service->createBrand($this->brand);

        $this->assertEquals($this->brand, $actualResults);
    }

    public function testCannotCreateBrand_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('createBrandFeaturesAndConfigurations')
            ->willReturn(null);

        $actualResults = $this->service->createBrand($this->brand);

        $this->assertNull($actualResults);
    }

    public function testCanDeleteBrand_returnsBoolean()
    {
        $this->repository
            ->expects($this->once())
            ->method('deleteBrand')
            ->with($this->brand->convertToDto())
            ->willReturn(true);

        $actualResults = $this->service->deleteBrand($this->brand);

        $this->assertTrue($actualResults);
    }

    public function testCanGetBrandById_returnsBrand()
    {
        $this->repository
            ->expects($this->once())
            ->method('getBrandById')
            ->with(5)
            ->willReturn($this->dto);

        $this->channelRepository
            ->expects($this->once())
            ->method('getChannelsByBrandId')
            ->with($this->dto->id)
            ->willReturn([$this->channelDto]);

        $actualResults = $this->service->getBrandById(5);

        $this->assertEquals($this->brand, $actualResults);
    }

    public function testCannotGetBrandById_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getBrandById')
            ->with(5)
            ->willReturn(null);

        $actualResults = $this->service->getBrandById(5);

        $this->assertEmpty($actualResults);
    }

    public function testCanUpdateBrand_returnsBrand()
    {
        $this->repository
            ->expects($this->once())
            ->method('updateBrand')
            ->with($this->brand->convertToDto())
            ->willReturn($this->dto);

        $actualResults = $this->service->updateBrand($this->brand);

        $this->assertEquals($this->brand, $actualResults);
    }

    public function testCannotUpdateBrand_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('updateBrand')
            ->with($this->brand->convertToDto())
            ->willReturn(null);

        $actualResults = $this->service->updateBrand($this->brand);

        $this->assertEmpty($actualResults);
    }

    public function testCanUpdateChannelConfiguration_returnsTrue()
    {
        $this->channelRepository
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->with(3, 'hello', 9)
            ->willReturn(true);

        $actualResults = $this->service->updateChannelConfiguration(3, 'hello', 9);

        $this->assertTrue($actualResults);
    }

    public function testCanGetBrandBySlug_returnsBrand()
    {
        $this->repository
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with('black-bitter-coffee')
            ->willReturn($this->dto);

        $this->channelRepository
            ->expects($this->once())
            ->method('getChannelsByBrandId')
            ->with($this->dto->id)
            ->willReturn($this->dto->channels);

        $actualResults = $this->service->getBrandBySlug('black-bitter-coffee');

        $this->assertEquals($this->brand, $actualResults);
    }

    public function testCanSetBrandConfiguration_returnsTrue()
    {
        $this->repository
            ->expects($this->once())
            ->method('updateBrandConfiguration')
            ->with(3, 'hello', 9)
            ->willReturn(true);

        $actualResults = $this->service->setBrandConfiguration('hello', 9, 3);

        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateBrandConfigurationWithStripeDto__emptyDto_eturnsFalse()
    {
        $this->stripeRepository
        ->expects($this->once())
        ->method('connectStripeAccount')
        ->with(123)
        ->willReturn(null);

        $actualResults = $this->service->updateBrandConfigurationWithStripeDto(123, 3);

        $this->assertFalse($actualResults);
    }

    public function testCannotUpdateBrandConfigurationWithStripeDto__configurationNotUpdated_returnsFalse()
    {
        $configurationDto1 = new ConfigurationDto();
        $configurationDto1->configurationSlug = 'stripeAccount';
        $configurationDto1->configurationName = 'stripe Account';
        $configurationDto1->id = 3;
        $stripeAccountConfiguration1 = new Configuration($configurationDto1);

        $configurationDto2 = new ConfigurationDto();
        $configurationDto2->configurationSlug = 'stripePublishableKey';
        $configurationDto2->configurationName = 'stripe Publishable Key';
        $configurationDto2->id = 2;
        $stripeKeyConfiguration2 = new Configuration($configurationDto2);

        $configurationDto3 = new ConfigurationDto();
        $configurationDto3->configurationSlug = 'stripePublishableKey';
        $configurationDto3->configurationName = 'stripe Publishable Key';
        $configurationDto3->id = 1;
        $stripeAccessConfiguration3 = new Configuration($configurationDto3);

        $this->stripeRepository
            ->expects($this->once())
            ->method('connectStripeAccount')
            ->with(123)
            ->willReturn($this->stripeDto);

        $this->configRepository
            ->expects($this->at(0))
            ->method('getConfigurationBySlug')
            ->with('stripeAccount')
            ->willReturn($configurationDto1);

        $this->configRepository
            ->expects($this->at(1))
            ->method('getConfigurationBySlug')
            ->with('stripePublishableKey')
            ->willReturn($configurationDto2);

        $this->configRepository
            ->expects($this->at(2))
            ->method('getConfigurationBySlug')
            ->with('stripeAccessToken')
            ->willReturn($configurationDto3);

        $this->repository
            ->expects($this->at(0))
            ->method('updateBrandConfiguration')
            ->with(3, 1, 3)
            ->willReturn(false);

        $actualResults = $this->service->updateBrandConfigurationWithStripeDto(123, 3);

        $this->assertFalse($actualResults);
    }

    public function testCanUpdateBrandConfigurationWithStripeDto_returnsTrue()
    {
        $configurationDto1 = new ConfigurationDto();
        $configurationDto1->configurationSlug = 'stripeAccount';
        $configurationDto1->configurationName = 'stripe Account';
        $configurationDto1->id = 3;
        $stripeAccountConfiguration1 = new Configuration($configurationDto1);

        $configurationDto2 = new ConfigurationDto();
        $configurationDto2->configurationSlug = 'stripePublishableKey';
        $configurationDto2->configurationName = 'stripe Publishable Key';
        $configurationDto2->id = 2;
        $stripeKeyConfiguration2 = new Configuration($configurationDto2);

        $configurationDto3 = new ConfigurationDto();
        $configurationDto3->configurationSlug = 'stripePublishableKey';
        $configurationDto3->configurationName = 'stripe Publishable Key';
        $configurationDto3->id = 1;
        $stripeAccessConfiguration3 = new Configuration($configurationDto3);

        $this->stripeRepository
            ->expects($this->once())
            ->method('connectStripeAccount')
            ->with(123)
            ->willReturn($this->stripeDto);

        $this->configRepository
            ->expects($this->at(0))
            ->method('getConfigurationBySlug')
            ->with('stripeAccount')
            ->willReturn($configurationDto1);

        $this->configRepository
            ->expects($this->at(1))
            ->method('getConfigurationBySlug')
            ->with('stripePublishableKey')
            ->willReturn($configurationDto2);

        $this->configRepository
            ->expects($this->at(2))
            ->method('getConfigurationBySlug')
            ->with('stripeAccessToken')
            ->willReturn($configurationDto3);

        $this->repository
            ->expects($this->at(0))
            ->method('updateBrandConfiguration')
            ->with(3, 1, 3)
            ->willReturn(true);

        $this->repository
            ->expects($this->at(1))
            ->method('updateBrandConfiguration')
            ->with(2, 2, 3)
            ->willReturn(true);

        $this->repository
            ->expects($this->at(2))
            ->method('updateBrandConfiguration')
            ->with(1, 3, 3)
            ->willReturn(true);

        $actualResults = $this->service->updateBrandConfigurationWithStripeDto(123, 3);

        $this->assertTrue($actualResults);
    }

    public function testCannotGetBrandBySlug_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getBrandBySlug')
            ->with('black-bitter-coffee')
            ->willReturn(null);

        $actualResults = $this->service->getBrandBySlug('black-bitter-coffee');

        $this->assertEmpty($actualResults);
    }

    public function testCanGetConfigurationBySlug_returnsConfiguration()
    {
        $this->configRepository
            ->expects($this->once())
            ->method('getConfigurationBySlug')
            ->with('api-key')
            ->willReturn($this->configurationDto);

        $actualResults = $this->service->getConfigurationbySlug('api-key');

        $this->assertEquals($this->configuration, $actualResults);
    }

    public function testCannotGetConfigurationBySlug_returnsNull()
    {
        $this->configRepository
            ->expects($this->once())
            ->method('getConfigurationBySlug')
            ->with('api-key')
            ->willReturn(null);

        $actualResults = $this->service->getConfigurationbySlug('api-key');

        $this->assertEmpty($actualResults);
    }

    public function testCannotGetBrandApiKeyByBrandId_returnsNull()
    {
        $this->repository
        ->expects($this->once())
        ->method('getBrandKeyByBrandId')
        ->with(5)
        ->willReturn(null);

    $actualResults = $this->service->getBrandApiKeyByBrandId(5);

    $this->assertEmpty($actualResults);
    }

    public function testCanUpdateBrandConfigurationByBrandConfigurationValue_returnsTrue()
    {
        $brandConfigurationValue = 'stripe_account';

        $this->repository
        ->expects($this->once())
        ->method('updateBrandConfigurationByBrandCofigurationValue')
        ->with($brandConfigurationValue, '')
        ->willReturn(true);

        $actualResults = $this->service->updateBrandConfigurationByBrandConfigurationValue($brandConfigurationValue, '');

        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateBrandConfigurationByBrandConfigurationValue_returnsTrue()
    {
        $brandConfigurationValue = 'stripe_account';

        $this->repository
        ->expects($this->once())
        ->method('updateBrandConfigurationByBrandCofigurationValue')
        ->with($brandConfigurationValue, '')
        ->willReturn(false);

        $actualResults = $this->service->updateBrandConfigurationByBrandConfigurationValue($brandConfigurationValue, '');

        $this->assertFalse($actualResults);
    }

    public function testCanGetBrandsByIds_returnsBrandCollection()
    {
        $arrayOfBrandIds = [2, 3, 4];
        $brandCollection = $this->createMock(BrandCollection::class);
        $this->repository
            ->expects($this->once())
            ->method('getBrandsByIds')
            ->with($arrayOfBrandIds)
            ->willReturn($brandCollection);

        $actualResults = $this->service->getBrandsByIds($arrayOfBrandIds);

        $this->assertInstanceOf(BrandCollection::class, $actualResults);
    }

    public function testCanGetBrands_returnsBrandCollection()
    {
        $brandCollection = $this->createMock(BrandCollection::class);
        $this->repository
            ->expects($this->once())
            ->method('getBrands')
            ->willReturn($brandCollection);

        $actualResults = $this->service->getBrands();

        $this->assertInstanceOf(BrandCollection::class, $actualResults);
    }
}
