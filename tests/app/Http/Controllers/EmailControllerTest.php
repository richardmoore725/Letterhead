<?php

namespace App\Tests;

use App\Http\Services\EmailServiceInterface;
use App\Models\Email;
use App\Models\Channel;
use App\DTOs\ChannelDto;
use App\Http\Controllers\EmailController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Collections\EmailCollection;

class EmailControllerTest extends TestCase
{
  private $controller;
  private $request;
  private $service;
  private $email;
  private $channel;

  public function setUp() : void
  {
    $this->email = $this->createMock(Email::class);
    $this->channel = $this->createMock(Channel::class);

    $emailsFromDataBase = collect([]);
    $this->emailCollection = new EmailCollection($emailsFromDataBase);

    $this->service = $this->createMock(EmailServiceInterface::class);
    $this->controller = new EmailController($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCanCreateEmail_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('createEmail')
    ->willReturn($this->email);

    $actualResults = $this->controller->createEmail(14, 19, 'Content', 'Test', 'hello@whereby.us', 'fromName', 'Name', 'Subject');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotCreateEmail_returnsNull()
  {
    $this->service
    ->expects($this->once())
    ->method('createEmail')
    ->willReturn(null);

    $actualResults = $this->controller->createEmail(14, 19, 'Content', 'Test', 'hello@whereby.us', 'fromName', 'Name', 'Subject');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanDeleteEmail_returnsTrue()
  {
    $this->service
    ->expects($this->once())
    ->method('deleteEmail')
    ->willReturn(true);

    $actualResults = $this->controller->deleteEmail($this->email);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(200, $actualResults->getStatusCode());
  }

  public function testCanDeleteEmail_returnsFalse()
  {
    $this->service
    ->expects($this->once())
    ->method('deleteEmail')
    ->willReturn(false);

    $actualResults = $this->controller->deleteEmail($this->email);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanUpdateEmail_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('updateEmail')
    ->willReturn($this->email);

    $actualResults = $this->controller->updateEmail($this->email, 14, 19, 'Content', 'Test', 'hello@whereby.us', 'fromName', 'Name', 'Subject');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotUpdateEmail_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('updateEmail')
    ->willReturn(null);

    $actualResults = $this->controller->updateEmail($this->email, 14, 19, 'Content', 'Test', 'hello@whereby.us', 'fromName', 'Name', 'Subject');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanGetEmailById_returnsJsonResponse()
  {
    $email = $this->createMock(Email::class);
    $actualResults = $this->controller->getEmailById($email);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotGetEmailsByChannel_returnsJsonResponse()
  {
    $channelDto = new ChannelDto();
    $channelDto->id = 3;
    $channel = new Channel($channelDto);

    $this->service
      ->expects($this->once())
      ->method('getEmailsByChannel')
      ->willReturn(null);

    $actualResults = $this->controller->getEmailsByChannel($channel);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanGetEmailsByChannel_returnsJsonResponse()
  {
    $channelDto = new ChannelDto();
    $channelDto->id = 3;
    $channel = new Channel($channelDto);

    $this->service
      ->expects($this->once())
      ->method('getEmailsByChannel')
      ->willReturn($this->emailCollection);

    $actualResults = $this->controller->getEmailsByChannel($channel);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }
}