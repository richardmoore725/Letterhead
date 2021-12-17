<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;
use App\Http\Controllers\AdTypeController;
use App\Http\Response;
use App\Http\Services\AdTypeServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Channel;
use App\Models\PassportStamp;
use Aws\Result;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdTypeControllerTest extends TestCase
{
    private $adTypeService;
    private $beaconService;
    private $controller;
    private $request;

    public function setUp() : void
    {
        $this->adTypeService = $this->createMock(AdTypeServiceInterface::class);
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->controller = new AdTypeController($this->adTypeService, $this->beaconService);
        $this->request = $this->createMock(Request::class);
    }

    public function testCanCreateAdType_returns500Error()
    {
        $channel = $this->createMock(Channel::class);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypeRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn([]);

        $actualResults = $this->controller->createAdType($channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanCreateAdType_returns201()
    {
        $channel = $this->createMock(Channel::class);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypeRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn(['adTypeObjectHereWee']);

        $actualResults = $this->controller->createAdType($channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCannotDeleteAdType_returns500Error()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('deleteResourceFromService')
            ->with('ads', 'ad-types/0')
            ->willReturn(null);

        $actualResults = $this->controller->deleteAdType(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanDeleteAdType_returns200JsonResponse()
    {
        $wasTypeDeleted = new \stdClass();

        $this->beaconService
            ->expects($this->once())
            ->method('deleteResourceFromService')
            ->with('ads', 'ad-types/0')
            ->willReturn($wasTypeDeleted);

        $actualResults = $this->controller->deleteAdType(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotGetAdTypeById_returns404JsonResponse()
    {
        $adType = new \stdClass();

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'ad-types/0')
            ->willReturn($adType);

        $actualResults = $this->controller->getAdTypeById(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetAdTypeById_returnsJsonResponse()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'ad-types/0')
            ->willReturn(null);

        $actualResults = $this->controller->getAdTypeById(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetAdTypesByChannel_returnsJsonResponse()
    {
        $adTypes = ['someAdTypes'];
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/ads/types')
            ->willReturn($adTypes);

        $actualResults = $this->controller->getAdTypesByChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetAdTypesWithPricesByChannel_returns404Response()
    {
        $brandId = 1;
        $channel = $this->createMock(Channel::class);
        $channelConfigs = $this->createMock(ChannelConfigurationCollection::class);
        $channelId = 1;
        $listSize = 24000;
        $response = $this->createMock(Response::class);

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigs);

        $channelConfigs
            ->expects($this->once())
            ->method('getTotalSubscribers')
            ->willReturn($listSize);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypesWithPricesByChannel')
            ->with($brandId, $channelId, $listSize)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $actualResults = $this->controller->getAdTypesWithPricesByChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetAdTypesWithPricesByChannel_returnsJsonResponse()
    {
        $adTypeObject = new \stdClass();
        $adTypeObject->id = 1;
        $array = [$adTypeObject];
        $brandId = 1;
        $channel = $this->createMock(Channel::class);
        $channelConfigs = $this->createMock(ChannelConfigurationCollection::class);
        $channelId = 1;
        $listSize = 24000;
        $response = $this->createMock(Response::class);

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigs);

        $channelConfigs
            ->expects($this->once())
            ->method('getTotalSubscribers')
            ->willReturn($listSize);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypesWithPricesByChannel')
            ->with($brandId, $channelId, $listSize)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn($array);

        $actualResults = $this->controller->getAdTypesWithPricesByChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotUpdateAdType_returns500Error()
    {
        $channel = $this->createMock(Channel::class);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypeRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn([]);

        $actualResults = $this->controller->updateAdType($channel, 3, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateAdType_returns201()
    {
        $channel = $this->createMock(Channel::class);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAdTypeRequestFormattedForMultipartPost')
            ->with($this->request)
            ->willReturn([]);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn(['adTypeObjectHereWee']);

        $actualResults = $this->controller->updateAdType($channel, 30, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotGetAvailableDatesByAdType_returnsErrorResponse()
    {
        $adTypeId = 25;
        $brandId = 4;
        $channel = $this->createMock(Channel::class);
        $channelConfig = $this->createMock(ChannelConfigurationCollection::class);
        $channelId = 1;
        $disabledDates = [];
        $response = $this->createMock(Response::class);
        $schedulingBuffer = 72;

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfig);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $channelConfig
            ->expects($this->once())
            ->method('getDisabledDates')
            ->willReturn($disabledDates);

        $channelConfig
            ->expects($this->once())
            ->method('getAdSchedulingBuffer')
            ->willReturn($schedulingBuffer);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAvailableDatesByAdType')
            ->with($adTypeId, $brandId, $channelId, $disabledDates, $schedulingBuffer)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->controller->getAvailableDatesByAdType($channel, $adTypeId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(
            '"Darn, we were unable to retrieve the available dates for ad type 25 right now. We will work on fixing this ASAP. If you need more immediate help in the meantime, ping us in chat and we will help you with your promotion."',
            $actualResults->getContent()
        );
    }

    public function testCanGetAvailableDatesByAdType_returnsJsonResponse()
    {
        $adTypeId = 25;
        $brandId = 4;
        $channel = $this->createMock(Channel::class);
        $channelConfig = $this->createMock(ChannelConfigurationCollection::class);
        $channelId = 1;
        $disabledDates = ["2021-02-30"];
        $response = $this->createMock(Response::class);
        $schedulingBuffer = 72;

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfig);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $channelConfig
            ->expects($this->once())
            ->method('getDisabledDates')
            ->willReturn($disabledDates);

        $channelConfig
            ->expects($this->once())
            ->method('getAdSchedulingBuffer')
            ->willReturn($schedulingBuffer);

        $this->adTypeService
            ->expects($this->once())
            ->method('getAvailableDatesByAdType')
            ->with($adTypeId, $brandId, $channelId, $disabledDates, $schedulingBuffer)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn($disabledDates);

        $actualResults = $this->controller->getAvailableDatesByAdType($channel, $adTypeId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanGetDisabledDatesByAdType_returnsJsonResponse()
    {
        $disabledDates = ['someDisabledDates'];
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/ads/booked-dates-by-type/0')
            ->willReturn($disabledDates);

        $actualResults = $this->controller->getDisabledDatesByAdType($channel, 0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanScaffoldDefaultPromotionTypesForNewChannel_returns201()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->adTypeService
            ->expects($this->once())
            ->method('scaffoldDefaultPromotionTypesForNewChannel')
            ->with(5, 2)
            ->willReturn(true);

        $actualResults = $this->controller->scaffoldDefaultPromotionTypesForNewChannel($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCannotUpdatePromotionTypeTemplate_notAuthorized()
    {
        $channel = $this->createMock(Channel::class);
        $passport = $this->createMock(PassportStamp::class);
        $userService = $this->createMock(UserServiceInterface::class);

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(3);

        $userService->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'brand', $passport, 3)
            ->willReturn(false);

        $actualResults = $this->controller
            ->updatePromotionTypeTemplate($channel, 3, $passport, $this->request, $userService);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotUpdatePromotionTypeTemplate_missingMjml()
    {
        $channel = $this->createMock(Channel::class);
        $passport = $this->createMock(PassportStamp::class);
        $userService = $this->createMock(UserServiceInterface::class);

        $channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(3);

        $userService->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'brand', $passport, 3)
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('input')
            ->with('mjmlTemplate')
            ->willReturn(null);

        $actualResults = $this->controller
            ->updatePromotionTypeTemplate($channel, 3, $passport, $this->request, $userService);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCanUpdatePromotionTypeTemplate_returns200()
    {
        $channel = $this->createMock(Channel::class);
        $passport = $this->createMock(PassportStamp::class);
        $userService = $this->createMock(UserServiceInterface::class);
        $response = $this->createMock(Response::class);

        $channel
            ->expects($this->exactly(2))
            ->method('getBrandId')
            ->willReturn(3);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(4);

        $userService->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'brand', $passport, 3)
            ->willReturn(true);

        $this->request->expects($this->once())
            ->method('input')
            ->with('mjmlTemplate')
            ->willReturn('<mjml />');

        $this->adTypeService->expects($this->once())
            ->method('updatePromotionTypeTemplate')
            ->with(3, 4, 3, '<mjml />')
            ->willReturn($response);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn('wee');

        $response->expects($this->once())
            ->method('getStatus')
            ->willReturn(200);

        $actualResults = $this->controller
            ->updatePromotionTypeTemplate($channel, 3, $passport, $this->request, $userService);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('wee', $actualResults->getData());
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}
