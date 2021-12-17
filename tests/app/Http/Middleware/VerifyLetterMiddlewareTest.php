<?php

namespace App\Tests;

use App\Http\Middleware\VerifyLetterMiddleware;
use App\Http\Middleware\VerifyPlatformEventMiddleware;
use App\Http\Services\LetterServiceInterface;
use App\Http\Services\PlatformEventServiceInterface;
use App\Models\Letter;
use App\Models\PlatformEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyLetterMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(LetterServiceInterface::class);
    $this->middleware = new VerifyLetterMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetLetter_returns404JsonResponse()
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
              [ "validateLetter" ],
              "uses" =>
              [ "uses"]
          ],
          [
              "letterId" => 4
          ]
      ];

      $this->request
          ->expects($this->once())
          ->method('route')
          ->willReturn($mockRoute);

      $this->service
          ->expects($this->once())
          ->method('getLetterById')
          ->with(4)
          ->willReturn(null);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(404, $actualResults->getStatusCode());
      $this->assertEquals('Woops. This letter does not exist', $actualResults->getContent());
  }

  public function testCanGetLetter_returnsClosure()
  {
    $letter = $this->createMock(Letter::class);

    $closure = function() {
        return function() {
            return 'hello!';
        };
    };

    $mockRoute = [
        [1],
        [
            "middleware" =>
            [ "verifyLetter" ],
            "uses" =>
            [ "uses"]
        ],
        [
            "letterId" => 1
        ]
    ];

    $this->request
        ->expects($this->once())
        ->method('route')
        ->willReturn($mockRoute);

    $this->service
        ->expects($this->once())
        ->method('getLetterById')
        ->with(1)
        ->willReturn($letter);

    $actualResults = $this->middleware->handle($this->request, $closure);

    $this->assertInstanceOf(\Closure::class, $actualResults);
  }
}
