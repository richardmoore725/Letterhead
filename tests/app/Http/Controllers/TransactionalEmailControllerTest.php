<?php

namespace App\Tests;

use App\Http\Services\TransactionalEmailServiceInterface;
use App\Models\TransactionalEmail;
use App\Models\Channel;
use App\DTOs\ChannelDto;
use App\Http\Controllers\TransactionalEmailController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Collections\TransactionalEmailCollection;

class TransactionalEmailControllerTest extends TestCase
{
  private $controller;
  private $request;
  private $service;
  private $transactionalEmail;
  private $channel;

  public function setUp() : void
  {
    $this->transactionalEmail = $this->createMock(TransactionalEmail::class);
    $this->channel = $this->createMock(Channel::class);

    $transactionalEmailFromDatabase = collect([]);
    $this->transactionalEmailCollection = new TransactionalEmailCollection($transactionalEmailFromDatabase);

    $this->service = $this->createMock(TransactionalEmailServiceInterface::class);
    $this->controller = new TransactionalEmailController($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCanCreateTransactionalEmail_returnsJsonResponse()
  {

    $this->service
    ->expects($this->once())
    ->method('createTransactionalEmail')
    ->willReturn($this->transactionalEmail);

    $actualResults = $this->controller->createTransactionalEmail(1, 2, 'A Thing', 1, 1, true, 'Transactional Email test');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(200, $actualResults->getStatusCode());
  }

  public function testCannotCreateTransactionalEmail_returnsNull()
  {
    $this->service
    ->expects($this->once())
    ->method('createTransactionalEmail')
    ->willReturn(null);

    $actualResults = $this->controller->createTransactionalEmail(1, 2, 'A Thing', 1, 1, true, 'Transactional Email test');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanDeleteTransactionalEmail_returnsTrue()
  {
    $this->service
    ->expects($this->once())
    ->method('deleteTransactionalEmail')
    ->willReturn(true);

    $actualResults = $this->controller->deleteTransactionalEmail($this->transactionalEmail);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(200, $actualResults->getStatusCode());
  }

  public function testCannotDeleteTransactionalEmail_returnsFalse()
  {
    $this->service
    ->expects($this->once())
    ->method('deleteTransactionalEmail')
    ->willReturn(false);

    $actualResults = $this->controller->deleteTransactionalEmail($this->transactionalEmail);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanUpdateTransactionalEmail_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('updateTransactionalEmail')
    ->willReturn($this->transactionalEmail);

    $actualResults = $this->controller->updateTransactionalEmail($this->transactionalEmail, 1, 2, 1, 1, 1, 'A Thing', 'Transactional Email test');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotUpdateTransactionalEmail_returnsNull()
  {
    $this->service
    ->expects($this->once())
    ->method('updateTransactionalEmail')
    ->willReturn(null);

    $actualResults = $this->controller->updateTransactionalEmail($this->transactionalEmail, 1, 2, 1, 1, 1, 'A Thing', 'Transactional Email test');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanGetTransactionalEmailById_returnsJsonResponse()
  {
    $transactionalEmail = $this->createMock(TransactionalEmail::class);
    $actualResults = $this->controller->getTransactionalEmailById($transactionalEmail);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCanGetTransactionalEmailsByChannel_returnsJsonResponse()
  {
    $channelDto = new ChannelDto();
    $channelDto->id = 3;
    $channel = new Channel($channelDto);

    $this->service
      ->expects($this->once())
      ->method('getTransactionalEmailsByChannel')
      ->willReturn($this->transactionalEmailCollection);

    $actualResults = $this->controller->getTransactionalEmailsByChannel($channel);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotGetTransactionalEmailsByChannel_returnsNull()
  {
    $channelDto = new ChannelDto();
    $channelDto->id = 3;
    $channel = new Channel($channelDto);

    $this->service
      ->expects($this->once())
      ->method('getTransactionalEmailsByChannel')
      ->willReturn(null);

    $actualResults = $this->controller->getTransactionalEmailsByChannel($channel);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(404, $actualResults->getStatusCode());
  }
}