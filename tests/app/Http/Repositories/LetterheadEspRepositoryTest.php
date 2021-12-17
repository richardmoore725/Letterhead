<?php

namespace App\Tests\Http;

use App\Collections\ChannelSubscriberCollection;
use Rollbar\Payload\Level;
use Rollbar\Rollbar;
use App\Http\Response;
use App\Http\Repositories\LetterheadEspRepository;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;

class LetterheadEspRepositoryTest extends TestCase
{
  private $db;
  private $subscriberObject;
  private $subscriberDto;
  private $repository;

  public function setUp(): void
  {
      $this->repository = new LetterheadEspRepository();
      $this->db = \Mockery::mock(DatabaseManager::class);
  }

  public function testCanGetSubscribersByChannel_returnsSubscribersCollection()
  {
    $dbResult = $this->createMock(ChannelSubscriberCollection::class);

      $this->db
          ->shouldReceive('table->join->where->select->get')
          ->andReturn($dbResult);

      app()->instance('db', $this->db);

      $actualResults = $this->repository->getSubscribersByChannel(19);
      $this->assertInstanceOf(Response::class, $actualResults);
  }

  public function testCannotGetSubscribersByChannel_ExceptionThrown()
  {
      $this->db
          ->shouldReceive('table->join->whereNull->select->get')
          ->andThrow(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->getSubscribersByChannel(19);
      $this->assertInstanceOf(Response::class, $actualResults);
  }
}
