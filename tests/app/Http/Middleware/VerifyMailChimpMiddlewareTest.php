<?php

namespace App\Tests;

use App\Http\Middleware\VerifyMailChimpMiddleware;
use App\Http\Services\MailChimpFacadeInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\MailChimpFacade;
use App\Models\Channel;
use App\Collections\ChannelConfigurationCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Carbon\CarbonImmutable;

class VerifyMailChimpMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;
  private $facade;

  public function setUp() : void
  {
    $this->service = $this->createMock(ChannelServiceInterface::class);
    $this->facade = $this->createMock(MailChimpFacadeInterface::class);
    $this->middleware = new VerifyMailChimpMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCannotGetLetter_emptyChannel()
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
            "channel" => null
        ]
    ];

      $this->request
        ->expects($this->once())
        ->method('route')
        ->willReturn($mockRoute);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(500, $actualResults->getStatusCode());
      $this->assertEquals('Woops. We are trying to connect to MailChimp without a channel.', $actualResults->getContent());
  }

  public function testCannotGetLetter_emptyMcApiKey()
  {
    $channel = $this->createMock(Channel::class);
    $channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);

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
            "channel" => $channel
        ]
    ];

      $this->request
        ->expects($this->once())
        ->method('route')
        ->willReturn($mockRoute);

      $channel
        ->expects($this->once())
        ->method('getChannelConfigurations')
        ->willReturn($channelConfigurations);
    
      $channelConfigurations
        ->expects($this->once())
        ->method('getMcApiKey')
        ->willReturn('');

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(Response::class, $actualResults);
      $this->assertEquals(400, $actualResults->getStatusCode());
      $this->assertEquals('Remember to set a valid MailChimp API key so we can connect.', $actualResults->getContent());
  }
}
