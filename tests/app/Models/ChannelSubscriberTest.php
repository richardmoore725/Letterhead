<?php

namespace App\Tests;


use App\DTOs\ChannelSubscriberDto;
use App\Models\ChannelSubscriber;
use Illuminate\Support\Collection;

class ChannelSubscriberTest extends TestCase
{
    private $subscriber;

    public function setUp() : void
    {
        $dto = new ChannelSubscriberDto();
        $this->subscriber = new ChannelSubscriber($dto);
    }

    public function testCanConvertToArray_returnArray()
    {
        $actualResults = $this->subscriber->convertToArray();
        $this->assertIsArray($actualResults);
    }
}