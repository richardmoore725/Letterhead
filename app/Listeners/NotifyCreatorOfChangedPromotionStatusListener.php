<?php

namespace App\Listeners;

use App\Events\PromotionStatusChangedEvent;
use App\Jobs\EmailChannelAdministratorsPromoStatusChangedJob;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyCreatorOfChangedPromotionStatusListener implements ShouldQueue
{
    private $queue;

    public function __construct(
        Queue $queue
    ) {
        $this->queue = $queue;
    }

    public function handle(PromotionStatusChangedEvent $event)
    {
        $promotion = $event->promotion;

        $emailChannelAdminJob = new EmailChannelAdministratorsPromoStatusChangedJob($promotion);

        $this->queue->pushOn('send_email', $emailChannelAdminJob);
    }
}
