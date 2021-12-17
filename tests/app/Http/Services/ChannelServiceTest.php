<?php

namespace App\Tests\Http;

use App\DTOs\ChannelConfigurationDto;
use App\DTOs\ChannelDto;
use App\DTOs\ConfigurationDto;
use App\Http\Repositories\ChannelRepositoryInterface;
use App\Http\Response;
use App\Http\Services\ChannelService;
use App\Models\Channel;
use App\Models\ChannelConfiguration;
use App\Collections\ChannelCollection;
use App\Http\Repositories\ConfigurationRepositoryInterface;
use App\Models\Configuration;
use App\Tests\TestCase;
use Illuminate\Http\UploadedFile;

class ChannelServiceTest extends TestCase
{
    private $channel;
    private $channelDto;
    private $channelConfigurationDto;
    private $channelConfiguration;
    private $configurationRepository;
    private $configurationDto;
    private $configuration;
    private $repository;
    private $service;

    public function setUp(): void
    {
        $this->channelDto = new ChannelDto();
        $this->channelDto->accentColor = '#bada55';
        $this->channelDto->brandId = 5;
        $this->channelDto->channelConfigurations = [];
        $this->channelDto->title = 'Black Bitter Coffee Times';
        $this->channelDto->createdAt = '';
        $this->channelDto->id = 1;
        $this->channelDto->updatedAt = '';
        $this->channelDto->defaultEmailFromName = 'jun';
        $this->channelDto->defaultEsp = 0;
        $this->channelDto->defaultFromEmailAddress = 'junsu@whereby.us';
        $this->channelDto->defaultFont = 'serif';
        $this->channelDto->enableChannelAuthoring = false;
        $this->channelDto->headingFont = '';
        $this->channelDto->loadPromosBeforeHeadings = false;
        $this->channel = new Channel($this->channelDto);

        $this->channelConfigurationDto = new ChannelConfigurationDto();
        $this->channelConfigurationDto->channelConfigurationValue = [];
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

        $this->channel = new Channel($this->channelDto);

        $this->repository = $this->createMock(ChannelRepositoryInterface::class);
        $this->configurationRepository = $this->createMock(ConfigurationRepositoryInterface::class);
        $this->service = new ChannelService($this->repository, $this->configurationRepository);
    }

    public function testCanCreateChannel_returnsChannelModel()
    {
        $channelDto = new ChannelDto();
        $channelDto->accentColor = '#bada55';
        $channelDto->brandId = 5;
        $channelDto->channelDescription = 'description';
        $channelDto->channelHorizontalLogo = '';
        $channelDto->channelImage = '';
        $channelDto->channelSlug = 'slug';
        $channelDto->channelSquareLogo = '';
        $channelDto->title = 'Black Bitter Coffee Times';
        $channelDto->defaultEmailFromName = 'jun';
        $channelDto->defaultFromEmailAddress = 'junsu@whereby.us';
        $channelDto->defaultFont = 'serif';
        $channelDto->enableChannelAuthoring = false;
        $channelDto->headingFont = 'Lobster';
        $channelDto->loadPromosBeforeHeadings = false;
        $channelDto->createdAt = null;
        $channelDto->updatedAt = null;
        $channel = new Channel($channelDto);

        $this->repository
        ->expects($this->once())
        ->method('createChannel')
        ->with($channel->convertToDto())
        ->willReturn($channelDto);

        $actualResults = $this->service->createChannel('#bada55', 5, 'description', '', '', 'slug', '', 'jun', 'junsu@whereby.us', 'serif', false, 'Lobster', false, 'Black Bitter Coffee Times');

        $this->assertEquals($channel, $actualResults);
    }

    public function testCannotCreateChannel_returnsNull()
    {
        $channelDto = new ChannelDto();
        $channelDto->accentColor = '#bada55';
        $channelDto->brandId = 5;
        $channelDto->channelDescription = 'description';
        $channelDto->channelHorizontalLogo = '';
        $channelDto->channelImage = '';
        $channelDto->channelSlug = 'slug';
        $channelDto->channelSquareLogo = '';
        $channelDto->defaultEmailFromName = 'jun';
        $channelDto->defaultFromEmailAddress = 'junsu@whereby.us';
        $channelDto->defaultFont = 'serif';
        $channelDto->enableChannelAuthoring = false;
        $channelDto->headingFont = 'Lobster';
        $channelDto->loadPromosBeforeHeadings = false;
        $channelDto->title = 'Black Bitter Coffee Times';
        $channelDto->createdAt = null;
        $channelDto->updatedAt = null;
        $channel = new Channel($channelDto);

        $this->repository
        ->expects($this->once())
        ->method('createChannel')
        ->with($channel->convertToDto())
        ->willReturn(null);

        $actualResults = $this->service->createChannel('#bada55', 5, 'description', '', '', 'slug', '', 'jun', 'junsu@whereby.us', 'serif', false, 'Lobster', false, 'Black Bitter Coffee Times');

        $this->assertNull($actualResults);
    }

    public function testCanDeleteChannel_returnsTrue()
    {
        $this->repository
        ->expects($this->once())
        ->method('deleteChannel')
        ->with($this->channel->convertToDto())
        ->willReturn(true);

        $actualResults = $this->service->deleteChannel($this->channel);

        $this->assertTrue($actualResults);
    }

    public function testCanUpdateChannel_returnsChannelModel()
    {
        $this->repository
        ->expects($this->once())
        ->method('updateChannel')
        ->with($this->channel->convertToDto())
        ->willReturn($this->channelDto);

        $actualResults = $this->service->updateChannel($this->channel);

        $this->assertEquals($this->channel, $actualResults);
    }

    public function testCannotGetChannelById_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getChannelById')
            ->with(1)
            ->willReturn(null);

        $actualResults = $this->service->getChannelById(1);

        $this->assertNull($actualResults);
    }

    public function testCanGetChannelById_returnsChannel()
    {
        $this->repository
            ->expects($this->once())
            ->method('getChannelById')
            ->with(1)
            ->willReturn($this->channelDto);

        $actualResults = $this->service->getChannelById(1);

        $this->assertEquals($this->channel, $actualResults);
    }

    public function testCannotGetChannelBySlug_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('wee')
            ->willReturn(null);

        $actualResults = $this->service->getChannelBySlug('wee');

        $this->assertNull($actualResults);
    }

    public function testCanGetChannelBySlug_returnsChannel()
    {
        $this->repository
            ->expects($this->once())
            ->method('getChannelBySlug')
            ->with('wee')
            ->willReturn($this->channelDto);

        $actualResults = $this->service->getChannelBySlug('wee');

        $this->assertEquals($this->channel, $actualResults);
    }

    public function testCanGetChannelImagePath_returnsString()
    {
        $imageObject = $this->createMock(UploadedFile::class);
        $imageObject
            ->expects($this->once())
            ->method('storePubliclyAs')
            ->willReturn('https://whereby.us');

        $actualResults = $this->service->getChannelImagePath($this->channel, $imageObject);

        $this->assertEquals('https://whereby.us', $actualResults);
    }

    public function testCanGetChannelImagePath_withString_returnsString()
    {
        $imageObject = 'https://iamanimagealready.jpg';

        $actualResults = $this->service->getChannelImagePath($this->channel, $imageObject);

        $this->assertEquals('https://iamanimagealready.jpg', $actualResults);
    }

    public function testCannotGetChannelImagePath_withNull_returnsString()
    {
        $imageObject = 'null';

        $actualResults = $this->service->getChannelImagePath($this->channel, $imageObject);

        $this->assertEmpty($actualResults);
    }

    public function testCanGetChannels_returnsChannelCollection()
    {
        $channelCollection = $this->createMock(ChannelCollection::class);

        $this->repository
            ->expects($this->once())
            ->method('getChannels')
            ->willReturn($channelCollection);

        $actualResults = $this->service->getChannels();

        $this->assertInstanceOf(ChannelCollection::class, $actualResults);
    }

    public function testCanGetChannelsThatAutoSyncListStats_returnsChannelCollection()
    {
        $channelCollection = $this->createMock(ChannelCollection::class);

        $this->repository
            ->expects($this->once())
            ->method('getChannelsThatAutoSyncListStats')
            ->willReturn($channelCollection);

        $actualResults = $this->service->getChannelsThatAutoSyncListStats();

        $this->assertInstanceOf(ChannelCollection::class, $actualResults);
    }

    public function testCanGetChannelByBrandApiKey_returnsErrorResponse()
    {
        $key = '1234';
        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $this->repository
            ->expects($this->once())
            ->method('getChannelByBrandApiKey')
            ->with($key)
            ->willReturn($response);

        $actualResults = $this->service->getChannelByBrandApiKey($key);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGetChannelByBrandApiKey_returnsChannel()
    {
        $key = '1234';
        $response = $this->createMock(Response::class);
        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($this->channelDto);

        $this->repository
            ->expects($this->once())
            ->method('getChannelByBrandApiKey')
            ->with($key)
            ->willReturn($response);

        $actualResults = $this->service->getChannelByBrandApiKey($key);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertInstanceOf(Channel::class, $actualResults->getData());
        $this->assertEquals(200, $actualResults->getStatus());
    }
}
