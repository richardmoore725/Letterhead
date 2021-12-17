<?php

namespace App\Tests;

use App\Http\Response AS ServiceResponse;
use App\Http\Middleware\ApiKeyMiddleware;
use App\Http\Services\ChannelServiceInterface;
use App\Models\Channel;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiKeyMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(ChannelServiceInterface::class);
    $this->middleware = new ApiKeyMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetLetter_returns400()
  {
      $closure = function() {
          return function() {
              return 'hello!';
          };
      };

      $key = '';

      $this->request
          ->expects($this->once())
          ->method('bearerToken')
          ->willReturn($key);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(400, $actualResults->getStatusCode());
  }

  public function testCannotGetChannel_isError()
  {
      $closure = function() {
          return function() {
              return 'hello!';
          };
      };

      $response = $this->createMock(ServiceResponse::class);
      $response->expects($this->once())
          ->method('isError')
          ->willReturn(true);

      $response->expects($this->once())
          ->method('getEndUserMessage')
          ->willReturn('Oh no!');

      $response->expects($this->once())
          ->method('getStatus')
          ->willReturn(401);

      $key = '123';

      $this->request
          ->expects($this->once())
          ->method('bearerToken')
          ->willReturn($key);

      $this->service->expects($this->once())
          ->method('getChannelByBrandApiKey')
          ->with('123')
          ->willReturn($response);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(401, $actualResults->getStatusCode());
  }

    public function testCannotGetChannel_returnsChannelClosure()
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
                    [ "apiKeyMiddleware" ],
                "uses" =>
                    [ "uses"]
            ],
            [
                "id" => null
            ]
        ];

        $response = $this->createMock(ServiceResponse::class);
        $response->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $channel = $this->createMock(Channel::class);

        $response->expects($this->once())
            ->method('getData')
            ->willReturn($channel);

        $key = '123';

        $this->request
            ->expects($this->once())
            ->method('bearerToken')
            ->willReturn($key);

        $this->service->expects($this->once())
            ->method('getChannelByBrandApiKey')
            ->with('123')
            ->willReturn($response);

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(\Closure::class, $actualResults);
    }
}
