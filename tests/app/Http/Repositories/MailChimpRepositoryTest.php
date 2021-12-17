<?php

namespace App\Tests\Http;

use App\DTOs\MailChimpListDto;
use App\Collections\ListCollection;
use App\Collections\SegmentCollection;
use App\Http\Response;
use App\Models\Letter;
use App\Models\PassportStamp;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use DrewM\MailChimp\MailChimp;
use App\Http\Repositories\MailChimpRepository;
use App\Tests\TestCase;
use App\Tests\Http\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

class MailChimpRepositoryTest extends TestCase
{
    private $client;
    private $headerBag;
    private $httpRequest;
    private $letter;
    private $mailChimp;
    private $mailChimpListObject;
    private $mailChimpListDto;
    private $repository;
    protected $response;
    private $responseBody;
    private $request;

    public function setUp(): void
    {
        $this->client = \Mockery::mock(ClientInterface::class);
        $this->headerBag = $this->createMock(HeaderBag::class);
        $this->httpRequest = $this->createMock(Request::class);
        $this->letter = $this->createMock(Letter::class);
        $this->mailChimpListObject = new \stdClass();
        $this->mailChimpListObject->name = 'mail chimp list';
        $this->mailChimpListObject->id = 'abc1234567';
        $this->mailChimpListObject->totalSubscribers = 0;
        $this->mailChimpListObject->clickRate = 0;
        $this->mailChimpListObject->openRate = 0;
        $this->httpRequest->headers = $this->headerBag;
        $this->request = $this->createMock(RequestInterface::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->passport = $this->createMock(PassportStamp::class);
        $this->mailChimpListDto = new MailChimpListDto($this->mailChimpListObject);
        $this->responseBody = $this->createMock(StreamInterface::class);
        $this->repository = new MailChimpRepository($this->client, $this->httpRequest);
        $this->mailChimp = $this->createMock('DrewM\MailChimp\MailChimp');
    }

    public function testCanGetListById_returnsMailChimpListDto()
    {
      $mailChimpListId = 'abc1234567';
      $endpoint = 'lists/abc1234567';
      $arguments = [];

      $results = array();
      $results['status'] = 200;
      $results['details'] = 'details';
      $results['name'] = 'mail chimp list';
      $results['id'] = 'abc1234567';
      $results['stats'] = [
        'member_count' => 0,
        'click_rate' => 0,
        'open_rate' => 0
      ];

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, $arguments)
        ->willReturn($results);

        $actualResults = $this->repository->getListById($this->mailChimp, $mailChimpListId);

        $this->assertEquals($this->mailChimpListDto, $actualResults);
    }

    public function testCannotGetListById_throwsException_falseResults()
    {
      $mailChimpListId = 'abc1234567';
      $endpoint = 'lists/abc1234567';
      $arguments = [];

      $results = null;

      $errorMessage = 'Weeeee';

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, $arguments)
        ->will($this->throwException(new \Exception($errorMessage)));

      $actualResults = $this->repository->getListById($this->mailChimp, $mailChimpListId);

      $this->assertNull($actualResults);
    }

    public function testCannotGetLists()
    {
      $endpoint = "lists";

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, [])
        ->willReturn([]);

      $actualResults = $this->repository->getLists($this->mailChimp);
      $this->assertInstanceOf(ListCollection::class, $actualResults);
    }

    public function testCanGetLists()
    {
      $endpoint = "lists";
      $lists = [
        'lists' => [$this->mailChimpListObject,]
      ];

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, [])
        ->willReturn($lists);

      $actualResults = $this->repository->getLists($this->mailChimp);
      $this->assertInstanceOf(ListCollection::class, $actualResults);
    }

    public function testCanGetListSegments_returnsSegmentCollection()
    {
      $mailChimpListId = 'abc1234567';
      $endpoint = 'lists/abc1234567/segments';
      $arguments = [];

      $results = array();
      $results['segments'] = [
        [
          'id' => 112233,
          'name' => 'cool ppl',
          'member_count' => 1
        ]
      ];
      $results['list_id'] = '5a42a41440';

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, $arguments)
        ->willReturn($results);

        $actualResults = $this->repository->getListSegments($this->mailChimp, $mailChimpListId);

        $this->assertInstanceOf(SegmentCollection::class, $actualResults);
    }

    public function testCannotGetListSegments_throwsException_falseResults()
    {
      $mailChimpListId = 'abc1234567';
      $endpoint = 'lists/abc1234567/segments';
      $arguments = [];

      $results = null;

      $errorMessage = 'Weeeee';

      $this->mailChimp
        ->expects($this->any())
        ->method('get')
        ->with($endpoint, $arguments)
        ->will($this->throwException(new \Exception($errorMessage)));

      $actualResults = $this->repository->getListSegments($this->mailChimp, $mailChimpListId);

      $this->assertInstanceOf(SegmentCollection::class, $actualResults);
    }

    public function testCannotTest_CampaignFails()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->once())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->once()
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->once())
            ->method('getStatusCode')
            ->willReturn(400);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->test('123-us3', 'michael@whereby.us', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatus());
    }

    public function testCannotTest_CampaignTemplateFails()
    {
        $content = [
            'id' => '123434',
        ];
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->responseBody
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->test('123-us2', 'michael@whereby.us', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }

    public function testCannotTest_CampaignResponseHasNoIdFails()
    {
        $content = [
            'notId' => '123434',
        ];
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->responseBody
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->test('123-us2', 'michael@whereby.us', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCanSend()
    {
        $content = [
            'id' => '123434',
        ];
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->responseBody
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->send('123-us2', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }

    public function testCannotSend_returnsCampaignResponseError()
    {
        $content = [
            'id' => '123434',
        ];
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(400);

        $this->responseBody
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->send('123-us2', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(400, $actualResults->getStatus());
    }

    public function testCannotSend_noCampaignId()
    {
        $content = [];
        $jsonEncodedContent = json_encode($content);

        $this->letter->expects($this->once())
            ->method('getEmailServiceProviderListId')
            ->willReturn('123');

        $this->letter->expects($this->once())
            ->method('getSegmentId')
            ->willReturn(122223);

        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://whereby.us');

        $this->client
            ->shouldReceive('request')
            ->andReturn($this->response);

        $this->response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn(200);

        $this->responseBody
            ->expects($this->any())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->send('123-us2', $this->letter, 'michael2@whereby.us', 'Jack', 'title', '<p></p>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }
}
