<?php

namespace App\Tests\Http;

use App\DTOs\ChannelConfigurationDto;
use App\DTOs\ChannelDto;
use App\DTOs\ConfigurationDto;
use App\Http\Repositories\ChannelRepository;
use App\Collections\ChannelCollection;
use App\Collections\ChannelConfigurationCollection;
use App\Http\Response;
use App\Tests\TestCase;
use Carbon\CarbonImmutable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class ChannelRepositoryTest extends TestCase
{
    private $db;
    private $channelObject;
    private $channelDto;
    private $channelConfigObject;
    private $channelConfigDto;
    private $repository;

    public function setUp(): void
    {
        $this->channelObject = new \stdClass();
        $this->channelObject->brandId = 5;
        $this->channelObject->title = 'Test';
        $this->channelObject->created_at = CarbonImmutable::now()->toDateTimeString();;
        $this->channelObject->deleted_at = null;
        $this->channelObject->updated_at = CarbonImmutable::now()->toDateTimeString();;
        $this->channelObject->id = 2;
        $this->channelObject->channelSlug = 'Slug';
        $this->channelObject->channelDescription = 'description';
        $this->channelObject->channelImage = '';
        $this->channelObject->channelHorizontalLogo = '';
        $this->channelObject->channelSquareLogo = '';
        $this->channelObject->defaultEsp = 0;
        $this->channelDto = new ChannelDto($this->channelObject);

        $this->channelConfigObject = new \stdClass();
        $this->channelConfigObject->channelConfigurationValue = 1;
        $this->channelConfigObject->channelId = 2;
        $this->channelConfigObject->configurationId = 9;
        $this->channelConfigObject->created_at = '';
        $this->channelConfigObject->updated_at = '';
        $this->channelConfigObject->id = 11;
        $this->channelConfigDto = new ChannelConfigurationDto($this->channelConfigObject);

        $this->configObject = new \stdClass();
        $this->configObject->configurationName = 'Auto Update Channel Stats From Mailchimp';
        $this->configObject->configurationSlug = 'autoUpdateChannelStatsFromMailchimp';
        $this->configObject->dataType = 'boolean';
        $this->configObject->id = 11;
        $this->configDto = new ConfigurationDto($this->configObject);

        $this->repository = new ChannelRepository();
        $this->db = \Mockery::mock(DatabaseManager::class);
    }

    public function testCanCreateChannel_returnsDto()
    {
        $configDbResult = new Collection([
            $this->channelConfigObject
        ]);

        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5);

        $this->db
            ->shouldReceive('table->find')
            ->andReturn($this->channelObject);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andReturn($configDbResult);

        app()->instance('db', $this->db);


        $actualResults = $this->repository->createChannel($this->channelDto);

        $this->assertInstanceOf(ChannelDto::class, $actualResults);
    }

    public function testCannotCreateChannel_ChannelsThrowsException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createChannel($this->channelDto);

        $this->assertNull($actualResults);
    }

    public function testCannotCreateChannel_GetExceptionThrown_returnsNull()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5);

        $this->db
            ->shouldReceive('table->find')
            ->andReturn($this->channelObject);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createChannel($this->channelDto);

        $this->assertEmpty($actualResults);
    }

    public function testCanCreateChannelConfiguration_returnsInteger()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5);

        app()->instance('db', $this->db);


        $actualResults = $this->repository->createChannelConfiguration($this->channelConfigDto);

        $this->assertEquals(5, $actualResults);
    }

    public function testCannotCreateChannelConfiguration_returnsNull()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);


        $actualResults = $this->repository->createChannelConfiguration($this->channelConfigDto);

        $this->assertNull($actualResults);
    }

    public function testCanDeleteChannel_returnsTrue()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteChannel($this->channelDto);

        $this->assertTrue($actualResults);
    }

    public function testCannotDeleteChannel_returnsFalse()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteChannel($this->channelDto);

        $this->assertFalse($actualResults);
    }

    public function testCanGetChannelsByBrandId_returnsArrayOfChannels()
    {
        $dbResult = new Collection([
            $this->channelObject
        ]);

        $configResult = new Collection([
            $this->channelConfigObject
        ]);

        $this->db
            ->shouldReceive('table->where->whereNull->get')
            ->andReturn($dbResult);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andReturn($configResult);

        app()->instance('db', $this->db);


        $actualResults = $this->repository->getChannelsByBrandId(5);

        $this->assertIsArray($actualResults);
        $this->assertInstanceOf(ChannelDto::class, $actualResults[0]);
    }

    public function testCannotGetChannelsByBrandId_ExceptionThrown_returnsEmptyArray()
    {
        $this->db
            ->shouldReceive('table->where->get')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelsByBrandId(5);

        $this->assertEmpty($actualResults);
    }

    public function testCannotGetChannelsByBrandId_ChannelConfigExceptionThrown_returnsEmptyArray()
    {
        $dbResult = new Collection([
            $this->channelObject
        ]);

        $configResult = new Collection([
            $this->channelConfigObject
        ]);

        $this->db
            ->shouldReceive('table->where->get')
            ->andReturn($dbResult);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelsByBrandId(5);

        $this->assertEmpty($actualResults);
    }

    public function testCanUpdateChannel_returnsDto()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andReturn(true);

        $configDbResult = new Collection([
            $this->channelConfigObject
        ]);

        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5);

        $this->db
            ->shouldReceive('table->find')
            ->andReturn($this->channelObject);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andReturn($configDbResult);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateChannel($this->channelDto);

        $this->assertInstanceOf(ChannelDto::class, $actualResults);
    }

    public function testCannotUpdateChannel_Exception_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateChannel($this->channelDto);

        $this->assertNull($actualResults);
    }

    public function testCannotGetChannelRowFromDatabaseBySlug_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andReturn(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelBySlug('wee');

        $this->assertNull($actualResults);
    }

    public function testCannotGetChannelRowFromDatabaseBySlug_exceptionThrown_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelBySlug('wee');

        $this->assertNull($actualResults);
    }

    public function testCanGetChannelRowFromDatabaseBySlug_returnsDto()
    {
        $configDbResult = new Collection([
            $this->channelConfigObject
        ]);

        $this->db
            ->shouldReceive('table->where->first')
            ->andReturn($this->channelObject);

        $this->db
            ->shouldReceive('table->join->where->select->get')
            ->andReturn($configDbResult);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelBySlug('wee');

        $this->assertInstanceOf(ChannelDto::class, $actualResults);
    }

    public function testCannotGetChannelById_returnsNull()
    {
        $this->db
        ->shouldReceive('table->find')
        ->andReturn(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelById(1);

        $this->assertNull($actualResults);
    }

    public function testCanUpdateChannelConfiguration_returnsTrue()
    {
        $this->db
        ->shouldReceive('table->updateOrInsert')
        ->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateChannelConfiguration(1, 'hello', 2);

        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateChannelConfiguration_throwsException()
    {
        $this->db
        ->shouldReceive('table->updateOrInsert')
        ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateChannelConfiguration(1, 'hello', 2);

        $this->assertFalse($actualResults);
    }

    public function testCanGetChannels()
    {
        $channelResults = collect([$this->channelObject,]);

        $this->db->shouldReceive('table->get')
        ->andReturn($channelResults);

        $this->db->shouldReceive('table->join->where->select->get')
        ->andReturn(collect([]));

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannels();
        $this->assertInstanceOf(ChannelCollection::class, $actualResults);
    }

    public function testCannotGetChannels()
    {
        $this->db->shouldReceive('table->get')
        ->andThrow(new \Exception());

        $this->db->shouldReceive('rollBack');

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannels();
        $this->assertEmpty($actualResults);
    }

    public function testCannotGetChannelsThatAutoSyncListStats()
    {
        $channelResults = collect([$this->channelObject,]);

        $this->db->shouldReceive('table->join->join->where->where->select->get')
            ->andReturn($channelResults);

        $this->db->shouldReceive('table->join->where->select->get')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelsThatAutoSyncListStats();
        $this->assertInstanceOf(ChannelCollection::class, $actualResults);
    }

    public function testCanGetChannelsThatAutoSyncListStats()
    {
        $channelResults = collect([$this->channelObject,]);

        $this->db->shouldReceive('table->join->join->where->where->select->get')
            ->andReturn($channelResults);

        $this->db->shouldReceive('table->join->where->select->get')
            ->andReturn(collect([]));

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelsThatAutoSyncListStats();
        $this->assertInstanceOf(ChannelCollection::class, $actualResults);
    }

    public function testCanGetChannelByBrandApiKey_noResults()
    {
        $result = null;

        $this->db->shouldReceive('table->join->join->where->select->get->first')
            ->andReturn($result);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelByBrandApiKey('123');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(404, $actualResults->getStatus());
    }

    public function testCanGetChannelByBrandApiKey_keyInvalid()
    {
        $result = new \stdClass();
        $result->isActive = 0;

        $this->db->shouldReceive('table->join->join->where->select->get->first')
            ->andReturn($result);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelByBrandApiKey('123');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(401, $actualResults->getStatus());
    }

    public function testCanGetChannelByBrandApiKey_serverError()
    {
        $result = new \stdClass();
        $result->isActive = 0;

        $this->db->shouldReceive('table->join->join->where->select->get->first')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelByBrandApiKey('123');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(500, $actualResults->getStatus());
    }

    public function testCanGetChannelByBrandApiKey_returnsChannel()
    {
        $result = $this->channelObject;
        $result->isActive = 1;

        $this->db->shouldReceive('table->join->join->where->select->get->first')
            ->andReturn($result);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getChannelByBrandApiKey('123');

        $this->assertInstanceOf(Response::class, $actualResults);
        $this->assertEquals(200, $actualResults->getStatus());
        $this->assertInstanceOf(ChannelDto::class, $actualResults->getData());
    }
}
