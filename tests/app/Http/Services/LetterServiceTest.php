<?php

namespace App\Tests\Http;

use App\Collections\ChannelConfigurationCollection;
use App\Collections\LetterCollection;
use App\Collections\LetterPartCollection;
use App\Collections\LettersUsersCollection;
use App\Collections\UserCollection;
use App\DTOs\LetterDto;
use App\DTOs\PromotionDto;
use App\Formatters\LetterDeltaMJMLFormatterInterface;
use App\Http\Repositories\LetterRepositoryInterface;
use App\Http\Repositories\MailChimpRepositoryInterface;
use App\Http\Repositories\ConstantContactRepositoryInterface;
use App\Http\Repositories\MjmlTemplateRepositoryInterface;
use App\Http\Response;
use App\Http\Services\LetterService;
use App\Models\Channel;
use App\Models\Letter;
use App\Models\LetterPart;
use App\Models\Promotion;
use App\Tests\TestCase;
use Carbon\CarbonImmutable;
use App\Tests\Exception;

class LetterServiceTest extends TestCase
{
    private $channel;
    private $dto;
    private $formatter;
    private $letter;
    private $mailchimpRepository;
    private $constantContactRepository;
    private $mjmlTemplateRepository;
    private $repository;
    private $service;
    private $userCollection;

  public function setUp(): void
  {
      $this->channel = $this->createMock(Channel::class);
      $this->dto = new LetterDto();
      $this->dto->campaignId = 'campaignId';
      $this->dto->channelId = 4;
      $this->dto->copyRendered = '';
      $this->dto->createdAt = '2020-08-02';
      $this->dto->deletedAt = null;
      $this->dto->emailServiceProvider = 1;
      $this->dto->emailServiceProviderListId = '';
      $this->dto->emailTemplate = '';
      $this->dto->id = 4;
      $this->dto->mjmlTemplate = '<mj-section><mj-column><mj-text>Hey</mj-text></mj-column></mj-section>';
      $this->dto->publicationDate = '2020-08-03';
      $this->dto->publicationDateOffset = '-05:00';
      $this->dto->publicationStatus = 0;
      $this->dto->includePromotions = true;
      $this->dto->segmentId = 112233;
      $this->dto->slug = 'i-am-a-slug';
      $this->dto->subtitle = 'wee';
      $this->dto->specialBanner = '';
      $this->dto->title = 'hi';
      $this->dto->updatedAt = '2020-08-03';
      $this->dto->uniqueId = '2929292929';
      $this->formatter = $this->createMock(LetterDeltaMJMLFormatterInterface::class);
      $this->letter = new Letter($this->dto);
      $this->repository = $this->createMock(LetterRepositoryInterface::class);
      $this->mailchimpRepository = $this->createMock(MailChimpRepositoryInterface::class);
      $this->constantContactRepository = $this->createMock(ConstantContactRepositoryInterface::class);

      $this->mjmlTemplateRepository = $this->createMock(MjmlTemplateRepositoryInterface::class);
      $this->service = new LetterService($this->formatter, $this->repository, $this->mailchimpRepository, $this->constantContactRepository, $this->mjmlTemplateRepository);
      $this->userCollection = $this->createMock(UserCollection::class);
  }

  public function testCanCreateLetter_returnsLetter()
  {
      $knownDate = CarbonImmutable::create(2020, 8, 21);
      CarbonImmutable::setTestNow($knownDate);
      $authorIds = [5];
      $part = $this->createMock(LetterPart::class);
      $parts = [$part];
      $letter = $this->createMock(Letter::class);
      $letter->expects($this->once())->method('setCreatedAt');
      $letter->expects($this->once())->method('setUpdatedAt');
      $letter->expects($this->once())->method('setUniqueId');
      $letter->expects($this->once())->method('convertToDto')->willReturn($this->dto);

      $this->repository->expects($this->once())
          ->method('createLetter')
          ->with($authorIds, $parts, $this->dto, $knownDate)
          ->willReturn($this->dto);

      $actualResults = $this->service->createLetter($authorIds, $parts, $letter);

      $this->assertInstanceOf(Letter::class, $actualResults);
  }

  public function testGenerateEmptyLetterPart_returnsLetterPart()
  {
      $actualResults = $this->service->generateEmptyLetterPart('wee', 'woo');

      $this->assertInstanceOf(LetterPart::class, $actualResults);
  }

    public function testGenerateEmptyLetterPart_with_id_returnsLetterPart()
    {
        $actualResults = $this->service->generateEmptyLetterPart('wee', 'woo', 5);

        $this->assertInstanceOf(LetterPart::class, $actualResults);
    }


    public function testCanGenerateEmptyLetter_returnsLetter()
  {
      $channel = $this->createMock(Channel::class);
      $date = CarbonImmutable::now()->toDateTimeString();
      $knownDate = CarbonImmutable::create(2020, 8, 21);
      CarbonImmutable::setTestNow($knownDate);

      $channel->expects($this->once())
          ->method('getId')
          ->willReturn(5);

      $actualResults = $this->service->generateEmptyLetter(
          'campaignId',
          $channel,
          $date,
          0,
          true,
          'template',
          0,
          'wee',
          'woo',
          '',
          'why'
      );

      $this->assertInstanceOf(Letter::class, $actualResults);
  }

  public function testCanGetLetterById_returnsLetter()
  {
      $this->repository
          ->expects($this->once())
          ->method('getLetterById')
          ->with(4)
          ->willReturn($this->dto);

      $actualResults = $this->service->getLetterById(4);

      $this->assertInstanceOf(Letter::class, $actualResults);
  }

    public function testCannotGetLetterById_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('getLetterById')
            ->with(4)
            ->willReturn(null);

        $actualResults = $this->service->getLetterById(4);

        $this->assertNull($actualResults);
    }

    public function testCanGetLettersByChannelId_returnsLetterCollection()
    {
        $letterCollection = $this->createMock(LetterCollection::class);

        $this->repository
            ->expects($this->once())
            ->method('getLettersByChannelId')
            ->with(5)
            ->willReturn($letterCollection);

        $actualResults = $this->service->getLettersByChannelId(5);

        $this->assertInstanceOf(LetterCollection::class, $actualResults);
    }

    public function testCannotGeneratePixel_returnsEmptyString()
    {
        $letter = $this->createMock(Letter::class);
        $letter->expects($this->once())
            ->method('getUniqueId')
            ->willReturn('');

        $actualResults = $this->service->getLetterTrackingPixel($letter);

        $this->assertEmpty($actualResults);
    }

    public function testCanGeneratePixel_returnsString()
    {
        $expectedPixel = 'https://pixelservice.local/ranger/123456789/*|UNIQID|*/l.jpg';
        $letter = $this->createMock(Letter::class);
        $letter->expects($this->once())
            ->method('getUniqueId')
            ->willReturn('123456789');

        $actualResults = $this->service->getLetterTrackingPixel($letter);

        $this->assertEquals($expectedPixel, $actualResults);
    }

    public function testCanUpdateLetter_returnsLetter()
    {
        $letterParts = [];
        $this->repository
            ->expects($this->once())
            ->method('updateLetter')
            ->with(5, [2, 3], $letterParts, $this->letter->convertToDto())
            ->willReturn($this->dto);

        $actualResults = $this->service->updateLetter(5, [2, 3], $letterParts, $this->letter);

        $this->assertInstanceOf(Letter::class, $actualResults);
    }

    public function testCannotUpdateLetter_returnsNull()
    {
        $letterParts = [];
        $this->repository
            ->expects($this->once())
            ->method('updateLetter')
            ->with(5, [2, 3], $letterParts, $this->letter->convertToDto())
            ->willReturn(null);

        $actualResults = $this->service->updateLetter(5, [2, 3], $letterParts, $this->letter);

        $this->assertNull($actualResults);
    }

    public function testCannotTest_missingAccessToken()
    {
        $actualResults = $this->service->test($this->channel, 'michael@whereby.us', 0, $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatus());
    }

    public function testCanTest_returnsResponse()
    {
        $config = $this->createMock(ChannelConfigurationCollection::class);
        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($config);

        $config->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('12345');

        $this->mailchimpRepository
            ->expects($this->once())
            ->method('test')
            ->with('12345', 'michael@whereby.us', $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $actualResults = $this->service->test($this->channel, 'michael@whereby.us', 1, $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGenerateLetterEmailTemplate_mjmlTemplateFails()
    {
        $formatterResponse = $this->createMock(Response::class);
        $this->formatter
            ->expects($this->once())
            ->Method('renderMjmlTemplate')
            ->with($this->userCollection, $this->channel, 'deltaJson', $this->letter, [])
            ->willReturn($formatterResponse);

        $formatterResponse->expects($this->once())
            ->method('isError')
            ->willReturn(true);

        $actualResults = $this->service->generateLetterEmailTemplate($this->userCollection, $this->channel, 'deltaJson', $this->letter, []);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGenerateLetterEmailTemplate_htmlTemplateFails()
    {
        $formatterResponse = $this->createMock(Response::class);
        $this->formatter
            ->expects($this->once())
            ->Method('renderMjmlTemplate')
            ->with($this->userCollection, $this->channel, 'deltaJson', $this->letter, [])
            ->willReturn($formatterResponse);

        $formatterResponse->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $formatterResponse->expects($this->once())
            ->method('getData')
            ->willReturn('<mjml />');

        $htmlResponse = $this->createMock(Response::class);

        $htmlResponse->expects($this->once())->method('isError')->willReturn(true);

        $this->mjmlTemplateRepository->expects($this->once())
            ->method('getHtmlFromMjml')
            ->with('<mjml />')
            ->willReturn($htmlResponse);

        $actualResults = $this->service->generateLetterEmailTemplate($this->userCollection, $this->channel, 'deltaJson', $this->letter, []);

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGenerateLetterEmailTemplate_htmlTemplate2Fails()
    {
        $formatterResponse = $this->createMock(Response::class);
        $this->formatter
            ->expects($this->once())
            ->Method('renderMjmlTemplate')
            ->with($this->userCollection, $this->channel, 'deltaJson', $this->letter, [])
            ->willReturn($formatterResponse);

        $formatterResponse->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $formatterResponse->expects($this->once())
            ->method('getData')
            ->willReturn('<mjml />');

        $htmlResponse = $this->createMock(Response::class);

        $htmlResponse->expects($this->once())->method('isError')->willReturn(false);

        $body = new \stdClass();

        $htmlResponse->expects($this->once())
            ->method('getData')
            ->willReturn($body);

        $this->mjmlTemplateRepository->expects($this->once())
            ->method('getHtmlFromMjml')
            ->with('<mjml />')
            ->willReturn($htmlResponse);


        $actualResults = $this->service->generateLetterEmailTemplate($this->userCollection, $this->channel, 'deltaJson', $this->letter, []);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCanGenerateLetterEmailTemplate_letterUpdateFails()
    {
        $letter = $this->createMock(Letter::class);

        $formatterResponse = $this->createMock(Response::class);

        $this->formatter
            ->expects($this->once())
            ->Method('renderMjmlTemplate')
            ->with($this->userCollection, $this->channel, 'deltaJson', $letter, [])
            ->willReturn($formatterResponse);

        $formatterResponse->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $formatterResponse->expects($this->once())
            ->method('getData')
            ->willReturn('<mjml />');

        $htmlResponse = $this->createMock(Response::class);

        $htmlResponse->expects($this->once())->method('isError')->willReturn(false);

        $body = new \stdClass();
        $body->dom = '<html />';

        $htmlResponse->expects($this->once())
            ->method('getData')
            ->willReturn($body);

        $this->mjmlTemplateRepository->expects($this->once())
            ->method('getHtmlFromMjml')
            ->with('<mjml />')
            ->willReturn($htmlResponse);

        $letter->expects($this->once())
            ->method('setEmailTemplate')
            ->with('<html />');

        $letter->expects($this->once())
            ->method('getId')
            ->willReturn(5);

        $lettersUsers = $this->createMock(LettersUsersCollection::class);

        $letter
            ->expects($this->once())
            ->method('getAuthors')
            ->willReturn($lettersUsers);

        $lettersUsers->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([1,3]);

        $letterParts = $this->createMock(LetterPartCollection::class);
        $letterParts->expects($this->any())
            ->method('getArrayOfModels')
            ->willReturn([]);

        $dto = $this->createMock(LetterDto::class);
        $letter->expects($this->once())
            ->method('convertToDto')
            ->willReturn($dto);

        $this->repository->expects($this->once())
            ->method('updateLetter')
            ->willReturn(null);

        $actualResults = $this->service->generateLetterEmailTemplate($this->userCollection, $this->channel, 'deltaJson', $letter, []);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCanGenerateLetterEmailTemplate_letterUpdateSucceeds()
    {
        $letter = $this->createMock(Letter::class);

        $formatterResponse = $this->createMock(Response::class);

        $this->formatter
            ->expects($this->once())
            ->Method('renderMjmlTemplate')
            ->with($this->userCollection, $this->channel, 'deltaJson', $letter, [])
            ->willReturn($formatterResponse);

        $formatterResponse->expects($this->once())
            ->method('isError')
            ->willReturn(false);

        $formatterResponse->expects($this->once())
            ->method('getData')
            ->willReturn('<mjml />');

        $htmlResponse = $this->createMock(Response::class);

        $htmlResponse->expects($this->once())->method('isError')->willReturn(false);

        $body = new \stdClass();
        $body->dom = '<html />';

        $htmlResponse->expects($this->once())
            ->method('getData')
            ->willReturn($body);

        $this->mjmlTemplateRepository->expects($this->once())
            ->method('getHtmlFromMjml')
            ->with('<mjml />')
            ->willReturn($htmlResponse);

        $letter->expects($this->once())
            ->method('setEmailTemplate')
            ->with('<html />');

        $letter->expects($this->once())
            ->method('getId')
            ->willReturn(5);

        $lettersUsers = $this->createMock(LettersUsersCollection::class);

        $letter
            ->expects($this->once())
            ->method('getAuthors')
            ->willReturn($lettersUsers);

        $lettersUsers->expects($this->once())
            ->method('getPublicArray')
            ->willReturn([1,3]);

        $letterParts = $this->createMock(LetterPartCollection::class);
        $letterParts->expects($this->any())
            ->method('getArrayOfModels')
            ->willReturn([]);

        $dto = $this->createMock(LetterDto::class);
        $letter->expects($this->once())
            ->method('convertToDto')
            ->willReturn($dto);

        $this->repository->expects($this->once())
            ->method('updateLetter')
            ->willReturn($dto);

        $actualResults = $this->service->generateLetterEmailTemplate($this->userCollection, $this->channel, 'deltaJson', $letter, []);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }

    public function testCanSend_returnsResponse()
    {
        $config = $this->createMock(ChannelConfigurationCollection::class);
        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($config);

        $config->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('12345');

        $this->mailchimpRepository
            ->expects($this->once())
            ->method('send')
            ->with('12345', $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $actualResults = $this->service->send($this->channel,1, $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCannotSend_noToken_returnsResponse()
    {
        $config = $this->createMock(ChannelConfigurationCollection::class);
        $this->channel
            ->expects($this->once())
            ->method('getChannelConfigurations')
            ->willReturn($config);

        $config->expects($this->once())
            ->method('getMcApiKey')
            ->willReturn('');

        $actualResults = $this->service->send($this->channel,1, $this->letter, 'hey@test.com', 'Jack', 'title', '<p>hello</p>');

        $this->assertInstanceOf(Response::class, $actualResults);
    }
}
