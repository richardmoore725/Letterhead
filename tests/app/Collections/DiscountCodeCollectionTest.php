<?php

namespace App\Tests;

use App\Collections\DiscountCodeCollection;

class DiscountCodeCollectionTest extends TestCase
{
    private $discountCodeCollection;

    public function setUp() : void
    {
        $this->discountCodeCollection = new DiscountCodeCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $discountCodeObject = new \stdClass();
        $discountCodeObject->channelId = 1;
        $discountCodeObject->createdAt = '2020-12-24';
        $discountCodeObject->deletedAt = null;
        $discountCodeObject->discountCode = 'TESTCODE';
        $discountCodeObject->discountValue = '42';
        $discountCodeObject->displayName = 'Test Code';
        $discountCodeObject->isActive = true;
        $discountCodeObject->id = 1;
        $discountCodeObject->updatedAt = '2020-12-25';

        $arrayOfDiscountCodes = [ $discountCodeObject ];

        $this->discountCodeCollection = new DiscountCodeCollection($arrayOfDiscountCodes);

        $actualResults = $this->discountCodeCollection->getPublicArrays();
        $this->assertIsArray($actualResults);
    }
}