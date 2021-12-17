<?php

namespace App\Tests;

use App\Events\OrderPurchasedEvent;
use App\Listeners\SendOrderNotificationToUser;
use App\Models\Channel;
use App\Models\PassportStamp;
use Illuminate\Contracts\Queue\Queue;

class SendOrderNotificatinToUserTest extends TestCase
{
    private $event;
    private $listener;
    private $queue;

    public function setUp() : void
    {
        $this->event = $this->createMock(OrderPurchasedEvent::class);
        $this->event->company = 'Hey';
        $this->event->channel = $this->createMock(Channel::class);
        $this->event->date = '2020-03-04';
        $this->event->orderId = 2;
        $this->event->originalPackagePrice = 100;
        $this->event->discountValue = 50;
        $this->event->finalPackagePrice = 50;
        $this->event->packageName = 'Wee';
        $this->event->passport = $this->createMock(PassportStamp::class);
        $this->queue = $this->createMock(Queue::class);

        $this->listener = new SendOrderNotificationToUser($this->queue);
    }

    public function testCanQueueNotification()
    {
        $this->queue
            ->expects($this->at(0))
            ->method('pushOn');

        $this->queue
            ->expects($this->at(1))
            ->method('pushOn');

        $actualResults = $this->listener->handle($this->event);

        $this->assertNull($actualResults);
    }
}
