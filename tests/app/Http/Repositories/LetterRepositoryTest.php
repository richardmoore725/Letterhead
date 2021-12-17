<?php

namespace App\Tests\Http;

use App\Collections\LetterCollection;
use App\DTOs\LetterDto;
use App\DTOs\LetterPartDto;
use App\Http\Repositories\LetterRepository;
use App\Http\Repositories\MjmlTemplateRepositoryInterface;
use App\Models\LetterPart;
use App\Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Cache\Factory as Cache;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class LetterRepositoryTest extends TestCase
{
    private $cache;
    /**
     * @var DatabaseManager
     */
    private $db;
    private $letterObject;
    private $letterDto;
    private $lettersUsersObject;
    private $letterPartObject;
    private $letterPartDto;
    private $letterPart;
    private $repository;

  public function setUp() : void
  {
      $this->cache = \Mockery::mock(Cache::class);
      $this->letterObject = new \stdClass();
      $this->letterObject->campaignId = 'campaignId1';
      $this->letterObject->channelId = 5;
      $this->letterObject->copyRendered = '';
      $this->letterObject->created_at = '2020-08-02';
      $this->letterObject->deleted_at = null;
      $this->letterObject->emailServiceProvider = 1;
      $this->letterObject->emailServiceProviderListId = '22222';
      $this->letterObject->emailTemplate = '';
      $this->letterObject->id = 1;
      $this->letterObject->publicationDate = '2020-09-03';
      $this->letterObject->publicationDateOffset = '-05:00';
      $this->letterObject->includePromotions = true;
      $this->letterObject->mjmlTemplate = '<mj-section><mj-column><mj-text>Hey</mj-text></mj-column></mj-section>';
      $this->letterObject->segmentId = 112233;
      $this->letterObject->status = 0;
      $this->letterObject->slug = 'i-am-a-slug';
      $this->letterObject->subtitle = 'Why hello';
      $this->letterObject->specialBanner = '';
      $this->letterObject->title = 'Hooray';
      $this->letterObject->updated_at = '2020-08-02';
      $this->letterObject->uniqueId = '1j1j91919k1';
      $this->letterDto = new LetterDto($this->letterObject);
      $this->lettersUsersObject = new \stdClass();
      $this->lettersUsersObject->id = 2;
      $this->lettersUsersObject->letterId = 1;
      $this->lettersUsersObject->userId = 1;
      $this->letterPartObject = new \stdClass();
      $this->letterPartObject->id = 3;
      $this->letterPartObject->copy = 'Copy';
      $this->letterPartObject->created_at = '2020-08-02';
      $this->letterPartObject->deleted_at = null;
      $this->letterPartObject->heading ='Heading';
      $this->letterPartObject->letterId = 1;
      $this->letterPartObject->updated_at = '2020-08-02';
      $this->letterPartDto = new LetterPartDto($this->letterPartObject);
      $this->letterPart = new LetterPart($this->letterPartDto);
      $this->repository = new LetterRepository($this->cache);

      $this->db = \Mockery::mock(DatabaseManager::class);
  }

  public function testCanCreateLetter_returnsLetterDto()
  {
      $arrayOfAuthorIds = [1];
      $arrayOfEmptyLetterParts = [
          $this->letterPart,
      ];

      $this->cache->shouldReceive('get')->andReturn(null);

      $this->db->shouldReceive('beginTransaction');
      $this->db->shouldReceive('table->insertGetId')->andReturn(1);
      $this->db->shouldReceive('table->insert')->twice()->andReturn(true);
      $this->db->shouldReceive('commit');

      $this->cache->shouldReceive('forget');

      $this->db->shouldReceive('table->where->first')->andReturn($this->letterObject);
      $this->db->shouldReceive('table->where->get')->andReturn([$this->lettersUsersObject]);
      $this->db->shouldReceive('table->where->get')->andReturn([$this->letterPartObject]);

      $knownDate = CarbonImmutable::create(2020, 8, 21);
      CarbonImmutable::setTestNow($knownDate);
      $this->cache->shouldReceive('put')->andReturn(true);

      app()->instance('db', $this->db);

      $actualResults = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $this->letterDto, '2020-08-02');

      $this->assertInstanceOf(LetterDto::class, $actualResults);
  }

    public function testCannotCreateLetter_ExceptionThrown_returnsNull()
    {
        $arrayOfAuthorIds = [1];
        $arrayOfEmptyLetterParts = [
            $this->letterPart,
        ];

        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->insertGetId')->andReturn(1);
        $this->db->shouldReceive('table->insert')->once()->andThrows(new \Exception());
        $this->db->shouldReceive('rollback');

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $this->letterDto, '2020-08-02');

        $this->assertNull($actualResults);
    }

    public function testCanCreateLetterButCantGet_returnsNull()
    {
        $arrayOfAuthorIds = [1];
        $arrayOfEmptyLetterParts = [
            $this->letterPart,
        ];

        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->insertGetId')->andReturn(1);
        $this->db->shouldReceive('table->insert')->twice()->andReturn(true);
        $this->db->shouldReceive('commit');

        $this->cache->shouldReceive('forget');

        $this->db->shouldReceive('table->where->first')->andReturn(null);

        app()->instance('db', $this->db);


        $actualResults = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $this->letterDto, '2020-08-02');

        $this->assertNull($actualResults);
    }

    public function testCanCreateLetterButCantGet_exceptionThrown_returnsNull()
    {
        $arrayOfAuthorIds = [1];
        $arrayOfEmptyLetterParts = [
            $this->letterPart,
        ];

        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->insertGetId')->andReturn(1);
        $this->db->shouldReceive('table->insert')->twice()->andReturn(true);
        $this->db->shouldReceive('commit');

        $this->cache->shouldReceive('forget');

        $this->db->shouldReceive('table->where->first')->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $this->letterDto, '2020-08-02');

        $this->assertNull($actualResults);
    }

    public function testCanCreateLetterButCantGetAuthors_ExceptionThrown_returnsNull()
    {
        $arrayOfAuthorIds = [1];
        $arrayOfEmptyLetterParts = [
            $this->letterPart,
        ];

        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->insertGetId')->andReturn(1);
        $this->db->shouldReceive('table->insert')->twice()->andReturn(true);
        $this->db->shouldReceive('commit');

        $this->cache->shouldReceive('forget');

        $this->db->shouldReceive('table->where->first')->andReturn($this->letterObject);

        $knownDate = CarbonImmutable::create(2020, 8, 21);
        CarbonImmutable::setTestNow($knownDate);
        $this->cache->shouldReceive('put')->andReturn(true);

        $this->db->shouldReceive('table->where->get')->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createLetter($arrayOfAuthorIds, $arrayOfEmptyLetterParts, $this->letterDto, '2020-08-02');

        $this->assertInstanceOf(LetterDto::class, $actualResults);
    }

    /**
    public function testCanGetLetterFromCacheById_returnsDto()
    {
        $this->cache->shouldReceive('get')->andReturn($this->letterDto);
        $actualResults = $this->repository->getLetterById(1);

        $this->assertInstanceOf(LetterDto::class, $actualResults);
    }
     * */

    public function testCanGetLettersByChannelId_returnsCollection()
    {
        $letterCollection = $this->createMock(LetterCollection::class);
        $this->cache->shouldReceive('get')->andReturn($letterCollection);

        $actualResults = $this->repository->getLettersByChannelId(5);

        $this->assertInstanceOf(LetterCollection::class, $actualResults);
    }

    public function testCanGetLettersByChannelId_returnsCollectionFromDatabase()
    {
        $collection = $this->createMock(Collection::class);
        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('table->where->whereNull->get')->andReturn($collection);

        $collection->expects($this->once())->method('all')->willReturn(['wee']);

        $this->cache->shouldReceive('put')->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getLettersByChannelId(5);

        $this->assertInstanceOf(LetterCollection::class, $actualResults);
    }


    public function testCannotGetLettersByChannelId_throwsException_returnsCollectionFromDatabase()
    {
        $collection = $this->createMock(Collection::class);
        $this->cache->shouldReceive('get')->andReturn(null);

        $this->db->shouldReceive('table->where->whereNull->get')->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getLettersByChannelId(5);

        $this->assertInstanceOf(LetterCollection::class, $actualResults);
    }
}
