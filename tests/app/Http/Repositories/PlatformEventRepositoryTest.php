<?php

namespace App\Tests\Http;

use App\DTOs\PlatformEventDto;
use App\Http\Repositories\PlatformEventRepository;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class PlatformEventRepositoryTest extends TestCase
{
  private $db;
  private $platformEventObject;
  private $platformEventDto;
  private $repository;

  public function setUp(): void
  {
    $this->platformEventObject = new \stdClass();
    $this->platformEventObject->name = 'Name';
    $this->platformEventObject->description = 'Test';
    $this->platformEventObject->eventSlug = 'Slug';
    $this->platformEventObject->id = 2;
    $this->platformEventObject->created_at = CarbonImmutable::now()->toDateTimeString();
    $this->platformEventObject->deleted_at = null;
    $this->platformEventObject->updated_at = CarbonImmutable::now()->toDateTimeString();
    $this->platformEventDto = new PlatformEventDto($this->platformEventObject);

    $this->repository = new PlatformEventRepository();
    $this->db = \Mockery::mock(DatabaseManager::class);
  }

  public function testCanCreatePlatformEvent_returnsDto()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andReturn(2);

    $this->db
      ->shouldReceive('table->where->first')
      ->andReturn($this->platformEventObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createPlatformEvent($this->platformEventDto);

    $this->assertInstanceOf(PlatformEventDto::class, $actualResults);
  }

  public function testCannotCreatePlatformEvent_returnsNull()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andThrows(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createPlatformEvent($this->platformEventDto);

    $this->assertNull($actualResults);
  }

  public function testCanDeletePlatformEvent_returnsTrue()
  {
    $this->db
      ->shouldReceive('table->where->update')
      ->andReturn(true);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->deletePlatformEvent($this->platformEventDto);

    $this->assertTrue($actualResults);
  }

  public function testCannotDeletePlatformEvent_returnsFalse()
  {
      $this->db
          ->shouldReceive('table->where->update')
          ->andThrows(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->deletePlatformEvent($this->platformEventDto);

      $this->assertFalse($actualResults);
  }

  public function testCanUpdatePlatformEvent_returnsDto()
  {
    $this->db
        ->shouldReceive('table->where->update')
        ->andReturn(true);

    $this->db
        ->shouldReceive('table->insertGetId')
        ->andReturn(2);

    $this->db
        ->shouldReceive('table->where->first')
        ->andReturn($this->platformEventObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->updatePlatformEvent($this->platformEventDto);

    $this->assertInstanceOf(PlatformEventDto::class, $actualResults);
  }

  public function testCannotUpdatePlatformEvent_Exception_returnsNull()
  {
      $this->db
          ->shouldReceive('table->where->update')
          ->andThrow(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->updatePlatformEvent($this->platformEventDto);

      $this->assertNull($actualResults);
  }

  public function testCannotGetPlatformEventById_throwNewException()
  {
    $this->db
      ->shouldReceive('table->where->get')
      ->andThrow(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getPlatformEventById(2);

    $this->assertEmpty($actualResults);
  }

  public function testCannotGetPlatformEventById_returnsNull()
  {
    $this->db
      ->shouldReceive('table->find')
      ->andReturn(null);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getPlatformEventById(2);

    $this->assertEmpty($actualResults);
  }
}