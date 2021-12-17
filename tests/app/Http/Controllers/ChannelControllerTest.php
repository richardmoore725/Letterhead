<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;
use Illuminate\Support\Collection;
use App\DTOs\ChannelDto;
use App\DTOs\ConfigurationDto;
use App\DTOs\ChannelConfigurationDto;
use App\Collections\ChannelCollection;
use App\Http\Controllers\ChannelController;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\Configuration;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Jobs\SyncMailChimpListData;

class ChannelControllerTest extends TestCase
{
    private $brandService;
    private $channelService;

    /**
     * @var Channel
     */
    private $channel;

    /**
     * @var ChannelController
     */
    private $controller;
    private $request;
    private $queue;

    public function setUp() : void
    {
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->queue = $this->createMock(Queue::class);
        $dto = new ChannelDto();
        $dto->id = 2;
        $dto->channelConfigurations = new ChannelConfigurationCollection(collect([]));
        $this->channel = new Channel($dto);
        $this->channel->setAccentColor('#bada55');
        $this->channel->setBrandId(3);
        $this->channel->setChannelSlug('wee');
        $this->channel->setChannelDescription('woo');
        $this->channel->setChannelImage('');
        $this->channel->setTitle('I am a channel');
        $this->channel->setChannelHorizontalLogo('');
        $this->channel->setChannelSquareLogo('');
        $this->channel->setDefaultEmailFromName('jun');
        $this->channel->setDefaultFromEmailAddress('junsu@whereby.us');
        $this->channel->setDefaultFont('serif');
        $this->channel->setEnableChannelAuthoring(false);
        $this->channel->setHeadingFont('');
        $this->channel->setLoadPromosBeforeHeadings(false);

        $this->controller = new ChannelController($this->brandService, $this->channelService, $this->queue);
        $this->request = $this->createMock(Request::class);
    }

    public function testCannotCreateBrandChannel_returns500Error()
    {
        $brand = $this->createMock(Brand::class);

        $this->channelService
        ->expects($this->once())
        ->method('createChannel')
        ->willReturn(null);

        $actualResults = $this->controller->createBrandChannel('#bada55', $brand, '', 'description', '', 'slug', '', 'jun', 'junsu@whereby.us', 'serif', false, '', false, 'title');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanCreateBrandChannel_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $brand = $this->createMock(Brand::class);

        $this->channelService
        ->expects($this->once())
        ->method('createChannel')
        ->willReturn($channel);

        $actualResults = $this->controller->createBrandChannel('#bada55', $brand, '', 'description', '', 'slug', '', 'jun', 'junsu@whereby.us', 'serif', false, '', false, 'title');

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanDeleteChannel_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->channelService
        ->expects($this->once())
        ->method('deleteChannel')
        ->willReturn(true);

        $actualResults = $this->controller->deleteChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetChannelBySlug_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())
            ->method('convertToArray')
            ->willReturn([]);

        $actualResults = $this->controller->getChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanUpdateBrandChannel_returnsJsonResponse()
    {
        $this->channel->setAccentColor('#bada55');
        $this->channel->setChannelSquareLogo('https://google.com/law.jpg');
        $this->channel->setChannelHorizontalLogo('https://google.com/why.jpg');
        $this->channel->setChannelImage('logo.jpg');
        $this->channel->setDefaultFont('serif');
        $this->channel->setHeadingFont('Lobster');

        $this->channelService
            ->expects($this->at(0))
            ->method('getChannelImagePath')
            ->willReturn('https://google.com/law.jpg');

        $this->channelService
            ->expects($this->at(1))
            ->method('getChannelImagePath')
            ->willReturn('https://google.com/why.jpg');

        $this->channelService
            ->expects($this->at(0))
            ->method('getChannelImagePath')
            ->willReturn('logo.jpg');

        $this->channelService
            ->expects($this->once())
            ->method('updateChannel')
            ->with($this->channel)
            ->willReturn($this->channel);

        $actualResults = $this->controller
            ->updateBrandChannel(
                '#bada55',
                $this->channel,
                'Weee',
                'https://google.com/law.jpg',
                'https://google.com/why.jpg',
                'hmm-slug',
                'logo.jpg',
                'jun',
                'junsu@whereby.us',
                'serif',
                false,
                'Lobster',
                false,
                'Title 2'
            );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotUpdateBrandChannel_returnsJsonResponse()
    {
        $this->channel->setAccentColor('#bada55');
        $this->channel->setChannelSquareLogo('https://google.com/law.jpg');
        $this->channel->setChannelHorizontalLogo('https://google.com/why.jpg');
        $this->channel->setChannelImage('logo.jpg');
        $this->channel->setDefaultFont('serif');
        $this->channel->setHeadingFont('Lobster');

        $this->channelService
            ->expects($this->at(0))
            ->method('getChannelImagePath')
            ->willReturn('https://google.com/law.jpg');

        $this->channelService
            ->expects($this->at(1))
            ->method('getChannelImagePath')
            ->willReturn('https://google.com/why.jpg');

        $this->channelService
            ->expects($this->at(0))
            ->method('getChannelImagePath')
            ->willReturn('logo.jpg');

        $this->channelService
            ->expects($this->once())
            ->method('updateChannel')
            ->with($this->channel)
            ->willReturn(null);

        $actualResults = $this->controller
            ->updateBrandChannel(
                '#bada55',
                $this->channel,
                'Weee',
                'https://google.com/law.jpg',
                'https://google.com/why.jpg',
                'hmm-slug',
                'logo.jpg',
                'jun',
                'junsu@whereby.us',
                'serif',
                false,
                'Lobster',
                false,
                'Title 2'
            );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateChannelConfiguration_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $dto = new ConfigurationDto();
        $dto->dataType = 'array';
        $dto->id = 1;
        $dto->configurationSlug = 'slug';
        $configuration = new Configuration($dto);

        $uploadedFile = $this->createMock(UploadedFile::class);

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->with('channelConfigurationValue')
            ->willReturn(true);

        $this->request
            ->expects($this->once())
            ->method('file')
            ->with('channelConfigurationValue')
            ->willReturn($uploadedFile);

        $channel
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(3);

        $channel
            ->expects($this->once())
            ->method('getSlug')
            ->willReturn('the-new-tropic');

        $uploadedFile
            ->expects($this->once())
            ->method('storePubliclyAs')
            ->willReturn('logo.jpg');

        Storage::shouldReceive('url')
            ->once()
            ->with('logo.jpg')
            ->andReturn('https://whereby.us/logo.jpg');

        $this->brandService
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->willReturn(true);

        $actualResults = $this->controller->updateChannelConfiguration($channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanUpdateChannelConfiguration_noFile_returnsJsonResponse()
    {
        $dto = new ConfigurationDto();
        $dto->dataType = 'boolean';
        $dto->id = 1;
        $configuration = new Configuration($dto);

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelConfigurationValue', false)
            ->willReturn(false);

        $this->brandService
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->willReturn(true);

        $acutalResults = $this->controller->updateChannelConfiguration($this->channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $acutalResults);
    }

    public function testCanUpdateChannelConfiguration_noFile_booleanDataType_returnsJsonResponse()
    {
        $dto = new ConfigurationDto();
        $dto->dataType = 'boolean';
        $dto->id = 1;
        $configuration = new Configuration($dto);

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelConfigurationValue')
            ->willReturn(true);

        $this->brandService
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->willReturn(true);

        $acutalResults = $this->controller->updateChannelConfiguration($this->channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $acutalResults);
    }

    public function testCanUpdateChannelConfiguration_noFile_objectDataType_returnsJsonResponse()
    {
        $dto = new ConfigurationDto();
        $dto->dataType = 'object';
        $dto->id = 1;
        $configuration = new Configuration($dto);

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->willReturn(false);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelConfigurationValue')
            ->willReturn(true);

        $this->brandService
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->willReturn(true);

        $acutalResults = $this->controller->updateChannelConfiguration($this->channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $acutalResults);
    }

    public function testCanGetChannels_returnsJsonResponse()
    {
        $channels = $this->createMock(ChannelCollection::class);

        $this->channelService
            ->expects($this->once())
            ->method('getChannels')
            ->willReturn($channels);

        $actualResults = $this->controller->getChannels();

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotUpdateChannelConfigurationMailChimpListId_noApiKey_returns400JsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $configuration = $this->createMock(Configuration::class);
        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('');

        $actualResults = $this->controller->updateChannelConfigurationMailChimpListId($channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotUpdateChannelConfigurationMailChimpListId_returns200JsonResponse()
    {
        $configuration = $this->createMock(Configuration::class);
        $channel = $this->createMock(Channel::class);
        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('abcd123456');

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('abc1234567');

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('channelConfigurationValue')
            ->willReturn('abc1234567');

        $actualResults = $this->controller->updateChannelConfigurationMailChimpListId($channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanUpdateChannelConfigurationMailChimpListId_returnsJsonResponse()
    {
        $configuration = $this->createMock(Configuration::class);
        $channel = $this->createMock(Channel::class);
        $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurations);

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('abcd123456');

        $channelConfigurations
            ->expects($this->once())
            ->method('getMcSelectedEmailListId')
            ->willReturn('abc1234567');

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('channelConfigurationValue')
            ->willReturn('newListId1');

        $syncMailChimpListDataJob = $this->createMock(SyncMailChimpListData::class);

        $this->queue
            ->expects($this->once())
            ->method('pushOn');

        $this->request
            ->expects($this->once())
            ->method('hasFile')
            ->willReturn(false);

        $configuration
            ->expects($this->at(0))
            ->method('getDataType')
            ->willReturn('boolean');

        $configuration
            ->expects($this->at(1))
            ->method('getDataType')
            ->willReturn('boolean');

        $this->request
            ->expects($this->any())
            ->method('input')
            ->with('channelConfigurationValue')
            ->willReturn('true');

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $configuration
            ->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->brandService
            ->expects($this->once())
            ->method('updateChannelConfiguration')
            ->willReturn(true);

        $actualResults = $this->controller->updateChannelConfigurationMailChimpListId($channel, $configuration, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
}
