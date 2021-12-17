<?php

namespace App\Tests;

use App\Http\Middleware\VerifyEmailMiddleware;
use App\Http\Services\EmailServiceInterface;
use App\Models\Email;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class VerifyEmailMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(EmailServiceInterface::class);
    $this->middleware = new VerifyEmailMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetEmail_returns404JsonResponse()
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
              [ "email" ],
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
      $this->assertEquals('Are you sure that email exists?', $actualResults->getContent());
  }

  public function testCanGetEmail_returnsClosure()
  {
    $email = $this->createMock(Email::class);

    $closure = function() {
        return function() {
            return 'hello!';
        };
    };

    $mockRoute = [
        [1],
        [
            "middleware" =>
            [ "email" ],
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
        ->method('getEmailById')
        ->with(1)
        ->willReturn($email);

    $actualResults = $this->middleware->handle($this->request, $closure);

    $this->assertInstanceOf(\Closure::class, $actualResults);
  }
}