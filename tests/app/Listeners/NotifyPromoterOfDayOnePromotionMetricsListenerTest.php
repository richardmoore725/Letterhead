<?php

namespace App\Tests;

use App\DTOs\PromotionDto;
use App\Events\PromotionPublishedEvent;
use App\Listeners\NotifyPromoterOfDayOnePromotionMetricsListener;
use App\Models\Promotion;
use Illuminate\Contracts\Queue\Queue;

class NotifyPromoterOfDayOnePromotionMetricsListenerTest extends TestCase
{
    private $event;
    private $listener;
    private $queue;
    private $promotion;

    public function setUp() : void
    {
        $promotionArray = [
            'channelId' => 4,
            'heading' => 'Hello',
            'emoji' => '🦐',
            'blurb' => 'Wee',
            'dateStart' => '2020-10-02',
            'id' => 5,
        ];

        $this->promotion = new Promotion(new PromotionDto($promotionArray));
        $this->event = new PromotionPublishedEvent($this->promotion, 5);

        $this->queue = $this->createMock(Queue::class);
        $this->listener = new NotifyPromoterOfDayOnePromotionMetricsListener($this->queue);
    }

    public function testCanQueueEmail()
    {
        $this->queue
            ->expects($this->once())
            ->method('laterOn');

        $actualResults = $this->listener->handle($this->event);

        $this->assertEmpty($actualResults);
    }
}
