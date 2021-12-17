<?php

namespace App\Tests;

use App\Http\Middleware\VerifyPlatformEventMiddleware;
use App\Http\Services\PlatformEventServiceInterface;
use App\Models\PlatformEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyPlatformEventMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(PlatformEventServiceInterface::class);
    $this->middleware = new VerifyPlatformEventMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetPlatformEvent_returns404JsonResponse()
  {
      $closure = function() {
          return function() {
              return 'hello!';
          };
      };

      $mockRoute = [
          [1],
          [
              "middleware" =>
              [ "platformEvent" ],
              "uses" =>
              [ "uses"]
          ],
          [
              "id" => null
          ]
      ];

      $this->request
          ->expects($this->once())
          ->method('route')
          ->willReturn($mockRoute);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(404, $actualResults->getStatusCode());
      $this->assertEquals('Are you sure that platform event exists?', $actualResults->getContent());
  }

  public function testCanGetPlatformEvent_returnsClosure()
  {
    $platformEvent = $this->createMock(PlatformEvent::class);

    $closure = function() {
        return function() {
            return 'hello!';
        };
    };

    $mockRoute = [
        [1],
        [
            "middleware" =>
            [ "platformEvent" ],
            "uses" =>
            [ "uses"]
        ],
        [
            "id" => 1
        ]
    ];

    $this->request
        ->expects($this->once())
        ->method('route')
        ->willReturn($mockRoute);

    $this->service
        ->expects($this->once())
        ->method('getPlatformEventById')
        ->with(1)
        ->willReturn($platformEvent);

    $actualResults = $this->middleware->handle($this->request, $closure);

    $this->assertInstanceOf(\Closure::class, $actualResults);
  }
}