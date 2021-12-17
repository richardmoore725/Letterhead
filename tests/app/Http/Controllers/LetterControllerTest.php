<?php

namespace App\Tests;

use App\Collections\LetterCollection;
use App\Collections\LetterPartCollection;
use App\Collections\LettersUsersCollection;
use App\Collections\UserCollection;
use App\Collections\ChannelConfigurationCollection;
use App\Http\Controllers\LetterController;
use App\Http\Response;
use App\Http\Services\AdServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\LetterService;
use App\Http\Services\UserServiceInterface;
use App\Http\Services\MailChimpFacadeInterface;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\LetterPart;
use App\Models\Promotion;
use App\DTOs\PromotionDto;
use DrewM\MailChimp\MailChimp;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LetterControllerTest extends TestCase
{
  private $adService;
  private $channel;
  private $channelConfigurations;
  private $controller;
  private $letter;
  private $service;
  private $channelService;
  private $userService;
  private $mailChimpService;
  private $request;

  public function setUp() : void
  {
      $this->adService = $this->createMock(AdServiceInterface::class);
      $this->channel = $this->createMock(Channel::class);
      $this->channelConfigurations = $this->createMock(ChannelConfigurationCollection::class);
      $this->letter = $this->createMock(Letter::class);
      $this->service = $this->createMock(LetterService::class);
      $this->channelService = $this->createMock(ChannelServiceInterface::class);
      $this->userService = $this->createMock(UserServiceInterface::class);
      $this->mailChimpService = $this->createMock(MailChimpFacadeInterface::class);
      $this->controller = new LetterController(
          $this->service,
          $this->channelService,
          $this->userService
        );

      $this->request = $this->createMock(Request::class);
  }

  public function testCanCreateLetter_returnsJsonResponse()
  {
      $authors = [3];
      $letter = $this->createMock(Letter::class);
      $part = $this->createMock(LetterPart::class);
      $parts = [
          $part,
      ];

      $letter->expects($this->once())
          ->method('convertToArray')
          ->wilLReturn([]);

      $this->service
          ->expects($this->once())
          ->method('createLetter')
          ->with($authors, $parts, $letter)
          ->willReturn($letter);

      $actualResults = $this->controller->createLetter($authors, $letter, $parts);

      $this->assertInstanceOf(JsonResponse::class, $actualResults);
      $this->assertEquals(201, $actualResults->getStatusCode());
  }

    public function testCannotCreateLetter_returnsJsonResponse()
    {
        $authors = [3];
        $letter = $this->createMock(Letter::class);
        $part = $this->createMock(LetterPart::class);
        $parts = [
            $part,
        ];

        $this->service
            ->expects($this->once())
            ->method('createLetter')
            ->with($authors, $parts, $letter)
            ->willReturn(null);

        $actualResults = $this->controller->createLetter($authors, $letter, $parts);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatusCode());
    }

    public function testCanDeleteLetterById_returnsJsonResponse()
    {
        $letter = $this->createMock(Letter::class);

        $this->service
            ->expects($this->once())
            ->method('getLetterById')
            ->with(1)
            ->willReturn($letter);

        $this->service
            ->expects($this->once())
            ->method('deleteLetter')
            ->with($letter)
            ->willReturn(true);

        $actualResults = $this->controller->deleteLetterById($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatusCode());
    }

    public function testCannotDeleteLetterById_returnsJsonResponse()
    {
        $this->service
            ->expects($this->once())
            ->method('getLetterById')
            ->with(1)
            ->willReturn(null);

        $actualResults = $this->controller->deleteLetterById($this->request, 1);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatusCode());
    }

    public function testCanGetLettersByChanneLId_returnsJsonResponse()
    {
        $channel = $this->createMock(Channel::class);
        $channel->expects($this->once())->method('getId')->willReturn(5);

        $letterCollection = $this->createMock(LetterCollection::class);

        $letterCollection->expects($this->once())->method('getPublicArray')->willReturn([]);

        $this->service->expects($this->once())
            ->method('getLettersByChannelId')
            ->with(5)
            ->willReturn($letterCollection);

        $actualResults = $this->controller->getLettersByChannelId($channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals('[]', $actualResults->getContent());
    }

    public function testCanGetLetterById_returnsJsonResponse()
    {
        $letter = $this->createMock(Letter::class);
        $letter->expects($this->once())->method('convertToArray')->willReturn([]);

        $actualResults = $this->controller->getLetterById($letter);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    /*
    public function testCanUpdateLetter_returnsJsonResponse()
    {
        $letter = $this->createMock(Letter::class);
        $copyRendered = '';
        $letterToUpdate = $this->createMock(Letter::class);
        $letterToUpdate
            ->expects($this->once())
            ->method('setCopyRendered')
            ->with($copyRendered);

        $this->service->expects($this->once())
            ->method('updateLetter')
            ->with(1, ['author1'], ['letterpats'], $letterToUpdate)
            ->willReturn($letter);

        $actualResults = $this->controller->updateLetter(1, ['author1'], $copyRendered, ['letterpats'], $letterToUpdate);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
    */

    public function testCanGetAuthorsByChannel_returnsAuthorsFromCache()
    {
        $authors = $this->createMock(UserCollection::class);
        $cache = \Mockery::mock(Cache::class);

        $channel = $this->createMock(Channel::class);
        $userService = $this->createMock(UserServiceInterface::class);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $cache->shouldReceive('get')->andReturn($authors);

        $actualResults = $this->controller->getAuthorsByChannel($userService, $cache, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanGetAuthorsByChannel_returnsAuthorsFromService()
    {
        $authors = $this->createMock(UserCollection::class);
        $cache = \Mockery::mock(Cache::class);
        $channel = $this->createMock(Channel::class);
        $userService = $this->createMock(UserServiceInterface::class);

        $channel->expects($this->once())
            ->method('getId')
            ->willReturn(10);

        $channel->expects($this->once())
            ->method('getBrandId')
            ->willReturn(5);

        $cache->shouldReceive('get')
            ->andReturn(null);

        $userService->expects($this->once())
            ->method('getBrandAdministrators')
            ->with(5)
            ->willReturn($authors);

        $knownDate = CarbonImmutable::create(2020, 8, 21);
        CarbonImmutable::setTestNow($knownDate);

        $cache->shouldReceive('put')->andReturn(true);

        $actualResults = $this->controller->getAuthorsByChannel($userService, $cache, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCannotSendLetterTestEmail_return404()
    {
        $letter = $this->createMock(Letter::class);
        $channel = $this->createMock(Channel::class);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('toEmail')
            ->willReturn('');

        $actualResults = $this->controller->sendLetterTestEmail($this->mailChimpService, $this->request, $letter, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotSendLetterTestEmail_return404NotValid()
    {
        $letter = $this->createMock(Letter::class);
        $channel = $this->createMock(Channel::class);

        $this->request
            ->expects($this->once())
            ->method('input')
            ->with('toEmail')
            ->willReturn('abcdefg');

        $actualResults = $this->controller->sendLetterTestEmail($this->mailChimpService, $this->request, $letter, $channel);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotSendTestEmailMissingData_returnsResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('delta', '')
            ->willReturn('');

        $letter = $this->createMock(Letter::class);

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('toEmail', '')
            ->willReturn('');

        $actualResults = $this->controller->test($this->adService, $this->request, $this->channel, $this->letter);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatusCode());
    }

    public function testCannotSendTestEmailMissingTemplate_returnsResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('delta', '')
            ->willReturn('deltaJson');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('toEmail', '')
            ->willReturn('michael@whereby.us');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('promotions', '')
            ->willReturn('promotionsJson');

        $this->adService
            ->expects($this->once())
            ->method('getPromotionsFromFromJsonString')
            ->with('promotionsJson')
            ->willReturn([]);

        $letterUsersCollection = $this->createMock(LettersUsersCollection::class);
        $userCollection = $this->createMock(UserCollection::class);
        $this->letter->expects($this->once())
            ->method('getAuthors')
            ->willReturn($letterUsersCollection);

        $letterUsersCollection->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([1,3,4]);

        $this->userService->expects($this->once())
            ->method('getUsersByUserIds')
            ->with([1,3,4])
            ->willReturn($userCollection);

        $templateResponse = $this->createMock(Response::class);
        $this->service->expects($this->once())
            ->method('generateLetterEmailTemplate')
            ->with($userCollection, $this->channel, 'deltaJson', $this->letter, [])
            ->willReturn($templateResponse);

        $templateResponse
            ->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->controller->test($this->adService, $this->request, $this->channel, $this->letter);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }

    public function testCanSendTestEmail_returnsResponse()
    {
        $this->request
            ->expects($this->at(0))
            ->method('input')
            ->with('delta', '')
            ->willReturn('deltaJson');

        $this->request
            ->expects($this->at(1))
            ->method('input')
            ->with('toEmail', '')
            ->willReturn('michael@whereby.us');

        $this->request
            ->expects($this->at(2))
            ->method('input')
            ->with('promotions', '')
            ->willReturn('promotionsJson');

        $this->adService
            ->expects($this->once())
            ->method('getPromotionsFromFromJsonString')
            ->with('promotionsJson')
            ->willReturn([]);

        $letterUsersCollection = $this->createMock(LettersUsersCollection::class);
        $userCollection = $this->createMock(UserCollection::class);
        $this->letter->expects($this->once())
            ->method('getAuthors')
            ->willReturn($letterUsersCollection);

        $letterUsersCollection->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([1,3,4]);

        $this->userService->expects($this->once())
            ->method('getUsersByUserIds')
            ->with([1,3,4])
            ->willReturn($userCollection);

        $this->letter
            ->expects($this->any())
            ->method('getSpecialBanner')
            ->willReturn('');

        $this->channel
            ->expects($this->any())
            ->method('getChannelHorizontalLogo')
            ->willReturn('default banner');

        $templateResponse = $this->createMock(Response::class);
        $this->service->expects($this->once())
            ->method('generateLetterEmailTemplate')
            ->with($userCollection, $this->channel, 'deltaJson', $this->letter, [])
            ->willReturn($templateResponse);

        $templateResponse
            ->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $this->channel
            ->expects($this->exactly(2))
            ->method('getChannelConfigurations')
            ->willReturn($this->channelConfigurations);

        $this->channelConfigurations->expects($this->once())
            ->method('getDefaultFromEmailAddress')
            ->willReturn('hey@test.com');

        $this->channelConfigurations->expects($this->once())
            ->method('getDefaultEmailFromName')
            ->willReturn('William Tell');

        $this->letter->expects($this->once())->method('getTitle')->willReturn('title');

        $this->letter->expects($this->once())->method('getEmailServiceProvider')->willReturn(1);

        $templateResponse->expects($this->once())
            ->method('getData')
            ->willReturn('<p>hi</p>');

        $testEmailResponse = $this->createMock(Response::class);

        $this->service
            ->expects($this->once())
            ->method('test')
            ->with($this->channel, 'michael@whereby.us', 1, $this->letter, 'hey@test.com', 'William Tell', 'title', '<p>hi</p>')
            ->willReturn($testEmailResponse);

        $jsonResponse = $this->createMock(JsonResponse::class);

        $testEmailResponse->expects($this->once())
            ->method('getJsonResponse')
            ->willReturn($jsonResponse);

        $actualResults = $this->controller->test($this->adService, $this->request, $this->channel, $this->letter);

        $this->assertInstanceOf(JsonResponse::class, $actualResults);
    }
}
