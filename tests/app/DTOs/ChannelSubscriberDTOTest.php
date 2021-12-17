<?php

namespace App\Tests;

use App\DTOs\ChannelSubscriberDto;
use App\Models\ChannelSubscriber;
use Illuminate\Support\Collection;

class ChannelSubscriberDTOTest extends TestCase
{
    private $subscriber;

    public function setUp() : void
    {
        $this->channelSubscriberObject = new \stdClass();
        $this->channelSubscriberObject->channelId = 1;
        $this->channelSubscriberObject->channelSubscriberStatus = 1;
        $this->channelSubscriberObject->createdAt = '2020-12-24';
        $this->channelSubscriberObject->deletedAt = null;
        $this->channelSubscriberObject->email = 'test@test.com';
        $this->channelSubscriberObject->id = 1;
        $this->channelSubscriberObject->name = 'jun su';
        $this->channelSubscriberObject->updatedAt = '2020-12-25';
        $this->channelSubscriberObject->userId = 1;

        $this->channelSubscriberDto = new ChannelSubscriberDto($this->channelSubscriberObject);
    }

    public function testCanMapDtoPropertiesToColumns_returnArray()
    {
        $actualResults = $this->channelSubscriberDto->mapDtoPropertiesToColumns();
        $this->assertIsArray($actualResults);
    }
}