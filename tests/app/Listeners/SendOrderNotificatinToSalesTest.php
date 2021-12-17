<?php

namespace App\Tests;

use App\Events\OrderPurchasedEvent;
use App\Listeners\SendOrderNotificationToSales;
use App\Models\Channel;
use App\Models\PassportStamp;
use Illuminate\Contracts\Queue\Queue;

class SendOrderNotificatinToSalesTest extends TestCase
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
        $this->event->packageName = 'Wee';
        $this->event->price = 0;
        $this->event->passport = $this->createMock(PassportStamp::class);
        $this->queue = $this->createMock(Queue::class);

        $this->listener = new SendOrderNotificationToSales($this->queue);
    }

    public function testCanQueueNotification()
    {
        $this->queue
            ->expects($this->once())
            ->method('pushOn');

        $actualResults = $this->listener->handle($this->event);

        $this->assertNull($actualResults);
    }
}
