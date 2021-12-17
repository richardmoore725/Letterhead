<?php

use App\Tests\TestCase;
use App\Http\Services\AuthService;
use App\Http\Repositories\AuthRepositoryInterface;
use App\Models\PassportStamp;
use App\DTOs\PassportStampDto;

class AuthServiceTest extends TestCase
{
    private $passportStampDto;
    private $passportStamp;
    private $repository;
    private $service;

    public function setUp(): void
    {
        $passportObject = new \stdClass();
        $passportObject->acc = 'Access Token';
        $passportObject->exp = '24';
        $passportObject->originalToken = 'Original Token';
        $passportObject->ref = 'Refresh Token';
        $passportObject->user = new \stdClass();
        $passportObject->user->email = 'test@test.com';
        $passportObject->user->name = 'Marshall';
        $passportObject->user->id = 1;

        $this->passportDto = new PassportStampDto($passportObject, 'Original Token');
        $this->passportStamp = new PassportStamp($this->passportDto);

        $this->repository = $this->createMock(AuthRepositoryInterface::class);
        $this->service = new AuthService($this->repository);
    }

    public function testCanAuthenticatePassport_returnsPassportModel()
    {
        $this->repository
            ->expects($this->once())
            ->method('authenticatePassport')
            ->Willreturn($this->passportDto);

        $actualResults = $this->service->authenticatePassport('origin', 'token');

        $this->assertEquals($this->passportStamp ,$actualResults);
    }

    public function testCannotAuthenticatePassport_returnsNull()
    {
        $this->repository
            ->expects($this->once())
            ->method('authenticatePassport')
            ->Willreturn(null);

        $actualResults = $this->service->authenticatePassport('origin', 'token');

        $this->assertNull($actualResults);
    }
}
