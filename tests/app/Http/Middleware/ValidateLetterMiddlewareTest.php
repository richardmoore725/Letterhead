<?php

namespace App\Tests;

use App\Http\Middleware\ValidateLetterMiddleware;
use App\Http\Services\LetterServiceInterface;
use App\Models\Channel;
use App\Models\Letter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;

class ValidateLetterMiddlewareTest extends TestCase
{
  private $service;
  private $request;
  private $middleware;

  public function setUp() : void
  {
    $this->service = $this->createMock(LetterServiceInterface::class);
    $this->middleware = new ValidateLetterMiddleware($this->service);
    $this->request = $this->createMock(Request::class);
  }

  public function testCanHandleLetterValidation_returnsClosure()
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
                  [ "uses" ]
          ],
          [
              'campaignId' => '',
              'channel' => $this->createMock(Channel::class),
              'authors' => [2],
              'letterParts' => ['<p>hi</p>'],
              'publicationDate' => '2020-02-02',
              'publicationStatus' => 0,
              'includePromotions' => true,
              'segmentId' => 112233,
              'slug' => 'my-newsletter',
              'subtitle' => '',
              'specialBanner' => '',
              'title' => 'Hello',
          ],
      ];

      $this->request->expects($this->once())->method('route')->willReturn($mockRoute);
      $this->request->expects($this->at(1))->method('input')->willReturn([]);

      $validator = \Mockery::mock('stdClass');
      Validator::swap($validator);

      $validator->shouldReceive('make')->once()->andReturn($validator);
      $validator->shouldReceive('fails')->once()->andReturn(false);

      $letter = $this->createMock(Letter::class);

      $this->request->expects($this->at(2))->method('input')->with('authors', [])->willReturn([2]);
      $this->request->expects($this->at(3))->method('input')->with('campaignId', '')->willReturn('');
      $this->request->expects($this->at(4))->method('input')->with('copyRendered', '')->willReturn('');
      $this->request->expects($this->at(5))->method('input')->with('includePromotions', false)->willReturn(true);
      $this->request->expects($this->at(6))->method('input')->with('mjmlTemplate')->willReturn('');
      $this->request->expects($this->at(7))->method('input')->with('letterParts', [])->willReturn([]);
      $this->request->expects($this->at(8))->method('input')->with('publicationDate')->willReturn('2020-02-03');
      $this->request->expects($this->at(9))->method('input')->with('publicationStatus', 0)->willReturn(1);
      $this->request->expects($this->at(10))->method('input')->with('segmentId')->willReturn(112233);
      $this->request->expects($this->at(11))->method('input')->with('slug')->willReturn('i-am-a-slug');
      $this->request->expects($this->at(12))->method('input')->with('subtitle')->willReturn('i am a subtitle');
      $this->request->expects($this->at(13))->method('input')->with('specialBanner')->willReturn('');
      $this->request->expects($this->at(14))->method('input')->with('title')->willReturn('I am a title');

      $this->service->expects($this->once())
          ->method('generateEmptyLetter')
          ->willReturn($letter);

      $actualResults = $this->middleware->handle($this->request, $closure);

      $this->assertInstanceOf(\Closure::class, $actualResults);
  }

    public function testCannotHandleLetterValidation_returnsJsonResponse()
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
                    [ "uses" ]
            ],
            [
                // No channel! Will be caught my middleware
            ],
        ];

        $this->request->expects($this->at(0))->method('route')->willReturn($mockRoute);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testValidatorFails_returns400JsonResponse()
    {
        $testCampaignId = "campaignId";
        $testChannel = $this->createMock(Channel::class);

        $messageBag = $this->createMock(MessageBag::class);

        $closure = function () {
            return function () {
                return 'hello!';
            };
        };

        $mockRoute = [
            [1],
            [
                "middleware" =>
                    [ "validateLetter" ],
                "uses" =>
                    [ "uses" ]
            ],
            [
                "campaignId" => $testCampaignId,
                "channel" => $testChannel,
            ]
        ];

        $this->request
            ->expects($this->once())
            ->method('route')
            ->willReturn($mockRoute);

        $validator = \Mockery::mock('stdClass');
        Validator::swap($validator);

        $validator->errors = $messageBag;

        $validator
            ->shouldReceive('make')
            ->once()
            ->andReturn($validator);

        $validator
            ->shouldReceive('fails')
            ->once()
            ->andReturn(true);

        $validator
            ->shouldReceive('errors')
            ->once()
            ->andReturn($messageBag);

        $messageBag
            ->expects($this->once())
            ->method('all')
            ->willReturn([]);

        $actualResults = $this->middleware->handle($this->request, $closure);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }
}
