<?php

namespace App\Tests\Http;

use App\Http\Repositories\MjmlTemplateRepository;
use App\Http\Response;
use App\Models\PassportStamp;
use App\Tests\TestCase;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

class MjmlTemplateRepositoryTest extends TestCase
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

        $this->repository = new MjmlTemplateRepository($this->client, $this->httpRequest);
    }

    public function testCanGetHtmlFromMjml_returnsResponse()
    {
        $content = new \stdClass();
        $content->data = true;
        $jsonEncodedContent = \GuzzleHttp\json_encode($content);

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

        $actualResults = $this->repository->getHtmlFromMjml('<mj-section></mj-section>');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
    }
}
