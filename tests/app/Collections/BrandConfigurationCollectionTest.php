<?php

namespace App\Tests;

use App\Collections\BrandConfigurationCollection;

class BrandConfigurationCollectionTest extends TestCase
{
    private $configurationObject;
    /**
     * @var BrandConfigurationCollection
     */
    private $configurationCollection;

    public function setUp() : void
    {
        $this->configurationObject = new \stdClass();
        $this->configurationObject->brandConfigurationValue = 0.3;
        $this->configurationObject->brandId = 14;
        $this->configurationObject->configurationId = 3;
        $this->configurationObject->configurationName = 'Advertising Revenue Share';
        $this->configurationObject->configurationSlug = 'advertisingRevenueShare';
        $this->configurationObject->dataType = 'float';
        $this->configurationObject->id = 3;

        $this->configurationObject1 = new \stdClass();
        $this->configurationObject1->brandConfigurationValue = 2;
        $this->configurationObject1->brandId = 14;
        $this->configurationObject1->configurationId = 2;
        $this->configurationObject1->configurationName = 'test integer';
        $this->configurationObject1->configurationSlug = 'testInteger';
        $this->configurationObject1->dataType = 'integer';
        $this->configurationObject1->id = 1;

        $this->configurationObject2 = new \stdClass();
        $this->configurationObject2->brandConfigurationValue = 'string';
        $this->configurationObject2->brandId = 14;
        $this->configurationObject2->configurationId = 1;
        $this->configurationObject2->configurationName = 'test string';
        $this->configurationObject2->configurationSlug = 'testString';
        $this->configurationObject2->dataType = 'string';
        $this->configurationObject2->id = 2;

        $this->configurationObject3 = new \stdClass();
        $this->configurationObject3->brandConfigurationValue = [1, 2, 3];
        $this->configurationObject3->brandId = 14;
        $this->configurationObject3->configurationId = 4;
        $this->configurationObject3->configurationName = 'test string';
        $this->configurationObject3->configurationSlug = 'testString';
        $this->configurationObject3->dataType = 'array';
        $this->configurationObject3->id = 4;

        $this->configurationObject4 = new \stdClass();
        $this->configurationObject4->brandConfigurationValue = 'hello';
        $this->configurationObject4->brandId = 14;
        $this->configurationObject4->configurationId = 5;
        $this->configurationObject4->configurationName = 'test string 1';
        $this->configurationObject4->configurationSlug = 'testString1';
        $this->configurationObject4->dataType = 'array';
        $this->configurationObject4->id = 5;

        $configurationsFromDatabase = collect([
            $this->configurationObject, 
            $this->configurationObject1, 
            $this->configurationObject2,
            $this->configurationObject3,
            $this->configurationObject4,
        ]);

        $this->configurationCollection = new BrandConfigurationCollection($configurationsFromDatabase);
    }

    public function testCanGetAdvertisingRevenueShare_returnsFloat()
    {
        $expectedResults = 0.3;
        $actualResults = $this->configurationCollection->getAdvertisingRevenueShare();
        $this->assertEquals($expectedResults, $actualResults);
    }

    public function testCanGetBrandContactAddress__city_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactAddress__city();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactAddress__postal_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactAddress__postal();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactAddress__state_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactAddress__state();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactAddress__street_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactAddress__street();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactEmail_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactEmail();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactName_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactName();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandContactPhone_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandContactPhone();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetBrandUrl_returnsString()
    {
        $actualResults = $this->configurationCollection->getBrandUrl();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetStripeAccount_returnsString()
    {
        $actualResults = $this->configurationCollection->getStripeAccount();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $actualResults = $this->configurationCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }
}
