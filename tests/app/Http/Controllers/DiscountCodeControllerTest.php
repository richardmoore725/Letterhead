<?php

namespace App\Tests;

use App\Collections\DiscountCodeCollection;
use App\Http\Controllers\DiscountCodeController;
use App\Http\Services\DiscountCodeServiceInterface;
use App\DTOs\DiscountCodeDto;
use App\Http\Services\DiscountCodeService;
use App\Models\Channel;
use App\Models\DiscountCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DiscountCodeControllerTest extends TestCase
{
    private $service;
    private $discountCode;

    public function setUp() : void
    {
        $testChannelId = 1;
        $testCreatedAt = '2020-12-22';
        $testDiscountCode = 'testDiscountCode';
        $testDiscountValue = 42;
        $testDisplayName = 'testDisplayName';
        $testId = 1;
        $testIsActive = true;
        $testUpdatedAt = '2020-12-24';

        $this->service = $this->createMock(DiscountCodeServiceInterface::class);
        $dto = new DiscountCodeDto();
        $this->discountCode = new DiscountCode($dto);

        $this->discountCode->setChannelId($testChannelId);
        $this->discountCode->setCreatedAt($testCreatedAt);
        $this->discountCode->setDiscountCode($testDiscountCode);
        $this->discountCode->setDiscountValue($testDiscountValue);
        $this->discountCode->setDisplayName($testDisplayName);
        $this->discountCode->setId($testId);
        $this->discountCode->setIsActive($testIsActive);
        $this->discountCode->setUpdatedAt($testUpdatedAt);

        $this->controller = new DiscountCodeController($this->service);
        $this->request = $this->createMock(Request::class);
    }

    public function testCodeWasNotAlreadyDefined__returnsJsonResponseFalse()
    {
        $testDiscountCode = 'testDiscountCode';

        $this->service
            ->expects($this->once())
            ->method('checkIfCodeWasAlreadyDefined')
            ->with($testDiscountCode)
            ->willReturn(false);

        $actualResults = $this->controller->checkIfCodeWasAlreadyDefined($testDiscountCode);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('false', $actualResults->getContent());
    }

    public function testCodeWasAlreadyDefined__returnsJsonResponseTrue()
    {
        $testDiscountCode = 'testDiscountCode';

        $this->service
            ->expects($this->once())
            ->method('checkIfCodeWasAlreadyDefined')
            ->with($testDiscountCode)
            ->willReturn(true);

        $actualResults = $this->controller->checkIfCodeWasAlreadyDefined($testDiscountCode);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('true', $actualResults->getContent());
    }

    public function testCannotCreateDiscountCode__DiscountCodeAlreadyDefined__returns500Error()
    {
        $testChannelId = 1;
        $testDiscountCode = 'testDiscountCode';
        $testDiscountValue = 42;
        $testDisplayName = 'testDisplayName';
        $testIsActive = true;

        $this->service
            ->expects($this->once())
            ->method('checkIfCodeWasAlreadyDefined')
            ->with($testDiscountCode)
            ->willReturn(true);

        $actualResults = $this->controller->createDiscountCode($testChannelId, $testDiscountCode, $testDiscountValue, $testDisplayName, $testIsActive);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotCreateDiscountCode__returns500Error()
    {
        $testChannelId = 1;
        $testDiscountCode = 'testDiscountCode';
        $testDiscountValue = 42;
        $testDisplayName = 'testDisplayName';
        $testIsActive = true;

        $this->service
        ->expects($this->once())
        ->method('checkIfCodeWasAlreadyDefined')
        ->with($testDiscountCode)
        ->willReturn(false);

        $discountCode = new DiscountCode();

        $discountCode->setChannelId($testChannelId);
        $discountCode->setDiscountCode($testDiscountCode);
        $discountCode->setDiscountValue($testDiscountValue);
        $discountCode->setDisplayName($testDisplayName);
        $discountCode->setIsActive($testIsActive);

        $this->service
            ->expects($this->once())
            ->method('createDiscountCode')
            ->with($discountCode)
            ->willReturn(null);

        $actualResults = $this->controller->createDiscountCode($testChannelId, $testDiscountCode, $testDiscountValue, $testDisplayName, $testIsActive);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }


    public function testCanCreateDiscountCode__returns200Response()
    {
        $testChannelId = 1;
        $testDiscountCode = 'testDiscountCode';
        $testDiscountValue = 42;
        $testDisplayName = 'testDisplayName';
        $testIsActive = true;

        $this->service
            ->expects($this->once())
            ->method('checkIfCodeWasAlreadyDefined')
            ->with($testDiscountCode)
            ->willReturn(false);

        $discountCode = new DiscountCode();

        $discountCode->setChannelId($testChannelId);
        $discountCode->setDiscountCode($testDiscountCode);
        $discountCode->setDiscountValue($testDiscountValue);
        $discountCode->setDisplayName($testDisplayName);
        $discountCode->setIsActive($testIsActive);

        $this->service
            ->expects($this->once())
            ->method('createDiscountCode')
            ->with($discountCode)
            ->willReturn($discountCode);

        $actualResults = $this->controller->createDiscountCode($testChannelId, $testDiscountCode, $testDiscountValue, $testDisplayName, $testIsActive);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotGetDiscountCodes__returns500Error()
    {
        $testChannelId = 1;

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodesByChannelId')
            ->with($testChannelId)
            ->willReturn(null);

        $actualResults = $this->controller->getDiscountCodes($testChannelId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('"Something went wrong"', $actualResults->getContent());
    }

    public function testCanGetDiscountCodes__returns200Response()
    {
        $testChannelId = 1;
        $testCollection = $this->createMock(DiscountCodeCollection::class);

        $this->service
            ->expects($this->once())
            ->method('getDiscountCodesByChannelId')
            ->with($testChannelId)
            ->willReturn($testCollection);

        $actualResults = $this->controller->getDiscountCodes($testChannelId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }


    public function testCannotGetDiscountCode__DiscountCodeChannelIdDoesntMatch__returns200Response()
    {
        $channel = $this->createMock(Channel::class);
        $discountCode = $this->createMock(DiscountCode::class);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn(20);

        $actualResults = $this->controller->getDiscountCode($channel, $discountCode);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetDiscountCode__returns200Response()
    {
        $channel = $this->createMock(Channel::class);
        $discountCode = $this->createMock(DiscountCode::class);

        $channel
            ->expects($this->once())
            ->method('getId')
            ->willReturn(1);

        $discountCode
            ->expects($this->once())
            ->method('getChannelId')
            ->willReturn(1);

        $actualResults = $this->controller->getDiscountCode($channel, $discountCode);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotDeleteDiscountCode__returnsFalseResponse()
    {
        $testDiscountId = 1;

        $this->service
            ->expects($this->once())
            ->method('deleteDiscountCode')
            ->with($testDiscountId)
            ->willReturn(false);

        $actualResults = $this->controller->deleteDiscountCodeById($testDiscountId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('false', $actualResults->getContent());
    }

    public function testCanDeleteDiscountCode__returnsTrueResponse()
    {
        $testDiscountId = 1;

        $this->service
            ->expects($this->once())
            ->method('deleteDiscountCode')
            ->with($testDiscountId)
            ->willReturn(true);

        $actualResults = $this->controller->deleteDiscountCodeById($testDiscountId);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('true', $actualResults->getContent());
    }

    public function testCannotUpdateDiscountCode__returns500Error()
    {
        $testDiscountCode = 'newTestDiscountCode';
        $testDiscountValue = 45;
        $testDisplayName = 'newTestDisplayName';
        $testIsActive = false;

        $discountCode = $this->discountCode;

        $discountCode->setDiscountCode($testDiscountCode);
        $discountCode->setDiscountValue($testDiscountValue);
        $discountCode->setDisplayName($testDisplayName);
        $discountCode->setIsActive($testIsActive);

        $this->service
            ->expects($this->once())
            ->method('updateDiscountCode')
            ->with($discountCode)
            ->willReturn(null);

        $actualResults = $this->controller->updateDiscountCode($discountCode, $testDiscountCode, $testDiscountValue, $testDisplayName, $testIsActive);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanUpdateDiscountCode__returns200Response()
    {
        $testDiscountCode = 'newTestDiscountCode';
        $testDiscountValue = 45;
        $testDisplayName = 'newTestDisplayName';
        $testIsActive = false;

        $discountCode = $this->discountCode;

        $discountCode->setDiscountCode($testDiscountCode);
        $discountCode->setDiscountValue($testDiscountValue);
        $discountCode->setDisplayName($testDisplayName);
        $discountCode->setIsActive($testIsActive);

        $this->service
            ->expects($this->once())
            ->method('updateDiscountCode')
            ->with($discountCode)
            ->willReturn($discountCode);

        $actualResults = $this->controller->updateDiscountCode($discountCode, $testDiscountCode, $testDiscountValue, $testDisplayName, $testIsActive);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }
}