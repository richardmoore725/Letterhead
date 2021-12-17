<?php

namespace App\Tests;

use App\Collections\DiscountCodeCollection;
use App\Http\Repositories\DiscountCodeRepository;
use App\Http\Services\DiscountCodeService;
use App\DTOs\DiscountCodeDto;
use App\Models\DiscountCode;
use Illuminate\Support\Collection;

class DiscountCodeServiceTest extends TestCase
{
    private $repository;
    private $service;

    public function setUp() : void
    {
        $this->discountCodeObject = new \stdClass();
        $this->discountCodeObject->channelId = 1;
        $this->discountCodeObject->createdAt = '2020-12-24';
        $this->discountCodeObject->deletedAt = null;
        $this->discountCodeObject->discountCode = 'TESTCODE';
        $this->discountCodeObject->discountValue = '42';
        $this->discountCodeObject->displayName = 'Test Code';
        $this->discountCodeObject->id = true;
        $this->discountCodeObject->isActive = 1;
        $this->discountCodeObject->updatedAt = '2020-12-25';
        $this->discountCodeDto = new DiscountCodeDto($this->discountCodeObject);
        $this->discountCode = new DiscountCode($this->discountCodeDto);


        $this->repository = $this->createMock(DiscountCodeRepository::class);
        $this->service = new DiscountCodeService($this->repository);
    }

    public function testCanCheckIfCodeWasAlreadyDefined_returnsTrue()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with('testDiscountCode')
            ->willReturn($this->discountCodeDto);

        $actualResults = $this->service->checkIfCodeWasAlreadyDefined('testDiscountCode');

        $this->assertTrue($actualResults);
    }

    public function testCanCheckIfCodeWasAlreadyDefined_returnsFalse()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with('testDiscountCode')
            ->willReturn(null);

        $actualResults = $this->service->checkIfCodeWasAlreadyDefined('testDiscountCode');

        $this->assertFalse($actualResults);
    }

    public function testCannotCreateDiscountCode_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('createDiscountCode')
            ->with($this->discountCodeDto)
            ->willReturn(null);

        $actualResults = $this->service->createDiscountCode($this->discountCode);

        $this->assertNull($actualResults);
    }

    public function testCanCreateDiscountCode_returnsDiscountCode()
    {
        $this->repository
            ->expects($this->once())
            ->method('createDiscountCode')
            ->with($this->discountCodeDto)
            ->willReturn($this->discountCodeDto);

        $actualResults = $this->service->createDiscountCode($this->discountCode);

        $this->assertInstanceOf(DiscountCode::class, $actualResults);
    }

    public function testCannotGetDiscountCodesByChannelId_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodesByChannelId')
            ->with(1)
            ->willReturn(null);

        $actualResults = $this->service->getDiscountCodesByChannelId(1);

        $this->assertNull($actualResults);
    }

    public function testCanGetDiscountCodesByChannelId_returnsDiscountCodeCollection()
    {
        $discountCodeDbResult = collect([
            $this->discountCodeObject,
        ]);

        $discountCodeCollection = new DiscountCodeCollection($discountCodeDbResult->toArray());

        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodesByChannelId')
            ->with(1)
            ->willReturn($discountCodeCollection);

        $actualResults = $this->service->getDiscountCodesByChannelId(1);

        $this->assertInstanceOf(DiscountCodeCollection::class, $actualResults);
    }

    public function testCannotGetDiscountCodeById_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with(1)
            ->willReturn(null);

        $actualResults = $this->service->getDiscountCodeById(1);

        $this->assertNull($actualResults);
    }

    public function testCanGetDiscountCodeById_returnsDiscountCode()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeById')
            ->with(1)
            ->willReturn($this->discountCodeDto);

        $actualResults = $this->service->getDiscountCodeById(1);

        $this->assertInstanceOf(DiscountCode::class, $actualResults);
    }

    public function testCannotGetDiscountCodeByCode_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with('testDiscountCode')
            ->willReturn(null);

        $actualResults = $this->service->getDiscountCodeByCode('testDiscountCode');

        $this->assertNull($actualResults);
    }

    public function testCanGetDiscountCodeByCode_returnsDiscountCode()
    {
        $this->repository
            ->expects($this->once())
            ->method('getDiscountCodeByCode')
            ->with('testDiscountCode')
            ->willReturn($this->discountCodeDto);

        $actualResults = $this->service->getDiscountCodeByCode('testDiscountCode');

        $this->assertInstanceOf(DiscountCode::class, $actualResults);
    }

    public function testCannotDeleteDiscountCode_returnsFalse()
    {
        $this->repository
            ->expects($this->once())
            ->method('deleteDiscountcode')
            ->with(1)
            ->willReturn(false);

        $actualResults = $this->service->deleteDiscountCode(1);

        $this->assertFalse($actualResults);
    }

    public function testCanDeleteDiscountCode_returnsTrue()
    {
        $this->repository
            ->expects($this->once())
            ->method('deleteDiscountcode')
            ->with(1)
            ->willReturn(true);

        $actualResults = $this->service->deleteDiscountCode(1);

        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateDiscountCode_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('updateDiscountCode')
            ->with($this->discountCodeDto)
            ->willReturn(null);

        $actualResults = $this->service->updateDiscountCode($this->discountCode);

        $this->assertNull($actualResults);
    }

    public function testCanUpdateDiscountCode_returnsDiscountCode()
    {
        $this->repository
            ->expects($this->once())
            ->method('updateDiscountCode')
            ->with($this->discountCodeDto)
            ->willReturn($this->discountCodeDto);

        $actualResults = $this->service->updateDiscountCode($this->discountCode);

        $this->assertInstanceof(DiscountCode::class, $actualResults);
    }
}