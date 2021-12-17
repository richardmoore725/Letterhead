<?php

namespace App\Tests;


use App\DTOs\DiscountCodeDto;
use App\Models\DiscountCode;
use Illuminate\Support\Collection;

class DiscountCodeTest extends TestCase
{
    private $discountCode;

    public function setUp() : void
    {
        $object = new \stdClass;

        $object->channelId = 1;
        $object->deletedAt = '2020-12-24';
        $object->discountValue = 20;
        $object->id = 1;

        $dto = new DiscountCodeDto();

        $this->discountCode = new DiscountCode($dto);
    }

    public function testCanGetChannelId__returnsInt()
    {
        $actualResults = $this->discountCode->getChannelId();
        $this->assertIsInt($actualResults);
    }

    public function testCanGetDeletedAt()
    {
        $actualResults = $this->discountCode->getDeletedAt();
        $this->assertIsString($actualResults);
    }

    public function testCanGetDiscountValue__returnsInt()
    {
        $actualResults = $this->discountCode->getDiscountValue();
        $this->assertIsInt($actualResults);
    }

    public function testCanGetId()
    {
        $actualResults = $this->discountCode->getId();
        $this->assertIsInt($actualResults);
    }

    public function testCanConvertToArray_returnArray()
    {
        $actualResults = $this->discountCode->convertToArray();
        $this->assertIsArray($actualResults);
    }
}