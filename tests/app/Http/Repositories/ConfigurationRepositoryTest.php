<?php

namespace App\Tests\Http;

use App\DTOs\ConfigurationDto;
use App\Http\Repositories\ConfigurationRepository;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;

class ConfigurationRepositoryTest extends TestCase
{
    private $db;
    private $configObject;
    private $configDto;
    private $repository;

    public function setUp(): void
    {
        $this->configObject = new \stdClass();
        $this->configObject->id = 4;
        $this->configObject->configurationName = 'Wee';
        $this->configObject->configurationSlug = 'whereby-us';

        $this->configDto = new ConfigurationDto($this->configObject);

        $this->repository = new ConfigurationRepository();
        $this->db = \Mockery::mock(DatabaseManager::class);
    }

    public function testCanGetConfigurationBySlug_returnsDto()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andReturn($this->configObject);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getConfigurationBySlug('whereby-us');

        $this->assertEquals($this->configDto, $actualResults);
    }

    public function testCannotGetConfigurationBySlug_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andReturn(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getConfigurationBySlug('whereby-us');

        $this->assertNull($actualResults);
    }

    public function testCannotGetConfigurationBySlug_Exception_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getConfigurationBySlug('whereby-us');

        $this->assertNull($actualResults);
    }


}
