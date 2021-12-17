<?php

namespace App\Tests;

use App\Http\Services\PlatformEventServiceInterface;
use App\Models\PlatformEvent;
use App\Http\Controllers\PlatformEventController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlatformEventControllerTest extends TestCase
{
  private $controller;
  private $request;
  private $service;
  private $platformEvent;

  public function setUp() : void
  {
    $this->platformEvent = $this->createMock(PlatformEvent::class);

    $this->service = $this->createMock(PlatformEventServiceInterface::class);
    $this->controller = new PlatformEventController($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCanCreatePlatformEvent_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('createPlatformEvent')
    ->willReturn($this->platformEvent);

    $actualResults = $this->controller->createPlatformEvent('Name', 'Test', 'Slug');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotCreatePlatformEvent_returnsNull()
  {
    $this->service
    ->expects($this->once())
    ->method('createPlatformEvent')
    ->willReturn(null);

    $actualResults = $this->controller->createPlatformEvent('Name', 'Test', 'Slug');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanDeletePlatformEvent_returnsTrue()
  {
    $this->service
    ->expects($this->once())
    ->method('deletePlatformEvent')
    ->willReturn(true);

    $actualResults = $this->controller->deletePlatformEvent($this->platformEvent);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(200, $actualResults->getStatusCode());
  }

  public function testCanDeletePlatformEvent_returnsFalse()
  {
    $this->service
    ->expects($this->once())
    ->method('deletePlatformEvent')
    ->willReturn(false);

    $actualResults = $this->controller->deletePlatformEvent($this->platformEvent);

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }

  public function testCanUpdatePlatformEvent_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('updatePlatformEvent')
    ->willReturn($this->platformEvent);

    $actualResults = $this->controller->updatePlatformEvent($this->platformEvent,'Name', 'Test', 'Slug');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
  }

  public function testCannotUpdatePlatformEvent_returnsJsonResponse()
  {
    $this->service
    ->expects($this->once())
    ->method('updatePlatformEvent')
    ->willReturn(null);

    $actualResults = $this->controller->updatePlatformEvent($this->platformEvent,'Name', 'Test', 'Slug');

    $this->assertInstanceOf(JsonResponse::class, $actualResults);
    $this->assertEquals(500, $actualResults->getStatusCode());
  }
}