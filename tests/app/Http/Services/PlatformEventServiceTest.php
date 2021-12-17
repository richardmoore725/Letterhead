<?php

namespace App\Tests\Http;

use App\DTOs\PlatformEventDto;
use App\Http\Repositories\PlatformEventRepositoryInterface;
use App\Http\Services\PlatformEventService;
use App\Models\PlatformEvent;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;

class PlatformEventServiceTest extends TestCase
{
  private $platformEvent;
  private $platformEventDto;
  private $repository;
  private $service;

  public function setUp(): void
  {
    $this->platformEventDto = new PlatformEventDto();
    $this->platformEventDto->description = 'Test';
    $this->platformEventDto->name = 'Name';
    $this->platformEventDto->eventSlug = 'Slug';
    $this->platformEventDto->id = 1;
    $this->platformEventDto->createdAt = CarbonImmutable::now()->toDateTimeString();
    $this->platformEventDto->updatedAt = CarbonImmutable::now()->toDateTimeString();
    $this->platformEventDto->deletedAt = null;
    $this->platformEvent = new PlatformEvent($this->platformEventDto);
    
    $this->repository = $this->createMock(PlatformEventRepositoryInterface::class);
    $this->service = new PlatformEventService($this->repository);
  }

  public function testCannotGetPlatformEventById_returnsNull()
  {
    $this->repository
        ->expects($this->once())
        ->method('getPlatformEventById')
        ->with(1)
        ->willReturn(null);

    $actualResults = $this->service->getPlatformEventById(1);

    $this->assertNull($actualResults);
  }

  public function testCanGetPlatformEventById_returnsPlatformEvent()
  {
    $this->repository
        ->expects($this->once())
        ->method('getPlatformEventById')
        ->with(1)
        ->willReturn($this->platformEventDto);

    $actualResults = $this->service->getPlatformEventById(1);

    $this->assertEquals($this->platformEvent, $actualResults);
  }
  public function testCanCreatePlatformEvent_returnsDto()
  {
    $this->repository
        ->expects($this->once())
        ->method('createPlatformEvent')
        ->with($this->platformEventDto)
        ->willReturn($this->platformEventDto);

    $actualResults = $this->service->createPlatformEvent($this->platformEvent);

    $this->assertEquals($this->platformEvent, $actualResults);
  }

  public function testCannotCreatePlatformEvent_returnsNewException()
  {
    $this->repository
        ->expects($this->once())
        ->method('createPlatformEvent')
        ->with($this->platformEventDto)
        ->willReturn(null);

    $actualResults = $this->service->createPlatformEvent($this->platformEvent);

    $this->assertNull($actualResults);
  }

  public function testCanDeletePlatformEvent_returnsTrue()
  {
    $this->repository
        ->expects($this->once())
        ->method('deletePlatformEvent')
        ->with($this->platformEvent->convertToDto())
        ->willReturn(true);

    $actualResults = $this->service->deletePlatformEvent($this->platformEvent);

    $this->assertTrue($actualResults);
  }

  public function testCanUpdatePlatformEvent_returnsPlatformEvent()
  {
      $this->repository
          ->expects($this->once())
          ->method('updatePlatformEvent')
          ->with($this->platformEvent->convertToDto())
          ->willReturn($this->platformEventDto);

      $actualResults = $this->service->updatePlatformEvent($this->platformEvent);

      $this->assertEquals($this->platformEvent, $actualResults);
  }
}