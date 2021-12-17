<?php

namespace App\Tests\Http;

use App\Collections\MessageCollection;
use App\DTOs\MessageDto;
use App\Http\Repositories\MessageRepository;
use App\Http\Response;
use App\Tests\TestCase;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;
use Mockery\Mock;
use Mockery\MockInterface;

class MessageRepositoryTest extends TestCase
{
    private $cache;

    /**
     * @var MockInterface
     */
    private $db;

    /**
     * @var MessageRepository
     */
    private $repository;

    public function setUp() : void
    {
        $this->cache = \Mockery::mock(Cache::class);
        $this->db = \Mockery::mock(DatabaseManager::class);
        $this->repository = new MessageRepository($this->cache);
    }

    public function testCanCreateMessage_returnsSuccessfulResponse()
    {
        $messageObject = new \stdClass();
        $messageObject->created_at = '2020-12-24';
        $messageObject->deleted_at = null;
        $messageObject->id = 1;
        $messageObject->message = 'A message is here for the reading';
        $messageObject->resourceId = 100;
        $messageObject->resourceName = 'promotion';
        $messageObject->uniqueId = 'aj939323';
        $messageObject->userId = 16;

        $dto = $this->createMock(MessageDto::class);
        $dto->expects($this->once())
            ->method('mapDtoToDatabaseColumnsArray')
            ->willReturn([
                'created_at' => $messageObject->created_at,
                'deleted_at' => $messageObject->deleted_at,
                'id' => (int) $messageObject->id,
                'message' => $messageObject->message,
                'resourceId' => (int) $messageObject->resourceId,
                'resourceName' => $messageObject->resourceName,
                'uniqueId' => $messageObject->uniqueId,
                'userId' => (int) $messageObject->userId
            ]);

        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(1);

        $this->cache->shouldReceive('forget');

        app()->instance('db', $this->db);

        $this->db
            ->shouldReceive('table->where->first')
            ->andReturn($messageObject);

        $actualResults = $this->repository->createMessage($dto);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(201, $actualResults->getStatus());
        $this->assertInstanceOf(MessageDto::class, $actualResults->getData());
    }

    public function testCanCreateMessage_returnsErrorResponse()
    {
        $dto = $this->createMock(MessageDto::class);
        $dto->expects($this->once())
            ->method('mapDtoToDatabaseColumnsArray')
            ->willReturn([]);

        $this->db
            ->shouldReceive('table->insertGetId')
            ->andThrows(new \Exception());

        $this->cache->shouldNotReceive('forget');

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createMessage($dto);

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCanGetMessageByResource_returnsCollection()
    {
        $messageCollection = $this->createMock(MessageCollection::class);
        $this->cache->shouldReceive('get')->andReturn($messageCollection);

        $actualResults = $this->repository->getMessagesByResource(1, 'test');

        $this->assertInstanceOf(MessageCollection::class, $actualResults);
    }

    public function testCanGetMessageByResource_returnsCollectionFromDatabase()
    {
        $collection = $this->createMock(Collection::class);
        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('table->where->where->whereNull->get')->andReturn($collection);

        $collection->expects($this->once())->method('all')->willReturn(['wee']);

        $this->cache->shouldReceive('put')->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getMessagesByResource(1, 'test');

        $this->assertInstanceOf(MessageCollection::class, $actualResults);
    }

    public function testCannotGetMessageByResource_throwsException_returnsCollectionFromDatabase()
    {
        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('table->where->where->whereNull->get')->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getMessagesByResource(1, 'test');

        $this->assertInstanceOf(MessageCollection::class, $actualResults);
    }
}
