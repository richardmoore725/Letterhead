<?php

namespace App\Tests\Http;

use App\DTOs\EmailDto;
use App\Http\Repositories\EmailRepository;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use App\Collections\EmailCollection;

class EmailRepositoryTest extends TestCase
{
  private $db;
  private $emailObject;
  private $emailDto;
  private $repository;

  public function setUp(): void
  {
    $this->emailObject = new \stdClass();
    $this->emailObject->brandId = 14;
    $this->emailObject->channelId = 19;
    $this->emailObject->content = 'Content';
    $this->emailObject->description = 'Test';
    $this->emailObject->fromEmail = 'hello@whereby.us';
    $this->emailObject->fromName = 'fromName';
    $this->emailObject->name = 'Name';
    $this->emailObject->subject = 'Subject';
    $this->emailObject->id = 2;
    $this->emailObject->created_at = CarbonImmutable::now()->toDateTimeString();
    $this->emailObject->deleted_at = null;
    $this->emailObject->updated_at = CarbonImmutable::now()->toDateTimeString();
    $this->emailDto = new EmailDto($this->emailObject);

    $this->emailsFromDatabase = collect([
      $this->emailObject,
    ]);

    $this->emailCollection = new EmailCollection($this->emailsFromDatabase);

    $this->repository = new EmailRepository();
    $this->db = \Mockery::mock(DatabaseManager::class);
  }

  public function testCanCreateEmail_returnsDto()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andReturn(2);

    $this->db
      ->shouldReceive('table->where->first')
      ->andReturn($this->emailObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createEmail($this->emailDto);

    $this->assertInstanceOf(EmailDto::class, $actualResults);
  }

  public function testCannotCreateEmail_returnsNull()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andThrows(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->createEmail($this->emailDto);

    $this->assertNull($actualResults);
  }

  public function testCannotCreateEmail_throwsNewException()
  {
    $this->db
      ->shouldReceive('table->insertGetId')
      ->andReturn(2);

    $this->db
      ->shouldReceive('table->where->first')
      ->andThrows(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->createEmail($this->emailDto);
  
      $this->assertNull($actualResults);
  }

  public function testCanDeleteEmail_returnsTrue()
  {
    $this->db
      ->shouldReceive('table->where->update')
      ->andReturn(true);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->deleteEmail($this->emailDto);

    $this->assertTrue($actualResults);
  }

  public function testCannotDeleteEmail_returnsFalse()
  {
      $this->db
          ->shouldReceive('table->where->update')
          ->andThrows(new \Exception());

      app()->instance('db', $this->db);

      $actualResults = $this->repository->deleteEmail($this->emailDto);

      $this->assertFalse($actualResults);
  }

  public function testCanUpdateEmail_returnsDto()
  {
    $this->db
        ->shouldReceive('table->where->update')
        ->andReturn(true);

    $this->db
        ->shouldReceive('table->insertGetId')
        ->andReturn(2);

    $this->db
        ->shouldReceive('table->where->first')
        ->andReturn($this->emailObject);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->updateEmail($this->emailDto);

    $this->assertInstanceOf(EmailDto::class, $actualResults);
  }

  public function testCannotUpdateEmail_Exception_returnsNull()
  {
    $this->db
      ->shouldReceive('table->where->update')
      ->andThrow(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->updateEmail($this->emailDto);

    $this->assertNull($actualResults);
  }

  public function testCannotGetEmailsByChannelId_throwNewException()
  {
    $this->db
      ->shouldReceive('table->where->get')
      ->andThrow(new \Exception());

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getEmailsByChannelId(19);

    $this->assertEmpty($actualResults);
  }

  public function testCanGetEmailsByChannelId_returnsCollection()
  {
    $this->db
    ->shouldReceive('table->where->whereNull->get')
    ->andReturn($this->emailsFromDatabase);

    app()->instance('db', $this->db);

    $actualResults = $this->repository->getEmailsByChannelId(19);

    $this->assertInstanceOf(EmailCollection::class, $actualResults);
  }
}