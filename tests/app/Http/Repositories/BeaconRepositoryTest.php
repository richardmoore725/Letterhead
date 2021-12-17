<?php

namespace App\Tests\Http;

use App\DTOs\PassportStampDto;
use App\Http\Repositories\AuthRepository;
use App\Http\Repositories\BeaconRepository;
use App\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use \Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\HeaderBag;

class BeaconRepositoryTest extends TestCase
{
    private $client;
    private $db;
    private $headerBag;
    private $httpRequest;
    private $passport;
    private $passportObject;
    private $repository;
    protected $response;
    private $responseBody;
    private $request;

    public function setUp(): void
    {
        $this->client = Mockery::mock(ClientInterface::class);
        $this->db = Mockery::mock(DatabaseManager::class);
        $this->passportObject = new \stdClass();
        $this->passportObject->acc = '123';
        $this->passportObject->exp = '1213232323';
        $this->passportObject->user = new \stdClass();
        $this->passportObject->user->email = 'michael@whereby.us';
        $this->passportObject->user->name = 'Michael';
        $this->passportObject->user->id = 4;
        $this->passportObject->user->permissions = [];
        $this->passportObject->ref = '2j29292';

        $this->passport = new PassportStampDto($this->passportObject, 'weeee');

        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseBody = $this->createMock(StreamInterface::class);
        $this->request = $this->createMock(RequestInterface::class);
        $this->httpRequest = $this->createMock(Request::class);
        $this->headerBag = $this->createMock(HeaderBag::class);
        $this->repository = new BeaconRepository($this->client, $this->httpRequest);
    }

    public function testCanCreateBrandChannelResourceFromService_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->client
            ->shouldReceive('post')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->createBrandChannelResourceFromService('123', '5', '3', 'https://example.com', 'ads', 'packages', false);

        $this->assertEquals($resource, $actualResults);
    }

    public function testCannotCreateBrandChannelResource_RequestException_returnsNull()
    {
        $this->client
            ->shouldReceive('post')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->createBrandChannelResourceFromService('123', '5', '3', 'https://example.com', 'ads', 'packages', false);

        $this->assertNull($actualResults);
    }

    public function testCannotCreateBrandChannelResource_Exception_returnsNull()
    {
        $this->client
            ->shouldReceive('post')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->createBrandChannelResourceFromService('123', '5', '3', 'https://example.com', 'ads', 'packages', false);

        $this->assertNull($actualResults);
    }

    public function testCanDeleteResourceFromService_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->client
            ->shouldReceive('delete')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->deleteResourceFromService('packages/123', 'wee');

        $this->assertEquals($resource, $actualResults);
    }

    public function testCanGetBrandChannelResourceFromService_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->client
            ->shouldReceive('get')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->getBrandChannelResourceFromService('123', '3', '4', 'https://whereby.us', 'ads');

        $this->assertEquals($resource, $actualResults);
    }

    public function testCanGetAdResourceFromService_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->client
            ->shouldReceive('get')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->getAdResourceFromService('123', 'https://whereby.us', 'ads');

        $this->assertEquals($resource, $actualResults);
    }

    public function testCannotGetAdResource_RequestException_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->getAdResourceFromService('123', 'https://whereby.us', 'ads');

        $this->assertNull($actualResults);
    }

    public function testCannotGetAdResource_Exception_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->getAdResourceFromService('123', 'https://whereby.us', 'ads');

        $this->assertNull($actualResults);
    }

    public function testCannotDeleteResource_RequestException_returnsNull()
    {
        $this->client
            ->shouldReceive('delete')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->deleteResourceFromService('wee', 'woo');

        $this->assertNull(($actualResults));
    }

    public function testCannotDeleteResource_Exception_returnsNull()
    {
        $this->client
            ->shouldReceive('delete')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->deleteResourceFromService('123', 'wee');

        $this->assertNull($actualResults);
    }

    public function testCannotGetBrandChannelResource_RequestException_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->getBrandChannelResourceFromService('123', '3', '4', 'https://whereby.us', 'ads');

        $this->assertNull($actualResults);
    }

    public function testCannotGetBrandChannelResource_Exception_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->getBrandChannelResourceFromService('123', '3', '4', 'https://whereby.us', 'ads');

        $this->assertNull($actualResults);
    }

    public function testCanGetResourceFromService_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->httpRequest->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->client
            ->shouldReceive('get')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->getResourceFromService('123', 'https://whereby.us');

        $this->assertEquals($resource, $actualResults);
    }


    public function testCannotGetResourceFromService_throwsClientException_returnsNull()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->httpRequest->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->client
            ->shouldReceive('get')
            ->andThrow(new ClientException('wee', $this->request));

        $actualResults = $this->repository->getResourceFromService('123', 'https://whereby.us');

        $this->assertNull($actualResults);
    }

    public function testCannotGetResourceFromService_throwsConnectException_returnsNull()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->httpRequest->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->client
            ->shouldReceive('get')
            ->andThrow(new ConnectException('wee', $this->request));

        $actualResults = $this->repository->getResourceFromService('123', 'https://whereby.us');

        $this->assertNull($actualResults);
    }

    public function testCannotGetResourceFromService_throwsServerException_returnsNull()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->httpRequest->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->client
            ->shouldReceive('get')
            ->andThrow(new ServerException('wee', $this->request));

        $actualResults = $this->repository->getResourceFromService('123', 'https://whereby.us');

        $this->assertNull($actualResults);
    }

    public function testCannotGetResourceFromService_throwsException_returnsNull()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->httpRequest->headers = $this->headerBag;
        $this->headerBag
            ->expects($this->any())
            ->method('get')
            ->with('origin')
            ->willReturn('https://google.com');

        $this->client
            ->shouldReceive('get')
            ->andThrow(new \Exception('wee'));

        $actualResults = $this->repository->getResourceFromService('123', 'https://whereby.us');

        $this->assertNull($actualResults);
    }

    public function testCanGetResourceFromServiceWithRequestData_returnsResource()
    {
        $resource = new \stdClass();
        $resource->hello = 'world';

        $jsonEncodedContents = json_encode($resource);

        $this->client
            ->shouldReceive('get')
            ->andReturn($this->response);

        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedContents);

        $actualResults = $this->repository->getResourceFromServiceWithRequestData('https://example.com', 'wee', 'packages', false);

        $this->assertEquals($resource, $actualResults);
    }

    public function testCannotGetResourceFromServiceWithRequestData_RequestException_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->getResourceFromServiceWithRequestData('https://example.com', 'wee', 'packages', false);

        $this->assertNull($actualResults);
    }

    public function testCannotGetResourceFromServiceWithRequestData_Exception_returnsNull()
    {
        $this->client
            ->shouldReceive('get')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->getResourceFromServiceWithRequestData('https://example.com', 'wee', 'packages', false);

        $this->assertNull($actualResults);
    }
}
