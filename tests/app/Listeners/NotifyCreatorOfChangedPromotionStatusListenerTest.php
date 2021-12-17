<?php

use App\Events\PromotionStatusChangedEvent;
use App\Listeners\NotifyCreatorOfChangedPromotionStatusListener;
use App\DTOs\ChannelDto;
use App\DTOs\PromotionDto;
use App\Models\Channel;
use App\Models\Promotion;
use App\Tests\TestCase;
use Illuminate\Contracts\Queue\Queue;

class NotifyCreatorOfChangedPromotionStatusListenerTest extends TestCase
{
    private $event;
    private $listener;
    private $queue;
    private $promotion;

    public function setUp() : void
    {
        $dto = new ChannelDto();
        $dto->id = 2;
        $this->channel = new Channel($dto);
        $this->channel->setAccentColor('#bada55');
        $this->channel->setBrandId(3);
        $this->channel->setChannelSlug('wee');
        $this->channel->setChannelDescription('woo');
        $this->channel->setChannelImage('');
        $this->channel->setTitle('I am a channel');
        $this->channel->setChannelHorizontalLogo('');
        $this->channel->setChannelSquareLogo('');
        $this->channel->setDefaultEmailFromName('jun');
        $this->channel->setDefaultFromEmailAddress('junsu@whereby.us');
        $this->channel->setDefaultFont('serif');
        $this->channel->setEnableChannelAuthoring(false);

        $promotionArray = [
            'channelId' => 4,
            'heading' => 'Hello',
            'emoji' => '🦐',
            'blurb' => 'Wee',
            'dateStart' => '2020-10-02',
            'id' => 5,
        ];

        $this->promotion = new Promotion(new PromotionDto($promotionArray));

        $this->event = new PromotionStatusChangedEvent($this->promotion);

        $this->queue = $this->createMock(Queue::class);
        $this->listener = new NotifyCreatorOfChangedPromotionStatusListener($this->queue);
    }

    public function testCanGetPromotion()
    {
        $this->queue
            ->expects($this->once())
            ->method('pushOn');

        $actualResults = $this->listener->handle($this->event);
        $this->assertEmpty($actualResults);
    }
}