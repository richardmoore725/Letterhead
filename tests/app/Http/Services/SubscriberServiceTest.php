<?php

namespace App\Tests\Http;

use App\Models\ChannelSubscriber;
use App\Collections\ChannelSubscriberCollection;
use App\Http\Repositories\LetterheadEspRepositoryInterface;
use App\Http\Services\SubscriberService;
use App\Http\Response;
use App\Tests\TestCase;

class SubscriberServiceTest extends TestCase
{
  private $repository;

  public function setUp(): void
  {
    $this->repository = $this->createMock(LetterheadEspRepositoryInterface::class);
    $this->service = new SubscriberService($this->repository);
  }

  public function testCanGetSubscribersByChannel_returnsNull()
  {
    $subscriberCollection = $this->createMock(ChannelSubscriberCollection::class);
    $repositoryResponse = new Response('', 200, $subscriberCollection);

    $this->repository
        ->expects($this->once())
        ->method('getSubscribersByChannel')
        ->with(1)
        ->willReturn($repositoryResponse);

    $actualResults = $this->service->getSubscribersByChannel(1);
    $this->assertInstanceOf(Response::class, $actualResults);
  }
}