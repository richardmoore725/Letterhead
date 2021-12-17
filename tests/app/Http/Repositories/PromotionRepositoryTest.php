<?php

namespace App\Tests\Http;

use App\DTOs\UserDto;
use App\Http\Repositories\PromotionRepository;
use App\Http\Response;
use App\Models\Channel;
use App\Models\PassportStamp;
use App\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use Illuminate\Http\Request;
use Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

class PromotionRepositoryTest extends TestCase
{
    private $client;
    private $headerBag;
    private $httpRequest;
    private $passport;
    private $repository;
    protected $response;
    private $responseBody;
    private $request;

    public function setUp(): void
    {
        $this->client = Mockery::mock(ClientInterface::class);
        $this->headerBag = $this->createMock(HeaderBag::class);
        $this->httpRequest = $this->createMock(Request::class);
        $this->passport = $this->createMock(PassportStamp::class);
        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseBody = $this->createMock(StreamInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->httpRequest->headers = $this->headerBag;

        $this->repository = new PromotionRepository($this->client, $this->httpRequest);
    }

    public function testCannotCreatePromotion_returnsError()
    {
        $channel = $this->createMock(Channel::class);
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(403);

        $actualResults = $this->repository->createPromotion([], $channel);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatus());
    }

    public function testCanCreatePromotion_returnsSuccess()
    {
        $channel = $this->createMock(Channel::class);
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(201);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->createPromotion([], $channel);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatus());
    }

    public function testCanGetPromotions_returnsSuccess()
    {
        $channel = $this->createMock(Channel::class);
        $content = new \stdClass();
        $content->data = [];
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(201);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->getPromotions(
            5,
            2,
            '2020-04-02',
            0,
            0,
            true,
            true,
            0
        );

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }


    public function testCannotGetPromotions_returnsSuccess()
    {
        $channel = $this->createMock(Channel::class);
        $content = new \stdClass();
        $content->data = [];
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(500);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->getPromotions(
            5,
            2,
            '2020-04-02',
            0,
            0,
            true,
            true,
            0
        );

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCannotGetPromotion_ReturnsError()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(403);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->getPromotion(5, false);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatus());
    }

    public function testCanGetPromotion_ReturnsSuccess()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(200);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->getPromotion(5, false);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }

    public function testCannotUpdatePromotion_ReturnsError()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(403);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->updatePromotion(5, []);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(403, $actualResults->getStatus());
    }

    public function testCanUpdatePromotion_ReturnsSuccess()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = json_encode($content);

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
            ->willReturn(200);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContent);

        $actualResults = $this->repository->updatePromotion(5, []);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }
}
