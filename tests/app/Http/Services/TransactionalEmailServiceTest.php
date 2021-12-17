<?php

namespace App\Tests\Http;

use App\DTOs\TransactionalEmailDto;
use App\Http\Repositories\TransactionalEmailRepositoryInterface;
use App\Http\Services\TransactionalEmailService;
use App\Models\TransactionalEmail;
use App\Models\Channel;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;
use Illuminate\Support\Collection;
use App\Collections\TransactionalEmailCollection;

class TransactionalEmailServiceTest extends TestCase
{
  private $transactionalEmail;
  private $transactionalEmailDto;
  private $repository;
  private $service;

  public function setUp() : void
  {
    $this->transactionalEmailObject = new \stdClass();
    $this->transactionalEmailObject->brandId = 1;
    $this->transactionalEmailObject->channelId = 2;
    $this->transactionalEmailObject->createdAt = CarbonImmutable::now()->toDateTimeString();
    $this->transactionalEmailObject->deletedAt = null;
    $this->transactionalEmailObject->description = 'A thing';
    $this->transactionalEmailObject->emailId = 1;
    $this->transactionalEmailObject->eventId = 1;
    $this->transactionalEmailObject->id = 2;
    $this->transactionalEmailObject->isActive = 1;
    $this->transactionalEmailObject->name = 'Transactional Email test';
    $this->transactionalEmailObject->updatedAt = CarbonImmutable::now()->toDateTimeString();
    $this->transactionalEmailDto = new TransactionalEmailDto($this->transactionalEmailObject);
    $this->transactionalEmail = new TransactionalEmail($this->transactionalEmailDto);

    $transactionalEmailFromDatabase = collect([
      $this->transactionalEmailObject,
    ]);

    $this->transactionalEmailCollection = new TransactionalEmailCollection($transactionalEmailFromDatabase);

    $this->repository = $this->createMock(TransactionalEmailRepositoryInterface::class);
    $this->service = new TransactionalEmailService($this->repository);
  }

  public function testCanCreateTransactionalEmail_returnsDto()
  {
    $this->repository
        ->expects($this->once())
        ->method('createTransactionalEmail')
        ->with($this->transactionalEmailDto)
        ->willReturn($this->transactionalEmailDto);

    $actualResults = $this->service->createTransactionalEmail($this->transactionalEmail);

    $this->assertEquals($this->transactionalEmail, $actualResults);
  }

  public function testCannotCreateTransactionalEmail_returnsNewException()
  {
    $this->repository
        ->expects($this->once())
        ->method('createTransactionalEmail')
        ->with($this->transactionalEmailDto)
        ->willReturn(null);

    $actualResults = $this->service->createTransactionalEmail($this->transactionalEmail);

    $this->assertNull($actualResults);
  }

  public function testCannotGetTransactionalEmailByChannelAndEventSlug_returnsNull()
  {
    $channel = $this->createMock(Channel::class);

    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailByChannelAndEventSlug')
        ->with(0, 'hello')
        ->willReturn(null);

    $actualResults = $this->service->getTransactionalEmailByChannelAndEventSlug($channel, 'hello');

    $this->assertNull($actualResults);
  }

  public function testCanDeleteTransactionalEmail_returnsTrue()
  {
    $this->repository
        ->expects($this->once())
        ->method('deleteTransactionalEmail')
        ->with($this->transactionalEmail->convertToDto())
        ->willReturn(true);

    $actualResults = $this->service->deleteTransactionalEmail($this->transactionalEmail);

    $this->assertTrue($actualResults);
  }

  public function testCanUpdateTransactionalEmail_returnsTransactionalEmail()
  {
    $this->repository
        ->expects($this->once())
        ->method('updateTransactionalEmail')
        ->with($this->transactionalEmail->convertToDto())
        ->willReturn($this->transactionalEmailDto);

    $actualResults = $this->service->updateTransactionalEmail($this->transactionalEmail);

    $this->assertEquals($this->transactionalEmail, $actualResults);
  }

  public function testCannotGetTransactionalEmailById_returnsNull()
  {
    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailById')
        ->with(2)
        ->willReturn(null);

    $actualResults = $this->service->getTransactionalEmailById(2);

    $this->assertNull($actualResults);
  }

  public function testCanGetTransactionalEmailById_returnsEmail()
  {
    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailById')
        ->with(2)
        ->willReturn($this->transactionalEmailDto);

    $actualResults = $this->service->getTransactionalEmailById(2);

    $this->assertEquals($this->transactionalEmail, $actualResults);
  }

  public function testCannotGetTransactionalEmailsByChannel_returnsNull()
  {
    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailsByChannelId')
        ->with(2)
        ->willReturn(null);

    $actualResults = $this->service->getTransactionalEmailsByChannel(2);

    $this->assertNull($actualResults);
  }

  public function testCanGetTransactionalEmailsByChannel_returnsEmailCollection()
  {
    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailsByChannelId')
        ->with(2)
        ->willReturn($this->transactionalEmailCollection);

    $actualResults = $this->service->getTransactionalEmailsByChannel(2);

    $this->assertInstanceOf(TransactionalEmailCollection::class, $actualResults);
    $this->assertEquals($this->transactionalEmailCollection, $actualResults);
  }

  public function testCanGetTransactionalEmailByChannelAndEventSlug_returnsModel()
  {
    $channel = $this->createMock(Channel::class);

    $this->repository
        ->expects($this->once())
        ->method('getTransactionalEmailByChannelAndEventSlug')
        ->with(0, 'hello')
        ->willReturn($this->transactionalEmailDto);

    $actualResults = $this->service->getTransactionalEmailByChannelAndEventSlug($channel, 'hello');

    $this->assertInstanceOf(TransactionalEmail::class, $actualResults);
  }
}