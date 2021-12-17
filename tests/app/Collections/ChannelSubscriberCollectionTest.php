<?php

namespace App\Tests;

use App\Collections\ChannelSubscriberCollection;
use App\DTOs\ChannelSubscriberDto;
use App\Models\ChannelSubscriber;
use Illuminate\Database\Eloquent\Collection as BaseCollection;
use Illuminate\Support\Collection;

class ChannelSubscriberCollectionTest extends TestCase
{
    private $subscriberCollection;

    public function setUp() : void
    {
        $this->subscriberCollection = new ChannelSubscriberCollection();
    }

    public function testCanGetPublicArray_returnsArray()
    {
        $dto = new ChannelSubscriberDto(new \stdClass);
        $dto->channelId = 1;
        $dto->createdAt = '2021-01-11';
        $dto->deletedAt = '';
        $dto->email = 'hello@whereby.us';
        $dto->id = 1;
        $dto->name = 'jun su';
        $dto->updatedAt = '2021-01-11';
        $dto->userId = '1';


        $arrayOfSubscriberDtos = [ $dto ];

        $this->subscriberCollection = new ChannelSubscriberCollection($arrayOfSubscriberDtos);

        $actualResults = $this->subscriberCollection->getPublicArray();
        $this->assertIsArray($actualResults);
    }

    public function testCanGetPublicArrayWithObjectsArray_returnsArray()
    {
       $subscriberObject = new \stdClass();
       $subscriberObject->channelId = 1;
       $subscriberObject->createdAt = '2021-01-11';
       $subscriberObject->deletedAt = '';
       $subscriberObject->email = 'hello@whereby.us';
       $subscriberObject->id = 1;
       $subscriberObject->name = 'jun su';
       $subscriberObject->updatedAt = '2021-01-11';
       $subscriberObject->userId = 1;
       
       $subscriberObjectsFromDatabase = collect([ $subscriberObject ]);

       $this->subscriberCollection = new ChannelSubscriberCollection($subscriberObjectsFromDatabase);

       $actualResults = $this->subscriberCollection->getPublicArray();
       $this->assertIsArray($actualResults);
    }
}
