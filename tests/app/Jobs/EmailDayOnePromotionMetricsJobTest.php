<?php

namespace App\Tests;

use App\DTOs\PromotionDto;
use App\Http\Services\BeaconServiceInterface;
use App\Http\Services\ChannelServiceInterface;
use App\Http\Services\UserServiceInterface;
use App\Jobs\EmailDayOnePromotionMetricsJob;
use App\Models\Channel;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\Queue;

class EmailDayOnePromotionMetricsJobTest extends TestCase
{
    private $beaconService;
    private $channelService;
    private $job;
    private $promotion;
    private $queue;
    private $userService;

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

        $this->beaconService = $this->createMock(BeaconServiceInterface::class);
        $this->channelService = $this->createMock(ChannelServiceInterface::class);
        $this->userService = $this->createMock(UserServiceInterface::class);
        $this->queue = $this->createMock(Queue::class);
        $this->job = new EmailDayOnePromotionMetricsJob($this->promotion, 5);
    }

    public function testCannotQueueEmail_noChannel()
    {
        $this->channelService
            ->expects($this->once())
            ->method('getChannelById')
            ->with(4)
            ->willReturn(null);

        $actualResults = $this->job->handle($this->beaconService, $this->channelService, $this->queue, $this->userService);

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

        $actualResults = $this->job->handle($this->beaconService, $this->channelService, $this->queue, $this->userService);

        $this->assertEmpty($actualResults);
    }

    public function testCannotQueueEmail_noMetrics()
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

        $this->beaconService
            ->expects($this->once())
            ->method('getAdResourceByBeaconSlug')
            ->with('ads', 'promotions/5')
            ->willReturn(null);

        $actualResults = $this->job->handle($this->beaconService, $this->channelService, $this->queue, $this->userService);

        $this->assertEmpty($actualResults);
    }
}
