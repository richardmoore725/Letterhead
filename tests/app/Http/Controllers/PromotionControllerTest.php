<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;
use App\Collections\PromotionCollection;
use App\Events\PromotionStatusChangedEvent;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PromotionController;
use App\Http\Response;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\MessageServiceInterface;
use App\Http\Services\UserServiceInterface;

use App\Models\Channel;
use App\Models\PassportStamp;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Events\Dispatcher as Event;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionControllerTest extends TestCase
{
    private $adService;
    private $channel;
    private $channelService;
    private $controller;
    private $request;

    public function setUp() : void
    {
        $this->adService = $this->createMock(AdServiceInterface::class);
        $this->channel = $this->createMock(Channel::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->controller = new PromotionController();
        $this->event = $this->createMock(Event::class);
        $this->request = $this->createMock(Request::class);
        $this->messageService = $this->createMock(MessageServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
    }

    public function testCannnotCreatePromotion_returnsError()
    {
        $this->adService
            ->expects($this->once())
            ->method('getAdRequestFormattedForMultipartPost')
            ->with($this->channel, $this->request)
            ->willReturn([]);

        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('createPromotion')
            ->with($this->channel, [])
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $jsonResponse = $this->createMock(JsonResponse::class);

        $response->expects($this->once())
            ->method('getJsonResponse')
            ->willReturn($jsonResponse);

        $actualResults = $this->controller->createPendingPromotion($this->adService, $this->channel, $this->event, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanCreatePromotion_returnsSuccess()
    {
        $this->adService
            ->expects($this->once())
            ->method('getAdRequestFormattedForMultipartPost')
            ->with($this->channel, $this->request)
            ->willReturn([]);

        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('createPromotion')
            ->with($this->channel, [])
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $promotion = $this->createMock(Promotion::class);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($promotion);

        $promotion->expects($this->once())
            ->method('convertToArray')
            ->willReturn([]);

        $actualResults = $this->controller->createPendingPromotion($this->adService, $this->channel, $this->event, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatusCode());
    }

    public function testCannotGetPromotions_returnsJsonResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('date', '')
            ->willReturn('2020-02-03');

        $this->request
            ->expects($this->at(1))
            ->method('boolean')
            ->with('resolveContent', false)
            ->willReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('boolean')
            ->with('mjml', false)
            ->willReturn(true);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('status', Promotion::STATUS_NEWLY_CREATED)
            ->willReturn(4);

        $this->channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(4);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(4);

        $response = $this->createMock(Response::class);

        $this->adService->expects($this->once())
            ->method('getPromotions')
            ->with(4, 4, '2020-02-03', false, true, 4)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->controller->getPromotions($this->adService, $this->channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetPromotions_returnsJsonResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('date', '')
            ->willReturn('2020-02-03');

        $this->request
            ->expects($this->at(1))
            ->method('boolean')
            ->with('resolveContent', false)
            ->willReturn(false);

        $this->request
            ->expects($this->at(2))
            ->method('boolean')
            ->with('mjml', false)
            ->willReturn(true);

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('status', Promotion::STATUS_NEWLY_CREATED)
            ->willReturn(4);

        $this->channel
            ->expects($this->once())
            ->method('getBrandId')
            ->willReturn(4);

        $this->channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(4);

        $response = $this->createMock(Response::class);

        $this->adService->expects($this->once())
            ->method('getPromotions')
            ->with(4, 4, '2020-02-03', false, true, 4)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $promotionCollection = $this->createMock(PromotionCollection::class);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($promotionCollection);

        $promotionCollection->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([]);

        $actualResults = $this->controller->getPromotions($this->adService, $this->channel, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetPromotionsFeed_noKey_returnsResponse()
    {
        $this->request->expects($this->at(0))->method('input')->with('key', '');
        $this->request->expects($this->at(1))->method('input')->with('date', '');
        $view = $this->createMock(Factory::class);

        $actualResults = $this->controller->getPromotionsFeed($this->adService, $this->channelService, $this->request, $view);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotGetPromotionsFeed_noDate_returnsResponse()
    {
        $this->request->expects($this->at(0))->method('input')->with('key', '')->willReturn('adsdsds');
        $this->request->expects($this->at(1))->method('input')->with('date', '');
        $view = $this->createMock(Factory::class);

        $actualResults = $this->controller->getPromotionsFeed($this->adService, $this->channelService, $this->request, $view);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotGetPromotionsFeed_noChannel_returnsResponse()
    {
        $this->request->expects($this->at(0))->method('input')->with('key', '')->willReturn('asdasdasd');
        $this->request->expects($this->at(1))->method('input')->with('date', '')->willReturn('2020-02-02');

        $response = $this->createMock(Response::class);

        $this->channelService->expects($this->once())->method('getChannelByBrandApiKey')->with('asdasdasd')->willReturn($response);

        $response->expects($this->once())->method('isError')->willReturn(true);

        $response->expects($this->once())->method('getStatus')->willReturn(500);

        $view = $this->createMock(Factory::class);

        $actualResults = $this->controller->getPromotionsFeed($this->adService, $this->channelService, $this->request, $view);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCannotGetPromotionsFeed_promoBug_returnsResponse()
    {
        $this->request->expects($this->at(0))->method('input')->with('key', '')->willReturn('asdasdasd');
        $this->request->expects($this->at(1))->method('input')->with('date', '')->willReturn('2020-02-02');

        $response = $this->createMock(Response::class);

        $this->channelService->expects($this->once())->method('getChannelByBrandApiKey')->with('asdasdasd')->willReturn($response);

        $response->expects($this->once())->method('isError')->willReturn(false);

        $response->expects($this->once())->method('getData')->willReturn($this->channel);

        $this->channel->expects($this->once())->method('getBrandId')->willReturn(4);
        $this->channel->expects($this->once())->method('getId')->willReturn(4);

        $adServiceResponse = $this->createMock(Response::class);

        $this->adService->expects($this->once())
            ->method('getPromotions')
            ->with(4, 4, '2020-02-02', true, false, Promotion::STATUS_NEWLY_CREATED)
            ->willReturn($adServiceResponse);

        $adServiceResponse->expects($this->once())->method('isError')->willReturn(true);
        $adServiceResponse->expects($this->once())->method('getStatus')->willReturn(500);

        $view = $this->createMock(Factory::class);

        $actualResults = $this->controller->getPromotionsFeed($this->adService, $this->channelService, $this->request, $view);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanGetPromotionsFeed_returnsResponse()
    {
        $this->request->expects($this->at(0))->method('input')->with('key', '')->willReturn('asdasdasd');
        $this->request->expects($this->at(1))->method('input')->with('date', '')->willReturn('2020-02-02');

        $response = $this->createMock(Response::class);

        $this->channelService->expects($this->once())->method('getChannelByBrandApiKey')->with('asdasdasd')->willReturn($response);

        $response->expects($this->once())->method('isError')->willReturn(false);

        $response->expects($this->once())->method('getData')->willReturn($this->channel);

        $this->channel->expects($this->once())->method('getBrandId')->willReturn(4);
        $this->channel->expects($this->once())->method('getId')->willReturn(4);

        $adServiceResponse = $this->createMock(Response::class);

        $this->adService->expects($this->once())
            ->method('getPromotions')
            ->with(4, 4, '2020-02-02', true, false, Promotion::STATUS_NEWLY_CREATED)
            ->willReturn($adServiceResponse);

        $adServiceResponse->expects($this->once())->method('isError')->willReturn(false);

        $promotionCollection = $this->createMock(PromotionCollection::class);

        $adServiceResponse->expects($this->once())->method('getData')->willReturn($promotionCollection);

        $promotionCollection->expects($this->once())->method('getModels')->willReturn([]);

        $view = $this->createMock(Factory::class);
        $viewReturn = $this->createMock(View::class);

        $view->expects($this->once())->method('make')->with('promotions/promotions-feed', [
            'channel' => $this->channel,
            'promotions' => [],
        ])->willReturn($viewReturn);

        $actualResults = $this->controller->getPromotionsFeed($this->adService, $this->channelService, $this->request, $view);

        $this->assertInstanceOf(\Illuminate\Http\Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotApprovePromotion_returnsErrorResponse() {
        $message = 'This promotion has been approved.';
        $promotion = $this->createMock(Promotion::class);
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('updatePromotionStatus')
            ->with($promotion, Promotion::STATUS_APPROVED_FOR_PUBLICATION)
            ->willReturn($promotion);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('message')
            ->willReturn($message);

        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with($message, $promotion->getId(), 'promotion', $passport->getId())
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->controller->updatePromotionStatusToApproved(
            $this->adService,
            $this->event,
            $this->messageService,
            $passport,
            $promotion,
            $this->request
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanApprovePromotion() {
        $message = 'This promotion has been approved.';
        $promotion = $this->createMock(Promotion::class);
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('updatePromotionStatus')
            ->with($promotion, Promotion::STATUS_APPROVED_FOR_PUBLICATION)
            ->willReturn($promotion);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('message')
            ->willReturn($message);

        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with($message, $promotion->getId(), 'promotion', $passport->getId())
            ->willReturn($response);

        $actualResults = $this->controller->updatePromotionStatusToApproved(
            $this->adService,
            $this->event,
            $this->messageService,
            $passport,
            $promotion,
            $this->request
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotRequestChangesToPromotion_messageEmpty() {
        $promotion = $this->createMock(Promotion::class);
        $passport = $this->createMock(PassportStamp::class);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('message')
            ->willReturn(null);

        $actualResults = $this->controller->updatePromotionStatusToRequestChanges(
            $this->adService,
            $this->event,
            $this->messageService,
            $passport,
            $promotion,
            $this->request
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotRequestChangesToPromotion_returnsErrorResponse() {
        $message = 'This promotion has been approved.';
        $promotion = $this->createMock(Promotion::class);
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('updatePromotionStatus')
            ->with($promotion, Promotion::STATUS_CHANGES_REQUESTED)
            ->willReturn($promotion);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('message')
            ->willReturn($message);

        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with($message, $promotion->getId(), 'promotion', $passport->getId())
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->controller->updatePromotionStatusToRequestChanges(
            $this->adService,
            $this->event,
            $this->messageService,
            $passport,
            $promotion,
            $this->request
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanRequestChangesToPromotion() {
        $message = 'This promotion has been approved.';
        $promotion = $this->createMock(Promotion::class);
        $passport = $this->createMock(PassportStamp::class);
        $response = $this->createMock(Response::class);

        $this->adService
            ->expects($this->once())
            ->method('updatePromotionStatus')
            ->with($promotion, Promotion::STATUS_CHANGES_REQUESTED)
            ->willReturn($promotion);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('message')
            ->willReturn($message);

        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with($message, $promotion->getId(), 'promotion', $passport->getId())
            ->willReturn($response);

        $actualResults = $this->controller->updatePromotionStatusToRequestChanges(
            $this->adService,
            $this->event,
            $this->messageService,
            $passport,
            $promotion,
            $this->request
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}
