<?php

namespace App\Tests\Http;

use App\DTOs\TransactionalEmailDto;
use App\Http\Repositories\TransactionalEmailRepository;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use App\Collections\TransactionalEmailCollection;

class TransactionalEmailRepositoryTest extends TestCase
{
  private $db;
  private $transactionalEmailObject;
  private $transactionalEmailDto;
  private $repository;

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

    $this->transactionalEmailFromDatabase = collect([
      $this->transactionalEmailObject,
    ]);

    $this->transactionalEmailCollection = new TransactionalEmailCollection($this->transactionalEmailFromDatabase);

    $this->repository = new TransactionalEmailRepository();
    $this->db = \Mockery::mock(DatabaseManager::class);
  }

  public function testCanCreateTransactionalEmail_returnsDto()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andReturn(2);

    $this->db
      ->shouldReceive('table->where->first')
      ->andReturn($this->transactionalEmailObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createTransactionalEmail($this->transactionalEmailDto);

    $this->assertInstanceOf(TransactionalEmailDto::class, $actualResults);
  }

  public function testCannotCreateTransactionalEmail_returnsNull()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andThrows(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createTransactionalEmail($this->transactionalEmailDto);

    $this->assertNull($actualResults);
  }

  public function testCannotCreateTransactionalEmail_throwsNewException()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andReturn(2);

    $this->db
      ->shouldReceive('table->where->first')
      ->andThrows(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->createTransactionalEmail($this->transactionalEmailDto);

      $this->assertNull($actualResults);
  }

  public function testCanDeleteTransactionalEmail_returnsTrue()
  {
    $this->db
      ->shouldReceive('table->where->update')
      ->andReturn(true);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->deleteTransactionalEmail($this->transactionalEmailDto);

    $this->assertTrue($actualResults);
  }

  public function testCannotDeleteTransactionalEmail_returnsFalse()
  {
      $this->db
          ->shouldReceive('table->where->update')
          ->andThrows(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->deleteTransactionalEmail($this->transactionalEmailDto);

      $this->assertFalse($actualResults);
  }

  public function testCanUpdateTransactionalEmail_returnsDto()
  {
    $this->db
        ->shouldReceive('table->where->update')
        ->andReturn(true);

    $this->db
        ->shouldReceive('table->insertGetId')
        ->andReturn(2);

    $this->db
        ->shouldReceive('table->where->first')
        ->andReturn($this->transactionalEmailObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->updateTransactionalEmail($this->transactionalEmailDto);

    $this->assertInstanceOf(TransactionalEmailDto::class, $actualResults);
  }

  public function testCannotUpdateTransactionalEmail_Exception_returnsNull()
  {
    $this->db
      ->shouldReceive('table->where->update')
      ->andThrow(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->updateTransactionalEmail($this->transactionalEmailDto);

    $this->assertNull($actualResults);
  }

  public function testCannotGetTransactionalEmailByChannelAndEventSlug_returnsNullFromDatabase()
  {
    $this->db
      ->shouldReceive('table->join->where->where->select->first')
      ->andReturn(null);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getTransactionalEmailByChannelAndEventSlug(5, 'hello');

    $this->assertNull($actualResults);
  }

  public function testCannotGetTransactionalEmailsByChannelId_throwNewException()
  {
    $this->db
      ->shouldReceive('table->where->get')
      ->andThrow(new \Exception());

      $actualResults = $this->repository->getTransactionalEmailsByChannelId(19);

      $this->assertEmpty($actualResults);
  }

  public function testCanGetTransactionalEmailsByChannelId_returnsCollection()
  {
    $this->db
    ->shouldReceive('table->where->whereNull->get')
    ->andReturn($this->transactionalEmailFromDatabase);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getTransactionalEmailsByChannelId(19);

    $this->assertInstanceOf(TransactionalEmailCollection::class, $actualResults);
  }

  public function testCanGetTransactionalEmailByChannelAndEventSlug_returnsDto()
  {
    $this->db
      ->shouldReceive('table->join->where->where->select->first')
      ->andReturn($this->transactionalEmailObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getTransactionalEmailByChannelAndEventSlug(5, 'hello');

    $this->assertInstanceOf(TransactionalEmailDto::class, $actualResults);
  }

  public function testCannotGetTransactionalEmailByChannelAndEventSlug_throwsException()
  {
    $this->db
      ->shouldReceive('table->join->where->where->select->first')
      ->andThrow(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getTransactionalEmailByChannelAndEventSlug(5, 'hello');

    $this->assertEmpty($actualResults);
  }
}