<?php

namespace App\Tests\Http;

use App\Collections\BrandConfigurationCollection;
use App\Collections\BrandCollection;
use App\DTOs\BrandDto;
use App\Http\Repositories\BrandRepository;
use App\Http\Repositories\ChannelRepositoryInterface;
use App\Tests\TestCase;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Collection;

class BrandRepositoryTest extends TestCase
{
    private $db;
    private $brandObject;
    private $dto;
    private $repository;
    private $channelRepository;

    public function setUp(): void
    {
        $this->brandObject = new \stdClass();
        $brandConfigurationResults = collect([]);
        $this->brandObject->brandConfigurations = new BrandConfigurationCollection($brandConfigurationResults);
        $this->brandObject->brandName = 'Black Bitter Tea';
        $this->brandObject->brandSlug = 'black-bitter-tea';
        $this->brandObject->createdAt = '2020-01-28';
        $this->brandObject->id = 5;
        $this->brandObject->updatedAt = '2020-01-28';

        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->dto = new BrandDto($this->brandObject);
        $this->repository = new BrandRepository($this->channelRepository);
        $this->db = \Mockery::mock(DatabaseManager::class);
    }

    public function testCanCreateBrandFeaturesAndConfigurations()
    {
        $brandConfigurationResults = collect([]);

        $this->db->shouldReceive('beginTransaction');

        /**
         * The `insertBrand` method.
         */
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5); //brandId

        $this->db->shouldReceive('table->insertGetId')
            ->andReturn(3); //brandKeyId

        $this->db->shouldReceive('table->where->value')
            ->andReturn('imanapikey123456789');

        $this->db->shouldReceive('table->find')
            ->andReturn($this->brandObject);

        $this->db->shouldReceive('table->join->where->select->get')
            ->andReturn($brandConfigurationResults);

        $this->db->shouldReceive('commit');

        app()->instance('db', $this->db);

        $actualResult = $this->repository->createBrandFeaturesAndConfigurations($this->dto);

        $this->assertInstanceOf(BrandDto::class, $actualResult);
    }

    public function testCannotCreateBrandFeaturesAndConfigurations_KeyException_returnsNull()
    {
        $this->db->shouldReceive('beginTransaction');

        /**
         * The `insertBrand` method.
         */
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturn(5); //brandId

        $this->db->shouldReceive('table->insertGetId')
            ->andReturn(3); //brandKeyId

        $this->db->shouldReceive('table->where->value')
            ->andThrow(new \Exception());

        $this->db->shouldReceive('rollBack');

        app()->instance('db', $this->db);

        $actualResult = $this->repository->createBrandFeaturesAndConfigurations($this->dto);

        $this->assertNull($actualResult);
    }

    public function testCannotCreateBrand_throwsException_returnsNull()
    {
        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->insertGetId')
            ->andThrow(new \Exception());
        $this->db->shouldReceive('rollBack');

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createBrandFeaturesAndConfigurations($this->dto);

        $this->assertEmpty($actualResults);
    }

    public function testCanDeleteBrand_returnsTrue()
    {
        $this->db
            ->shouldReceive('table->where->delete')
            ->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteBrand($this->dto);

        $this->assertTrue($actualResults);
    }

    public function testCannotDeleteBrand_returnsFalse()
    {
        $this->db
            ->shouldReceive('table->where->delete')
            ->once()
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteBrand($this->dto);

        $this->assertFalse($actualResults);
    }

    public function testCannotFindBrandById_returnsNull()
    {
        $this->db
            ->shouldReceive('table->find')
            ->once()
            ->andReturn(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandById(5);

        $this->assertEmpty($actualResults);
    }

    public function testCannotGetBrandByIdThrowsException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->find')
            ->once()
            ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandById(0);

        $this->assertNull($actualResults);
    }

    public function testCannotGetBrandBySlug_returnsNull()
    {
        $this->db
        ->shouldReceive('table->where->first')
        ->once()
        ->andReturn(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandBySlug('hello');

        $this->assertEmpty($actualResults); 
    }

    public function testCannotGetBrandBySlugThrowsException_returnsNull()
    {
        $this->db
        ->shouldReceive('table->where->first')
        ->once()
        ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandBySlug('hello');

        $this->assertEmpty($actualResults); 
    }

    public function testCanUpdateBrand_returnsDto()
    {
        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->where->update')
            ->andReturn(true);

        $this->db->shouldReceive('table->find')
            ->andReturn($this->brandObject);

        $this->db->shouldReceive('table->join->where->select->get')
            ->andReturn(collect([]));

        $this->db->shouldReceive('commit');

        app()->instance('db', $this->db);
        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrand($this->dto);

        $this->assertInstanceOf(BrandDto::class, $actualResults);
    }

    public function testCannotUpdateBrandThrowsException_returnsNull()
    {
        $this->db->shouldReceive('beginTransaction');
        $this->db->shouldReceive('table->where')
            ->andThrow(new \Exception());
        $this->db->shouldReceive('rollBack');

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrand($this->dto);

        $this->assertEmpty($actualResults);
    }

    public function testCanUpdateBrandConfigurationByBrandCofigurationValue_returnsTrue()
    {
        $brandConfigurationValue = 'stripe_account';

        $this->db->shouldReceive('table->where->update')
        ->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrandConfigurationByBrandCofigurationValue($brandConfigurationValue, '');
        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateBrandConfigurationByBrandCofigurationValue_returnsFalse()
    {
        $brandConfigurationValue = 'stripe_account';

        $this->db->shouldReceive('table->where->update')
        ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrandConfigurationByBrandCofigurationValue($brandConfigurationValue, '');
        $this->assertFalse($actualResults);
    }

    public function testCanUpdateBrandConfiguration_returnsTrue()
    {
        $brandConfigurationValue = 'stripe_account';
        $brandId = 5;
        $configurationId = 3;

        $this->db->shouldReceive('table->updateOrInsert')
        ->andReturn(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrandConfiguration(3, $brandConfigurationValue, 5);
        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateBrandConfiguration_returnsFalse()
    {
        $brandConfigurationValue = 'stripe_account';
        $brandId = 5;
        $configurationId = 3;

        $this->db->shouldReceive('table->updateOrInsert')
        ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateBrandConfiguration(3, $brandConfigurationValue, 5);
        $this->assertFalse($actualResults);
    }

    public function testCanGetBrands()
    {
        $brandResults = collect([$this->brandObject,]);

        $this->db->shouldReceive('table->get')
        ->andReturn($brandResults);

        $this->db->shouldReceive('table->join->where->select->get')
        ->andReturn(collect([]));

        $this->channelRepository
        ->expects($this->once())
        ->method('getChannelsByBrandId')
        ->willReturn(['channelsArray']);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrands();
        $this->assertInstanceOf(BrandCollection::class, $actualResults);
    }

    public function testCanGetBrandsByIds()
    {
        $brandResults = collect([$this->brandObject,]);

        $this->db->shouldReceive('table->whereIn->get')
        ->andReturn($brandResults);

        $this->db->shouldReceive('table->join->where->select->get')
        ->andReturn(collect([]));

        $this->channelRepository
        ->expects($this->once())
        ->method('getChannelsByBrandId')
        ->willReturn(['channelsArray']);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandsByIds(['someBrandIds']);
        $this->assertInstanceOf(BrandCollection::class, $actualResults);
    }

    public function testCannotGetBrandsByIds_throwsException()
    {
        $brandResults = collect([$this->brandObject,]);

        $this->db->shouldReceive('table->whereIn->get')
        ->andReturn($brandResults);

        $this->db->shouldReceive('table->join->where->select->get')
        ->andThrow(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getBrandsByIds(['someBrandIds']);
        $this->assertEmpty($actualResults);
    }
}
