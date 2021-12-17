<?php

namespace App\Tests\Http;

use App\DTOs\EmailDto;
use App\Http\Repositories\EmailRepositoryInterface;
use App\Http\Services\EmailService;
use App\Models\Email;
use Carbon\CarbonImmutable;
use App\Tests\TestCase;
use Illuminate\Support\Collection;
use App\Collections\EmailCollection;

class EmailServiceTest extends TestCase
{
  private $email;
  private $emailDto;
  private $repository;
  private $service;

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
    $this->email = new Email($this->emailDto);

    $emailsFromDataBase = collect([
      $this->emailObject,
    ]);

    $this->emailCollection = new EmailCollection($emailsFromDataBase);

    $this->repository = $this->createMock(EmailRepositoryInterface::class);
    $this->service = new EmailService($this->repository);
  }

  public function testCanCreateEmail_returnsDto()
  {
    $this->repository
        ->expects($this->once())
        ->method('createEmail')
        ->with($this->emailDto)
        ->willReturn($this->emailDto);

    $actualResults = $this->service->createEmail($this->email);

    $this->assertEquals($this->email, $actualResults);
  }

  public function testCannotCreateEmail_returnsNewException()
  {
    $this->repository
        ->expects($this->once())
        ->method('createEmail')
        ->with($this->emailDto)
        ->willReturn(null);

    $actualResults = $this->service->createEmail($this->email);

    $this->assertNull($actualResults);
  }

  public function testCanDeleteEmail_returnsTrue()
  {
    $this->repository
        ->expects($this->once())
        ->method('deleteEmail')
        ->with($this->email->convertToDto())
        ->willReturn(true);

    $actualResults = $this->service->deleteEmail($this->email);

    $this->assertTrue($actualResults);
  }

  public function testCanUpdateEmail_returnsEmail()
  {
      $this->repository
          ->expects($this->once())
          ->method('updateEmail')
          ->with($this->email->convertToDto())
          ->willReturn($this->emailDto);

      $actualResults = $this->service->updateEmail($this->email);

      $this->assertEquals($this->email, $actualResults);
  }

  public function testCannotGetEmailById_returnsNull()
  {
    $this->repository
        ->expects($this->once())
        ->method('getEmailById')
        ->with(2)
        ->willReturn(null);

    $actualResults = $this->service->getEmailById(2);

    $this->assertNull($actualResults);
  }

  public function testCanGetEmailById_returnsEmail()
  {
    $this->repository
        ->expects($this->once())
        ->method('getEmailById')
        ->with(2)
        ->willReturn($this->emailDto);

    $actualResults = $this->service->getEmailById(2);

    $this->assertEquals($this->email, $actualResults);
  }

  public function testCannotGetEmailsByChannel_returnsNull()
  {
    $this->repository
        ->expects($this->once())
        ->method('getEmailsByChannelId')
        ->with(2)
        ->willReturn(null);

    $actualResults = $this->service->getEmailsByChannel(2);

    $this->assertNull($actualResults);
  }

  public function testCanGetEmailsByChannel_returnsEmailCollection()
  {
    $this->repository
        ->expects($this->once())
        ->method('getEmailsByChannelId')
        ->with(2)
        ->willReturn($this->emailCollection);

    $actualResults = $this->service->getEmailsByChannel(2);

    $this->assertInstanceOf(EmailCollection::class, $actualResults);
    $this->assertEquals($this->emailCollection, $actualResults);
  }
}