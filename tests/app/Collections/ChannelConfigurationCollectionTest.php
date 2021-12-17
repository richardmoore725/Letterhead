<?php

namespace App\Tests;

use App\Collections\ChannelConfigurationCollection;

class ChannelConfigurationCollectionTest extends TestCase
{
    private $configurationObject;
    /**
     * @var ChannelConfigurationCollection
     */
    private $configurationCollection;

    public function setUp() : void
    {
        $this->configurationObject = new \stdClass();
        $this->configurationObject->channelConfigurationValue = 90;
        $this->configurationObject->channelId = 5;
        $this->configurationObject->configurationName = 'Ad Scheduling Buffer';
        $this->configurationObject->configurationSlug = 'adSchedulingBuffer';
        $this->configurationObject->createdAt = '2020-02-04';
        $this->configurationObject->dataType = 'integer';
        $this->configurationObject->id = 1;
        $this->configurationObject->updatedAt = '2020-03-01';

        $this->configurationObject1 = new \stdClass();
        $this->configurationObject1->channelConfigurationValue = 0.9;
        $this->configurationObject1->channelId = 5;
        $this->configurationObject1->configurationName = 'test float';
        $this->configurationObject1->configurationSlug = 'testFloat';
        $this->configurationObject1->createdAt = '2020-02-04';
        $this->configurationObject1->dataType = 'float';
        $this->configurationObject1->id = 2;
        $this->configurationObject1->updatedAt = '2020-03-01';

        $this->configurationObject2 = new \stdClass();
        $this->configurationObject2->channelConfigurationValue = [1, 2, 3];
        $this->configurationObject2->channelId = 5;
        $this->configurationObject2->configurationName = 'test array';
        $this->configurationObject2->configurationSlug = 'testArray';
        $this->configurationObject2->createdAt = '2020-02-04';
        $this->configurationObject2->dataType = 'array';
        $this->configurationObject2->id = 3;
        $this->configurationObject2->updatedAt = '2020-03-01';

        $this->configurationObject3 = new \stdClass();
        $this->configurationObject3->channelConfigurationValue = 'hello';
        $this->configurationObject3->channelId = 5;
        $this->configurationObject3->configurationName = 'test array1';
        $this->configurationObject3->configurationSlug = 'testArray1';
        $this->configurationObject3->createdAt = '2020-02-04';
        $this->configurationObject3->dataType = 'array';
        $this->configurationObject3->id = 4;
        $this->configurationObject3->updatedAt = '2020-03-01';

        $configurationsFromDatabase = collect([
            $this->configurationObject,
            $this->configurationObject1,
            $this->configurationObject2,
            $this->configurationObject3,
        ]);

        $this->configurationCollection = new ChannelConfigurationCollection($configurationsFromDatabase);
    }

    public function testCanGetAdSchedulingBuffer_returnsInteger()
    {
        $expectedResults = 90;
        $actualResults = $this->configurationCollection->getAdSchedulingBuffer();
        $this->assertEquals($expectedResults, $actualResults);
    }

    public function testCanGetAverageDailyReads_returnsInt()
    {
        $actualResults = $this->configurationCollection->getAverageDailyReads();
        $this->assertEquals(0, $actualResults);
    }

    public function testCanGetCity_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactAddress__city();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetPostal_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactAddress__postal();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetState_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactAddress__state();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetStreet_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactAddress__street();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetEmail_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactEmail();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetPhone_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelContactPhone();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetScreenshot_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelStorefrontImageContentScreenshot();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetUrl_returnsString()
    {
        $actualResults = $this->configurationCollection->getChannelUrl();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetClickthoughRate_returnsString()
    {
        $actualResults = $this->configurationCollection->getClickthroughRate();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetOpenRate_returnsString()
    {
        $actualResults = $this->configurationCollection->getOpenRate();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetPublicationSchedule_returnsString()
    {
        $actualResults = $this->configurationCollection->getPublicationScheduleDaily();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetTotalSubscribers_returnsString()
    {
        $actualResults = $this->configurationCollection->getTotalSubscribers();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetMcSelectedEmailListId_returnsString()
    {
        $actualResults = $this->configurationCollection->getMcSelectedEmailListId();
        $this->assertEmpty($actualResults);
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $actualResults = $this->configurationCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }
}
