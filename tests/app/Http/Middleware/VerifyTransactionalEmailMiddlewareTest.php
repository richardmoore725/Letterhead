<?php

namespace App\Tests;

use App\Http\Middleware\VerifyTransactionalEmailMiddleware;
use App\Http\Services\TransactionalEmailServiceInterface;
use App\Models\TransactionalEmail;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyTransactionalEmailMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(TransactionalEmailServiceInterface::class);
    $this->middleware = new VerifyTransactionalEmailMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetTransactionalEmail_returns404JsonResponse()
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
              [ "transactionalEmail" ],
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
      $this->assertEquals('Are you sure that transactional email exists?', $actualResults->getContent());
  }

  public function testCanGetTransactionalEmail_returnsClosure()
  {
    $transactionalEmail = $this->createMock(TransactionalEmail::class);

    $closure = function() {
        return function() {
            return 'hello!';
        };
    };

    $mockRoute = [
        [1],
        [
            "middleware" =>
            [ "transactionalEmail" ],
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
        ->method('getTransactionalEmailById')
        ->with(1)
        ->willReturn($transactionalEmail);

    $actualResults = $this->middleware->handle($this->request, $closure);

    $this->assertInstanceOf(\Closure::class, $actualResults);
  }
}