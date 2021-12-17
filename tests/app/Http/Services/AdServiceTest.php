<?php

namespace App\Tests\Http;

use App\DTOs\PromotionDto;
use App\Http\Repositories\BeaconRepositoryInterface;
use App\Http\Repositories\PromotionRepository;
use App\Http\Response;
use App\Http\Services\AdService;
use App\Http\Services\BeaconServiceInterface;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\Timezone;
use App\Collections\ChannelConfigurationCollection;
use App\Tests\TestCase;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdServiceTest extends TestCase
{
    private $beaconRepository;
    private $beaconService;
    private $file;
    private $promotionRepository;
    private $request;
    private $service;

    public function setUp(): void
    {
        /**
         * @var BeaconRepositoryInterface
         */
        $this->beaconRepository = $this->createMock(BeaconRepositoryInterface::class);
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->file = $this->createMock(UploadedFile::class);
        $this->promotionRepository = $this->createMock(PromotionRepository::class);
        $this->request = $this->createMock(Request::class);
        $this->service = new AdService($this->beaconRepository, $this->beaconService, $this->promotionRepository);
    }

    public function testCanCalculateApplicationFeeAmount()
    {
        $finalPriceOfPackage = 1000;
        $revenueShare = 0.2;
        $result = 200;

        $actualResults = $this->service->calculateApplicationFeeAmount($finalPriceOfPackage, $revenueShare);

        $this->assertEquals($result, $actualResults);
    }

    public function testCanCalculateFinalPriceOfPackage()
    {
        $amount = 1000;
        $discountValue = 20;
        $result = 800;

        $actualResults = $this->service->calculateFinalPriceOfPackage($amount, $discountValue);

        $this->assertEquals($result, $actualResults);
    }

    public function testCanGetAdRequestFormattedForMultipart__hasPromoterImageButNotAdvertiserLogo_returnsArray()
    {
        $channel = $this->createMock(Channel::class);
        $channelConfigurationCollection = $this->createMock(ChannelConfigurationCollection::class);
        $timezone = $this->createMock(Timezone::class);

        $channel->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->request->expects($this->at(0))
            ->method('input')
            ->with('adCreditId')
            ->willReturn(5);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('adTypeId')
            ->willReturn(5);

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('blurb')
            ->willReturn('Hello world');

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('callToAction')
            ->willReturn('Learn more');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('callToActionUrl')
            ->willReturn('https://google.com');

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('content')
            ->willReturn('Hello <p>world</p>');


        $this->request->expects($this->at(6))
            ->method('input')
            ->with('dateStart')
            ->willReturn('2020-02-02');

        $this->request->expects($this->at(7))
            ->method('input')
            ->with('emoji')
            ->willReturn(':)');

        $this->request->expects($this->at(8))
            ->method('input')
            ->with('heading')
            ->willReturn('Wee');

        $this->request->expects($this->at(9))
            ->method('input')
            ->with('platformUserId')
            ->willReturn(1);

        $this->request->expects($this->at(10))
            ->method('input')
            ->with('promoterDisplayName')
            ->willReturn('Jack Jill');

        $this->request->expects($this->at(11))
            ->method('input')
            ->with('requireAdCreditId')
            ->willReturn(true);

        $this->request->expects($this->at(12))
            ->method('input')
            ->with('status')
            ->willReturn(0);

        $this->request->expects($this->at(13))
            ->method('input')
            ->with('promoterImageAlternativeText')
            ->willReturn('Jack Jill');

        $this->request->expects($this->at(14))
            ->method('hasFile')
            ->with('promoterImage')
            ->willReturn(true);

        $this->request->expects($this->at(15))
            ->method('file')
            ->with('promoterImage')
            ->willReturn($this->file);

        $this->file->expects($this->at(0))
            ->method('path')
            ->willReturn('https://google.com');

        $this->file->expects($this->at(1))
            ->method('getClientOriginalName')
            ->willReturn('OriginalName');

        $this->request->expects($this->at(16))
            ->method('hasFile')
            ->with('advertiserLogo')
            ->willReturn(false);

        $this->request->expects($this->at(17))
            ->method('input')
            ->with('advertiserLogo')
            ->willReturn('');

        $channel->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurationCollection);

        $channelConfigurationCollection
            ->expects($this->once())
            ->method('getTimezone')
            ->willReturn($timezone);

        $timezone->expects($this->once())
            ->method('getOffset')
            ->willReturn('-08:00');

        $actualResults = $this->service->getAdRequestFormattedForMultipartPost($channel, $this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanGetAdRequestFormattedForMultipart__hasAdvertiserLogoButNotPromoterImage_returnsArray()
    {
        $channel = $this->createMock(Channel::class);
        $channelConfigurationCollection = $this->createMock(ChannelConfigurationCollection::class);
        $timezone = $this->createMock(Timezone::class);

        $channel->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $this->request->expects($this->at(0))
            ->method('input')
            ->with('adCreditId')
            ->willReturn(5);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('adTypeId')
            ->willReturn(5);

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('blurb')
            ->willReturn('Hello world');

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('callToAction')
            ->willReturn('Learn more');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('callToActionUrl')
            ->willReturn('https://google.com');

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('content')
            ->willReturn('Hello <p>world</p>');


        $this->request->expects($this->at(6))
            ->method('input')
            ->with('dateStart')
            ->willReturn('2020-02-02');

        $this->request->expects($this->at(7))
            ->method('input')
            ->with('emoji')
            ->willReturn(':)');

        $this->request->expects($this->at(8))
            ->method('input')
            ->with('heading')
            ->willReturn('Wee');

        $this->request->expects($this->at(9))
            ->method('input')
            ->with('platformUserId')
            ->willReturn(1);

        $this->request->expects($this->at(10))
            ->method('input')
            ->with('promoterDisplayName')
            ->willReturn('Jack Jill');

        $this->request->expects($this->at(11))
            ->method('input')
            ->with('requireAdCreditId')
            ->willReturn(true);

        $this->request->expects($this->at(12))
            ->method('input')
            ->with('status')
            ->willReturn(0);

        $this->request->expects($this->at(13))
            ->method('input')
            ->with('promoterImageAlternativeText')
            ->willReturn('Jack Jill');

        $this->request->expects($this->at(14))
            ->method('hasFile')
            ->with('promoterImage')
            ->willReturn(false);

        $this->request->expects($this->at(15))
            ->method('input')
            ->with('promoterImage')
            ->willReturn('');

        $this->request->expects($this->at(16))
            ->method('hasFile')
            ->with('advertiserLogo')
            ->willReturn(true);

        $this->request->expects($this->at(17))
            ->method('file')
            ->with('advertiserLogo')
            ->willReturn($this->file);

        $this->file->expects($this->at(0))
            ->method('path')
            ->willReturn('https://google.com');

        $this->file->expects($this->at(1))
            ->method('getClientOriginalName')
            ->willReturn('OriginalName');

        $channel->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($channelConfigurationCollection);

        $channelConfigurationCollection
            ->expects($this->once())
            ->method('getTimezone')
            ->willReturn($timezone);

        $timezone->expects($this->once())
            ->method('getOffset')
            ->willReturn('-08:00');

        $actualResults = $this->service->getAdRequestFormattedForMultipartPost($channel, $this->request);

        $this->assertIsArray($actualResults);
    }

    public function testCanGetPromotionMetricsByChannelId_returnsObject()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(2);

        $objectReturnedFromResult = new \stdClass();

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromService')
            ->willReturn($objectReturnedFromResult);

        $actualResults = $this->service->getPromotionMetricsByChannelId($channel);

        $this->assertInstanceOf('stdClass', $actualResults);
    }

    public function testCannotGetPromotionCreditByPromotionId__returnsNull()
    {
        $promotionId = 20;
        $endpoint = env('SERVICE_ADS_ENDPOINT');
        $key = env('SERVICE_ADS_KEY');
        $path = "{$endpoint}/promotions/{$promotionId}/credit";

        $this->beaconRepository
            ->expects($this->once())
            ->method('getResourceFromService')
            ->with($path, $key)
            ->willReturn(null);

        $actualResults = $this->service->getPromotionCreditByPromotionId($promotionId);

        $this->assertEmpty($actualResults);
    }

    public function testCanSendRequestToOrderAndBookSinglePromotion__ReturnsResponse()
    {
        $adsBeaconSlug = "ads";
        $brandId = 5;
        $channelId = 4;
        $dateStart = '2021-12-01';
        $discountCodeId = 0;
        $discountValue = 0;
        $finalPriceOfPackage = 10000;
        $originalPurchasePrice = 10000;
        $paymentMethod="pm_card_fake";
        $promotionTypeId = 1;
        $revenueShare = 0.10;
        $userId = 1;
        $userName = "Charles";

        $object = $this->createMock(\stdClass::class);

        $adServicePostDataArray = [
            'amount' => $finalPriceOfPackage,
            'brandId' => $brandId,
            'channelId' => $channelId,
            'dateStart' => $dateStart,
            'discountCodeId' => $discountCodeId,
            'discountValue' => $discountValue,
            'platformUserId' => $userId,
            'originalPurchasePrice' => $originalPurchasePrice,
            'promotionTypeId' => $promotionTypeId,
            'stripe_id' => $paymentMethod,
            'revenueShare' => $revenueShare,
            'userName' => $userName
        ];

        $adServiceResourcePath = "brands/{$brandId}/channels/{$channelId}/orders/individual";

        $this->beaconService
            ->expects($this->once())
            ->method("createResourceByBeaconSlug")
            ->with($adsBeaconSlug, $brandId, $channelId, $adServiceResourcePath, $adServicePostDataArray, false)
            ->willReturn($object);

       $actualResults = $this->service->orderAndBookSinglePromotion(
            $brandId,
            $channelId,
            $finalPriceOfPackage,
            $dateStart,
            $discountCodeId,
            $discountValue,
            $userId,
            $originalPurchasePrice,
            $paymentMethod,
            $promotionTypeId,
            $revenueShare,
            $userName
        );

        $this->assertInstanceOf(\stdClass::class, $actualResults);
    }

    public function testCanCreatePromotion_returnsError()
    {
        $channel = $this->createMock(Channel::class);
        $response = $this->createMock(Response::class);

        $this->promotionRepository
            ->expects($this->once())
            ->method('createPromotion')
            ->with([], $channel)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->service->createPromotion($channel, []);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanCreatePromotion_returnsSuccess()
    {
        $channel = $this->createMock(Channel::class);
        $response = $this->createMock(Response::class);

        $this->promotionRepository
            ->expects($this->once())
            ->method('createPromotion')
            ->with([], $channel)
            ->willReturn($response);

        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $promotionDto = $this->createMock(PromotionDto::class);
        $promotion = $this->createMock(Promotion::class);

        $promotionDto->expects($this->once())
            ->method('convertToModel')
            ->willReturn($promotion);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($promotionDto);

        $actualResults = $this->service->createPromotion($channel, []);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGetPromotions_returnsResponse()
    {
        $response = $this->createMock(Response::class);

        $this->promotionRepository
            ->expects($this->once())
            ->method('getPromotions')
            ->willReturn($response);

        $actualResults = $this->service->getPromotions(5, 4, '', true, true, 0);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCannotGetPromotionByPromotionId()
    {
        $response = $this->createMock(Response::class);
        $adId = 1;
        $renderMjml = false;

        $this->promotionRepository
            ->expects($this->once())
            ->method('getPromotion')
            ->with($adId, $renderMjml)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $actualResults = $this->service->getPromotionByPromotionId($adId, $renderMjml);
        $this->assertNull($actualResults);
    }

    public function testCanGetPromotionByPromotionId()
    {
        $promotion = $this->createMock(Promotion::class);
        $response = $this->createMock(Response::class);
        $adId = 1;
        $renderMjml = false;

        $this->promotionRepository
            ->expects($this->once())
            ->method('getPromotion')
            ->with($adId, $renderMjml)
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn($promotion);

        $actualResults = $this->service->getPromotionByPromotionId($adId, $renderMjml);
        $this->assertInstanceOf(Promotion::class, $actualResults);
    }

    public function testCannotUpdatePromotionStatus()
    {
        $promotion = $this->createMock(Promotion::class);
        $response = $this->createMock(Response::class);
        $adId = 1;

        $promotion
            ->expects($this->at(0))
            ->method('getId')
            ->willReturn($adId);

        $promotion
            ->expects($this->at(1))
            ->method('setStatus')
            ->with(Promotion::STATUS_CHANGES_REQUESTED);

        $promotion
            ->expects($this->at(2))
            ->method('convertToArray')
            ->willReturn([]);

        $this->promotionRepository
            ->expects($this->once())
            ->method('updatePromotion')
            ->with($adId, [])
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn(null);

        $actualResults = $this->service->updatePromotionStatus($promotion, Promotion::STATUS_CHANGES_REQUESTED);
        $this->assertNull($actualResults);
    }

    public function testCanUpdatePromotionStatus()
    {
        $promotion = $this->createMock(Promotion::class);
        $response = $this->createMock(Response::class);
        $adId = 1;

        $promotion
            ->expects($this->at(0))
            ->method('getId')
            ->willReturn($adId);

        $promotion
            ->expects($this->at(1))
            ->method('setStatus')
            ->with(Promotion::STATUS_CHANGES_REQUESTED);

        $promotion
            ->expects($this->at(2))
            ->method('convertToArray')
            ->willReturn([]);

        $this->promotionRepository
            ->expects($this->once())
            ->method('updatePromotion')
            ->with($adId, [])
            ->willReturn($response);

        $response
            ->expects($this->once())
            ->method('getData')
            ->willReturn($promotion);

        $actualResults = $this->service->updatePromotionStatus($promotion, Promotion::STATUS_CHANGES_REQUESTED);
        $this->assertInstanceOf(Promotion::class, $actualResults);
    }
}
