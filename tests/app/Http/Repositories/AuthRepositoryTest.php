<?php

namespace App\Tests\Http;

use App\DTOs\PassportStampDto;
use App\Http\Repositories\AuthRepository;
use App\Tests\TestCase;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ServerException;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Database\DatabaseManager;
use \Mockery;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AuthRepositoryTest extends TestCase
{
    private $cache;
    private $client;
    private $db;
    private $passport;
    private $passportObject;
    private $repository;
    protected $response;
    private $responseBody;
    private $request;

    public function setUp(): void
    {
        $this->cache = $this->createMock(Cache::class);
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

        $this->passport = new PassportStampDto($this->passportObject, '123');

        $this->response = $this->createMock(ResponseInterface::class);
        $this->responseBody = $this->createMock(StreamInterface::class);
        $this->request = $this->createMock(RequestInterface::class);

        $this->repository = new AuthRepository($this->cache, $this->client);
    }

    public function testCanAuthenticatePassport_returnsPassportStamp()
    {
        $passportToken = '123';
        $jsonEncodedPassport = json_encode($this->passportObject);
        $this->response
            ->expects($this->once())
            ->method('getBody')
            ->willReturn($this->responseBody);

        $this->responseBody
            ->expects($this->once())
            ->method('getContents')
            ->willReturn($jsonEncodedPassport);

        $this->client
            ->shouldReceive('get')
            ->andReturn($this->response);

        $actualResults = $this->repository->authenticatePassport('wee', $passportToken);

        $this->assertEquals($this->passport, $actualResults);
    }

    public function testCannotAuthenticatePassport_ClientException_returnsNull()
    {
        $passportToken = '123';

        $this->client
            ->shouldReceive('get')
            ->andThrow(new ClientException('Message', $this->request));

        $actualResults = $this->repository->authenticatePassport('woo', $passportToken);

        $this->assertNull($actualResults);
    }

    public function testCannotAuthenticatePassport_ServerException_returnsNull()
    {
        $passportToken = '123';

        $this->client
            ->shouldReceive('get')
            ->andThrow(new ServerException('Message', $this->request));

        $actualResults = $this->repository->authenticatePassport('waaah', $passportToken);

        $this->assertNull($actualResults);
    }

    public function testCannotAuthenticatePassport_RequestException_returnsNull()
    {
        $passportToken = '123';

        $this->client
            ->shouldReceive('get')
            ->andThrow(new RequestException('Message', $this->request));

        $actualResults = $this->repository->authenticatePassport('wrrr', $passportToken);

        $this->assertNull($actualResults);
    }

    public function testCannotAuthenticatePassport_Exception_returnsNull()
    {
        $passportToken = '123';

        $this->client
            ->shouldReceive('get')
            ->andThrow(new \Exception());

        $actualResults = $this->repository->authenticatePassport('leee', $passportToken);

        $this->assertNull($actualResults);
    }
}
