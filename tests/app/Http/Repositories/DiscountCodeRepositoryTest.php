<?php

namespace App\Tests;

use App\Collections\DiscountCodeCollection;
use App\Http\Repositories\DiscountCodeRepository;
use App\DTOs\DiscountCodeDto;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\Collection;

class DiscountCodeRepositoryTest extends TestCase
{
    private $db;
    private $discountCodeDto;
    private $discountCodeObject;
    private $repository;


    public function setUp() : void
    {
        $this->discountCodeObject = new \stdClass();
        $this->discountCodeObject->channelId = 1;
        $this->discountCodeObject->createdAt = '2020-12-24';
        $this->discountCodeObject->deletedAt = null;
        $this->discountCodeObject->discountCode = 'TESTCODE';
        $this->discountCodeObject->discountValue = '42';
        $this->discountCodeObject->displayName = 'Test Code';
        $this->discountCodeObject->id = true;
        $this->discountCodeObject->isActive = 1;
        $this->discountCodeObject->updatedAt = '2020-12-25';
        $this->discountCodeDto = new DiscountCodeDto($this->discountCodeObject);

        $this->repository = new DiscountCodeRepository($this->discountCodeDto);
        $this->db = \Mockery::mock(DatabaseManager::class);
    }

    public function testCannotCreateDiscountCode_DiscountCodeThrowsException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createDiscountCode($this->discountCodeDto);

        $this->assertNull($actualResults);
    }

    public function testCanCreateDiscountCode_returnsDto()
    {
        $this->db
            ->shouldReceive('table->insertGetId')
            ->andReturns(1);

        $this->db
            ->shouldReceive('table->where->first')
            ->andReturns($this->discountCodeObject);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->createDiscountCode($this->discountCodeDto);

        $this->assertInstanceOf(DiscountCodeDto::class, $actualResults);
    }

    public function testCannotGetDiscountCodeById_databaseReturnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andReturns(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeById(1);

        $this->assertNull($actualResults);
    }

    public function testCannotGetDiscountCodeById_ThrowsException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeById(1);

        $this->assertNull($actualResults);
    }

    public function testCanGetDiscountCodeById_returnsDto()
    {
        $this->db
            ->shouldReceive('table->where->first')
            ->andReturns($this->discountCodeObject);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeById(1);

        $this->assertInstanceOf(DiscountCodeDto::class, $actualResults);
    }

    public function testCannotGetDiscountCodeByCode_databaseReturnsNull()
    {
        $this->db
            ->shouldReceive('table->where->whereNull->first')
            ->andReturns(null);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeByCode('testDiscountCode');

        $this->assertNull($actualResults);
    }

    public function testCannotGetDiscountCodeByCode_ThrowsException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->whereNull->first')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeByCode('testDiscountCode');

        $this->assertNull($actualResults);
    }

    public function testCanGetDiscountCodeByCode_returnsDto()
    {
        $this->db
            ->shouldReceive('table->where->whereNull->first')
            ->andReturns($this->discountCodeObject);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodeByCode('testDiscountCode');

        $this->assertInstanceOf(DiscountCodeDto::class, $actualResults);
    }

    public function testCannotGetDiscountCodesByChannelId_ThrowsException_returnsEmptyCollection()
    {
        $this->db
            ->shouldReceive('table->where->whereNull->get')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodesByChannelId(1);

        $this->assertInstanceOf(DiscountCodeCollection::class, $actualResults);
        $this->assertEmpty($actualResults);
    }

    public function testCanGetDiscountCodesByChannelId_returnsCollection()
    {
        $discountCodeDbResult = collect([
            $this->discountCodeObject,
        ]);

        $this->db
            ->shouldReceive('table->where->whereNull->get')
            ->andReturns($discountCodeDbResult);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->getDiscountCodesByChannelId(1);

        $this->assertInstanceOf(DiscountCodeCollection::class, $actualResults);
    }

    public function testCannotDeleteDiscountCodes_ThrowsException_returnsFalse()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteDiscountCode(1);

        $this->assertFalse($actualResults);
    }

    public function testCanDeleteDiscountCodes_returnsTrue()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andReturns(true);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->deleteDiscountCode(1);

        $this->assertTrue($actualResults);
    }

    public function testCannotUpdateDiscountCode_ThrowException_returnsNull()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andThrows(new \Exception());

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateDiscountCode($this->discountCodeDto);

        $this->assertNull($actualResults);
    }

    public function testCanUpdateDiscountCode_returnsDto()
    {
        $this->db
            ->shouldReceive('table->where->update')
            ->andReturns(true);

        $this->db
            ->shouldReceive('table->where->first')
            ->andReturns($this->discountCodeObject);

        app()->instance('db', $this->db);

        $actualResults = $this->repository->updateDiscountCode($this->discountCodeDto);

        $this->assertInstanceOf(DiscountCodeDto::class, $actualResults);
    }
}