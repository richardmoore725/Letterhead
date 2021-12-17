<?php

namespace App\Tests;

use App\DTOs\PromotionDto;
use App\Events\OrderPurchasedEvent;
use App\Events\PromotionPublishedEvent;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Listeners\NotifyPromoterOfPromotionPublicationListener;
use App\Listeners\SendOrderNotificationToUser;
use App\Models\Channel;
use App\Models\PassportStamp;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class NotifyPromoterOfPromotionPublicationListenerTest extends TestCase
{
    private $beaconService;
    private $channelService;
    private $event;
    private $listener;
    private $queue;
    private $promotion;
    private $userService;

    public function setUp() : void
    {
        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);

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
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->listener = new NotifyPromoterOfPromotionPublicationListener($this->beaconService, $this->channelService, $this->queue, $this->userService);
    }

    public function testCannotQueueEmail_noChannel()
    {
        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with(4)
            ->willReturn(null);

        $actualResults = $this->listener->handle($this->event);

        $this->assertEmpty($actualResults);
    }

    public function testCannotQueueEmail_noUser()
    {
        $channel = $this->createMock(Channel::class);

        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with(4)
            ->willReturn($channel);

        $this->userService
            ->expects($this->once())
            ->method('getUserById')
            ->with(5)
            ->willReturn(null);

        $actualResults = $this->listener->handle($this->event);

        $this->assertEmpty($actualResults);
    }

    public function testCanQueueEmail()
    {
        $channel = $this->createMock(Channel::class);
        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with(4)
            ->willReturn($channel);

        $this->userService
            ->expects($this->once())
            ->method('getUserById')
            ->with(5)
            ->willReturn($this->createMock(User::class));

        $channel->expects($this->exactly(2))
            ->method('getTitle')
            ->willReturn('Hello');

        $this->queue
            ->expects($this->once())
            ->method('pushOn');

        $actualResults = $this->listener->handle($this->event);

        $this->assertEmpty($actualResults);
    }
}
