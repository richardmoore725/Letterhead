<?php

namespace App\Tests\Http;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Http\Repositories\MessageRepositoryInterface;
use App\Http\Response;
use App\Http\Services\MessageService;
use App\Tests\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class MessageServiceTest extends TestCase
{
    /**
     * @var MessageService
     */
    private $service;

    /**
     * @var MockObject
     */
    private $repository;

    public function setUp() : void
    {
        $this->repository = $this->createMock(MessageRepositoryInterface::class);
        $this->service = new MessageService($this->repository);
    }

    public function testCanCreateMessage_returnsResponse()
    {
        $response = $this->createMock(Response::class);

        $this->repository
            ->expects($this->once())
            ->method('createMessage')
            ->with($this->isInstanceOf(MessageDto::class))
            ->willReturn($response);

        $actualResults = $this->service->createMessage('I am a comment', 5, 'promotion', 16);
        $this->assertInstanceOf(Response::class, $actualResults);
    }

    public function testCanGetMessagesByResource_returnsResponse()
    {
        $collection = new MessageCollection();

        $this->repository
            ->expects($this->once())
            ->method('getMessagesByResource')
            ->with(1, 'test')
            ->willReturn($collection);

        $actualResults = $this->service->getMessagesByResource(1, 'test');
        $this->assertInstanceOf(MessageCollection::class, $actualResults);
    }
}
