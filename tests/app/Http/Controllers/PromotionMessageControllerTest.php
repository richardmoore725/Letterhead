<?php

namespace app\Http\Controllers;

use App\Collections\MessageCollection;
use App\Http\Controllers\PromotionMessageController;
use App\Http\Response;
use App\Http\Services\MessageServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Models\Brand;
use App\Models\PassportStamp;
use App\Tests\TestCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PromotionMessageControllerTest extends TestCase
{
    private $userService;
    private $messageService;
    private $controller;
    private $request;
    private $passport;
    private $brand;
    private $testMessage;
    private $testPromotionId;

    public function setUp() : void
    {
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->messageService = $this->createMock(MessageServiceInterface::class);
        $this->controller = new PromotionMessageController($this->userService, $this->messageService);
        $this->request = $this->createMock(Request::class);
        $this->passport = $this->createMock(PassportStamp::class);
        $this->brand = $this->createMock(Brand::class);
        $this->testMessage = 'test';
        $this->testPromotionId = 1;
    }

    public function testCanCreateMessage_returnsJsonResponse()
    {
        $response = $this->createMock(Response::class);

        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'brand', $this->passport, $this->brand->getId())
            ->willReturn(true);

        $this->messageService
            ->expects($this->once())
            ->method('createMessage')
            ->with($this->testMessage, $this->testPromotionId)
            ->willReturn($response);

        $actualResults = $this->controller->createMessage(
            $this->brand,
            $this->passport,
            $this->testMessage,
            $this->testPromotionId
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotCreateMessage_cannotCreateOnBrand_returns403Error()
    {
        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('create', 'brand', $this->passport, $this->brand->getId())
            ->willReturn(false);

        $actualResults = $this->controller->createMessage(
            $this->brand,
            $this->passport,
            $this->testMessage,
            $this->testPromotionId
        );

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCanGetMessages_returnsJsonResponse()
    {
        $messageCollection = $this->createMock(MessageCollection::class);

        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('read', 'brand', $this->passport, $this->brand->getId())
            ->willReturn(true);

        $this->request
            ->expects($this->once())
            ->method('get')
            ->with('promotionId')
            ->willReturn($this->testPromotionId);

        $this->messageService
            ->expects($this->once())
            ->method('getMessagesByResource')
            ->with($this->testPromotionId)
            ->willReturn($messageCollection);

        $messageCollection
            ->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([]);

        $actualResults = $this->controller->getMessages($this->request, $this->brand, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetMessages_cannotCreateOnBrand_returns403Error()
    {
        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('read', 'brand', $this->passport, $this->brand->getId())
            ->willReturn(false);

        $actualResults = $this->controller->getMessages($this->request, $this->brand, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatusCode());
    }

    public function testCannotGetMessages_emptyResourceID_returns500Error()
    {
        $this->userService
            ->expects($this->once())
            ->method('checkWhetherUserCanPerformAction')
            ->with('read', 'brand', $this->passport, $this->brand->getId())
            ->willReturn(true);

        $this->request
            ->expects($this->at(0))
            ->method('get')
            ->with('promotionId')
            ->willReturn('');

        $actualResults = $this->controller->getMessages($this->request, $this->brand, $this->passport);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }
}
