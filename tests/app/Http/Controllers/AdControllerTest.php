<?php

namespace App\Tests;

use App\Collections\BrandConfigurationCollection;
use App\DTOs\BrandDto;
use App\DTOs\PromotionDto;
use App\Http\Controllers\AdController;
use App\Http\Repositories\BeaconRepository;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\BrandServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\DiscountCodeServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Brand;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\PromotionCredit;
use App\Models\User;
use App\Models\PassportStamp;
use App\Models\DiscountCode;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdControllerTest extends TestCase
{
    private $adService;
    private $brandService;
    private $beaconService;
    private $channelService;
    private $userService;
    private $controller;
    private $request;
    private $passport;
    private $event;

    public function setUp() : void
    {
        $this->adService = $this->createMock(AdServiceInterface::class);
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->brandService = $this->createMock(BrandServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->discountCodeService = $this->createMock(DiscountCodeServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->event = $this->createMock(Dispatcher::class);
        $this->controller = new AdController($this->adService, $this->beaconService, $this->brandService, $this->channelService, $this->discountCodeService, $this->event, $this->userService);
        $this->request = $this->createMock(Request::class);
        $this->passport = $this->createMock(PassportStamp::class);
    }

    public function testCanGetOrdersByUserId_returnsJsonResponse()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'users/2/orders')
            ->willReturn([]);

        $actualResults = $this->controller->getOrdersByUserId($this->request, 2);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals([], $actualResults->getData());
    }

    public function testCanGetAdCreditsByUserId_returnsJsonResponse()
    {
        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'users/2/ad-credits')
            ->willReturn([]);

        $actualResults = $this->controller->getAdCreditsByUserId($this->request, 2);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals([], $actualResults->getData());
    }

    public function testCannotGetAds_returns404JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('mjml', 'false')
            ->willReturn('true');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('resolveContent', 'false')
            ->willReturn('true');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('status', Promotion::STATUS_NEWLY_CREATED)
            ->willReturn(0);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/ads?resolveContent=true&mjml=true&status=0')
            ->willReturn([]);

        $actualResults = $this->controller->getAds($this->request, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetAds_returns200JsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('mjml', 'false')
            ->willReturn('true');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('resolveContent', 'false')
            ->willReturn('true');

        $this->request
            ->expects($this->at(3))
            ->method('input')
            ->with('status', Promotion::STATUS_NEWLY_CREATED)
            ->willReturn(0);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/ads?resolveContent=true&mjml=true&status=0')
            ->willReturn(['someAdsInside']);

        $actualResults = $this->controller->getAds($this->request, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanGetAdTypesByChannelId_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->beaconService
            ->expects($this->once())
            ->method('getResourceByBeaconSlug')
            ->with('ads', 0, 0, 'brands/0/channels/0/ads/types')
            ->willReturn(['someAdTypesInside']);

        $actualResults = $this->controller->getAdTypesByChannelId($this->request, 0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(['someAdTypesInside'], $actualResults->getData());
    }

    public function testCanCreateBrandChannelAd_returnsJsonResponse()
    {
        $ad = new \stdClass();
        $channel = $this->createMock(Channel::class);

        $this->adService
            ->expects($this->once())
            ->method('getAdRequestFormattedForMultipartPost')
            ->with($channel, $this->request)
            ->willReturn([]);

        $channel
            ->expects($this->exactly(2))
            ->method('getBrandId')
            ->willReturn(5);

        $channel
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->with('ads', 5, 1, 'brands/5/channels/1/ads', [], true)
            ->willReturn($ad);

        $actualResults = $this->controller->createBrandChannelAd($channel, $this->passport, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotCreateBrandChannelAd_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->adService
            ->expects($this->once())
            ->method('getAdRequestFormattedForMultipartPost')
            ->with($channel, $this->request)
            ->willReturn([]);

        $channel
            ->expects($this->exactly(2))
            ->method('getBrandId')
            ->willReturn(5);

        $channel
            ->expects($this->exactly(2))
            ->method('getId')
            ->willReturn(1);

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->with('ads', 5, 1, 'brands/5/channels/1/ads', [], true)
            ->willReturn(null);

        $actualResults = $this->controller->createBrandChannelAd($channel, $this->passport, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotOrderAdvertisingPackage_incompleteInputs_returns400JsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(null);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotOrderAdvertisingPackage_emptyBrand_returns404JsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(100);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brand = $this->createMock(Brand::class);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn(null);

        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCannotOrderAdvertisingPackage_emptyStripeId_returns403JsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(100);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');


        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brandConfigurations = collect([]);
        $dto = new BrandDto();
        $dto->brandConfigurations = new BrandConfigurationCollection($brandConfigurations);
        $dto->brandName = 'Black Bitter Coffee';
        $dto->brandSlug = 'black-bitter-coffee';
        $dto->channels = [];
        $dto->createdAt = '2020-01-28';
        $dto->id = 5;
        $dto->updatedAt = '2020-01-28';
        $brand = new Brand($dto);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotOrderAdvertisingPackage_emptyChannel_returnsJsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(100);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brand = $this->createMock(Brand::class);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);

        $brand->expects($this->once())->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn(0.0);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn('acct_12334555');


        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->willReturn(null);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCannotOrderAdvertisingPackage_orderApiFails_returnsJsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(100);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->willReturn($channel);

        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);

        $brand->expects($this->once())->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn(0.0);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn('acct_12334555');

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn(null);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCannotOrderAdvertisingPackage_emptyAmount_returnsJsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(0);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->willReturn($channel);

        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);

        $brand->expects($this->once())->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn(0.0);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn('acct_12334555');

        $orderMock = new \stdClass();
        $orderMock->created = '2020-02-03';
        $orderMock->id = 3;
        $orderMock->package = new \stdClass();
        $orderMock->package->name = 'A package';

        $this->beaconService
            ->expects($this->once())
            ->method('createResourceByBeaconSlug')
            ->willReturn($orderMock);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotOrderAdvertisingPackage_emptyInvoice_returns500JsonResponse()
    {
        $this->request->expects($this->at(0))
            ->method('input')
            ->with('amount')
            ->willReturn(100);

        $this->request->expects($this->at(1))
            ->method('input')
            ->with('description')
            ->willReturn('Weee');

        $this->request->expects($this->at(2))
            ->method('input')
            ->with('packageId')
            ->willReturn(5);

        $this->request->expects($this->at(3))
            ->method('input')
            ->with('paymentMethod')
            ->willReturn('pm_2m2j2k2k2');

        $this->request->expects($this->at(4))
            ->method('input')
            ->with('originalPurchasePrice')
            ->willReturn(100);

        $this->request->expects($this->at(5))
            ->method('input')
            ->with('userId')
            ->willReturn(10);

        $this->request->expects($this->at(6))
            ->method('input')
            ->with('userName')
            ->willReturn('Jack Benimble');

        $brand = $this->createMock(Brand::class);
        $channel = $this->createMock(Channel::class);

        $this->brandService
            ->expects($this->once())
            ->method('getBrandById')
            ->willReturn($brand);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->willReturn($channel);

        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);

        $brand->expects($this->once())->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn(0.0);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn('acct_12334555');

        $this->beaconService
            ->expects($this->at(0))
            ->method('createResourceByBeaconSlug')
            ->willReturn(['order']);

        $this->beaconService
            ->expects($this->at(1))
            ->method('createResourceByBeaconSlug')
            ->willReturn(null);

        $actualResults = $this->controller->orderAdvertisingPackage($this->request, 5, 3, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanOrderSinglePromoPackage__returns500Response()
    {
        $brand = $this->createMock(Brand::class);
        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);
        $brandId = 5;
        $channel = $this->createMock(Channel::class);
        $channelId = 4;
        $connectedStripeAccountId = 'acct_12334555';
        $discountCodeObject = $this->createMock(DiscountCode::class);
        $discountCodeId = 0;
        $discountValue = 0;
        $amount = 10000;
        $dateStart = '2021-12-01';
        $paymentMethod = 'usd';
        $order = $this->createMock(\stdClass::class);
        $originalPurchasePrice = 10000;
        $promotionTypeId = 1;
        $revenueShare = 0.10;
        $user = $this->createMock(User::class);
        $userEmail = "test@fake.url";
        $userId = 1;
        $userName = "Test Ing";

        $finalPriceOfPackage = 10000;
        $applicationFeeAmount = 1000;

        $brand
            ->expects($this->once())
            ->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brand
            ->expects($this->once())
            ->method('getId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn($connectedStripeAccountId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn($revenueShare);

        $this->adService
            ->expects($this->once())
            ->method('calculateFinalPriceOfPackage')
            ->with( $amount,$discountValue)
            ->willReturn($finalPriceOfPackage);

        $this->adService
            ->expects($this->once())
            ->method('calculateApplicationFeeAmount')
            ->with($finalPriceOfPackage, $revenueShare)
            ->willReturn($applicationFeeAmount);

        $this->userService
            ->expects($this->once())
            ->method('getOrCreateAndChargeUser')
            ->with(
                $applicationFeeAmount,
                $connectedStripeAccountId,
                'Single-promotion package of type no. 1 for Test Ing',
                $finalPriceOfPackage,
                $paymentMethod,
                $userEmail,
                $userName
            )
            ->willReturn($user);

        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $this->adService
            ->expects($this->once())
            ->method('orderAndBookSinglePromotion')
            ->with(
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
            )
            ->willReturn($order);

        $actualResults = $this->controller->orderSinglePromotionPackage(
            $brand,
            $channel,
            $discountCodeObject,
            $amount,
            $dateStart,
            $paymentMethod,
            $originalPurchasePrice,
            $promotionTypeId,
            $userEmail,
            $userName
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotOrderSinglePromoPackage__OrderCannotBePlaced__returns500Response()
    {
        $brand = $this->createMock(Brand::class);
        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);
        $brandId = 5;
        $channel = $this->createMock(Channel::class);
        $channelId = 4;
        $connectedStripeAccountId = 'acct_12334555';
        $discountCodeObject = $this->createMock(DiscountCode::class);
        $discountCodeId = 0;
        $discountValue = 0;
        $amount = 10000;
        $dateStart = '2021-12-01';
        $paymentMethod = 'usd';
        $order = $this->createMock(\stdClass::class);
        $originalPurchasePrice = 10000;
        $promotionTypeId = 1;
        $revenueShare = 0.10;
        $user = $this->createMock(User::class);
        $userEmail = "test@fake.url";
        $userId = 1;
        $userName = "Test Ing";

        $finalPriceOfPackage = 10000;
        $applicationFeeAmount = 1000;

        $brand
            ->expects($this->once())
            ->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brand
            ->expects($this->once())
            ->method('getId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn($connectedStripeAccountId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn($revenueShare);

        $this->adService
            ->expects($this->once())
            ->method('calculateFinalPriceOfPackage')
            ->with($amount, $discountValue)
            ->willReturn($finalPriceOfPackage);

        $this->adService
            ->expects($this->once())
            ->method('calculateApplicationFeeAmount')
            ->with($finalPriceOfPackage, $revenueShare)
            ->willReturn($applicationFeeAmount);

        $this->userService
            ->expects($this->once())
            ->method('getOrCreateAndChargeUser')
            ->with(
                $applicationFeeAmount,
                $connectedStripeAccountId,
                'Single-promotion package of type no. 1 for Test Ing',
                $finalPriceOfPackage,
                $paymentMethod,
                $userEmail,
                $userName
            )
            ->willReturn($user);

        $user
            ->expects($this->once())
            ->method('getId')
            ->willReturn($userId);

        $this->adService
            ->expects($this->once())
            ->method('orderAndBookSinglePromotion')
            ->with(
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
            )
            ->willReturn(null);

        $actualResults = $this->controller->orderSinglePromotionPackage(
            $brand,
            $channel,
            $discountCodeObject,
            $amount,
            $dateStart,
            $paymentMethod,
            $originalPurchasePrice,
            $promotionTypeId,
            $userEmail,
            $userName
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCannotOrderSinglePromoPackage__UserCannotBeCharged__returns500Response()
    {
        $brand = $this->createMock(Brand::class);
        $brandConfigurations = $this->createMock(BrandConfigurationCollection::class);
        $brandId = 5;
        $channel = $this->createMock(Channel::class);
        $channelId = 4;
        $connectedStripeAccountId = 'acct_12334555';
        $discountCodeObject = $this->createMock(DiscountCode::class);
        $discountCodeId = 0;
        $discountValue = 0;
        $amount = 10000;
        $dateStart = '2021-12-01';
        $paymentMethod = 'usd';
        $order = $this->createMock(\stdClass::class);
        $originalPurchasePrice = 10000;
        $promotionTypeId = 1;
        $revenueShare = 0.10;
        $user = $this->createMock(User::class);
        $userEmail = "test@fake.url";
        $userId = 1;
        $userName = "Test Ing";

        $finalPriceOfPackage = 10000;
        $applicationFeeAmount = 1000;

        $brand
            ->expects($this->once())
            ->method('getBrandConfigurations')
            ->willReturn($brandConfigurations);

        $brand
            ->expects($this->once())
            ->method('getId')
            ->willReturn($brandId);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn($channelId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getStripeAccount')
            ->willReturn($connectedStripeAccountId);

        $brandConfigurations
            ->expects($this->once())
            ->method('getAdvertisingRevenueShare')
            ->willReturn($revenueShare);

        $this->adService
            ->expects($this->once())
            ->method('calculateFinalPriceOfPackage')
            ->with($amount, $discountValue)
            ->willReturn($finalPriceOfPackage);

        $this->adService
            ->expects($this->once())
            ->method('calculateApplicationFeeAmount')
            ->with($finalPriceOfPackage, $revenueShare)
            ->willReturn($applicationFeeAmount);

        $this->userService
            ->expects($this->once())
            ->method('getOrCreateAndChargeUser')
            ->with(
                $applicationFeeAmount,
                $connectedStripeAccountId,
                'Single-promotion package of type no. 1 for Test Ing',
                $finalPriceOfPackage,
                $paymentMethod,
                $userEmail,
                $userName
            )
            ->willReturn(null);

        $actualResults = $this->controller->orderSinglePromotionPackage(
            $brand,
            $channel,
            $discountCodeObject,
            $amount,
            $dateStart,
            $paymentMethod,
            $originalPurchasePrice,
            $promotionTypeId,
            $userEmail,
            $userName
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCannotUpdateAd_returns500JsonResponse()
    {
        $channel = $this->createMock(Channel::class);

        $this->adService
        ->expects($this->once())
        ->method('getAdRequestFormattedForMultipartPost')
        ->with($channel, $this->request)
        ->willReturn(['adServicePostDataArray']);

        $this->beaconService
        ->expects($this->once())
        ->method('createResourceByBeaconSlug')
        ->with('ads', 0, 0, 'ads/0', ['adServicePostDataArray'], true )
        ->willReturn(null);

        $actualResults = $this->controller->updateAd($channel, 0, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateAd_returns200JsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $adUpdated = new \stdClass();

        $this->adService
        ->expects($this->once())
        ->method('getAdRequestFormattedForMultipartPost')
        ->with($channel, $this->request)
        ->willReturn(['adServicePostDataArray']);

        $this->beaconService
        ->expects($this->once())
        ->method('createResourceByBeaconSlug')
        ->with('ads', 0, 0, 'ads/0', ['adServicePostDataArray'], true )
        ->willReturn($adUpdated);

        $actualResults = $this->controller->updateAd($channel, 0, $this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotDeleteAd_returns500JsonResponse()
    {
        $this->beaconService
        ->expects($this->once())
        ->method('deleteResourceFromService')
        ->with('ads', 'ads/0')
        ->willReturn(false);

        $actualResults = $this->controller->deleteAd(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanDeleteAd_returns200JsonResponse()
    {
        $this->beaconService
        ->expects($this->once())
        ->method('deleteResourceFromService')
        ->with('ads', 'ads/0')
        ->willReturn(true);

        $actualResults = $this->controller->deleteAd(0);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanGetPromotionMetricsByChanenlId_returns200JsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $objectReturned = new \stdClass();

        $this->adService
            ->expects($this->once())
            ->method('getPromotionMetricsByChannelId')
            ->with($channel)
            ->willReturn($objectReturned);

        $actualResults = $this->controller->getPromotionMetricsByChannelId($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetAdById_returnJsonResponse()
    {
        $adObjectReturned = new \stdClass();
        $request = $this->createMock(Request::class);

        $request
        ->expects($this->at(0))
        ->method('input')
        ->with('resolveCredit', false)
        ->wilLReturn(false);

        $request
            ->expects($this->at(1))
            ->method('input')
            ->with('mjml', 'false')
            ->willReturn('false');

        $this->beaconService
        ->expects($this->once())
        ->method('getAdResourceByBeaconSlug')
        ->with('ads', 'promotions/1/?mjml=false')
        ->willReturn($adObjectReturned);

        $actualResults = $this->controller->getAdById($request, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertObjectNotHasAttribute('ad', $actualResults->getData());
        $this->assertObjectNotHasAttribute('promotionCredit', $actualResults->getData());
    }

    public function testCanGetAdByIdWithCredit_returnJsonResponse()
    {
        $adObjectReturned = new \stdClass();
        $promotionCredit = $this->createMock(PromotionCredit::class);
        $request = $this->createMock(Request::class);
        $user = $this->createMock(User::class);

        $request
        ->expects($this->at(0))
        ->method('input')
        ->with('resolveCredit', false)
        ->willReturn(true);

        $request
            ->expects($this->at(1))
            ->method('input')
            ->with('mjml', 'false')
            ->willReturn('true');

        $this->beaconService
        ->expects($this->once())
        ->method('getAdResourceByBeaconSlug')
        ->with('ads', 'promotions/1/?mjml=true')
        ->willReturn($adObjectReturned);

        $this->adService
        ->expects($this->once())
        ->method('getPromotionCreditByPromotionId')
        ->with(1)
        ->willReturn($promotionCredit);

        $this->userService
        ->expects($this->once())
        ->method('getUserById')
        ->willReturn($user);

        $actualResults = $this->controller->getAdById($request, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertObjectHasAttribute('ad', $actualResults->getData());
        $this->assertObjectHasAttribute('promotionCredit', $actualResults->getData());
    }

    public function testCannotGetAdById_returnJsonResponse()
    {
        $request = $this->createMock(Request::class);

        $request
        ->expects($this->at(0))
        ->method('input')
        ->with('resolveCredit', false)
        ->wilLReturn(true);

        $request
            ->expects($this->at(1))
            ->method('input')
            ->with('mjml', 'false')
            ->willReturn('false');

        $this->beaconService
        ->expects($this->once())
        ->method('getAdResourceByBeaconSlug')
        ->with('ads', 'promotions/1/?mjml=false')
        ->willReturn(null);

        $actualResults = $this->controller->getAdById($request, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanInterceptBroadcastOfPromotionPublishedEvent_returns200()
    {
        /**
         * Incomplete promotion array that PromotionDto and its model can parse.
         */
        $promotionArray = [
            'heading' => 'Hello',
            'blurb' => 'Hello again',
            'id' => 5,
        ];

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('promotion')
            ->willReturn($promotionArray);

        $promotionDto = new PromotionDto($promotionArray);
        $promotion = $promotionDto->convertToModel();

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('userId')
            ->willReturn(5);

        $actualResults = $this->controller->broadcastPromotionPublishedEvent($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanInterceptBroadcast_rejectsWith400()
    {
        $promotionArray = [];

        $this->request->expects($this->at(0))
            ->method('input')
            ->with('promotion')
            ->willReturn($promotionArray);

        $actualResults = $this->controller->broadcastPromotionPublishedEvent($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCanInterceptBroadcastOfPromotionRescheduledEvent_returns200()
    {
        /**
         * Incomplete promotion array that PromotionDto and its model can parse.
         */
        $promotionArray = [
            'heading' => 'Hello',
            'blurb' => 'Hello again',
            'id' => 5,
        ];

        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('promotion')
            ->willReturn($promotionArray);

        $promotionDto = new PromotionDto($promotionArray);
        $promotion = $promotionDto->convertToModel();

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('userId')
            ->willReturn(5);

        $actualResults = $this->controller->broadcastPromotionRescheduledEvent($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCanInterceptBroadcastPromotionRescheduledEvent_rejectsWith400()
    {
        $promotionArray = [];

        $this->request->expects($this->at(0))
            ->method('input')
            ->with('promotion')
            ->willReturn($promotionArray);

        $actualResults = $this->controller->broadcastPromotionRescheduledEvent($this->request);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }
}
