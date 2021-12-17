<?php

namespace App\Listeners;

use App\Events\PromotionStatusChangedEvent;
use App\Job\EmailChannelAdministratorsPromoStatusHasChangedJob;
use App\Jobs\EmailPromoterPromoStatusChangedJob;
use App\Models\Promotion;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyPromoterOfChangedPromotionStatusListener implements ShouldQueue
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

        $emailPromoterJob = new EmailPromoterPromoStatusChangedJob($promotion);

        $this->queue->pushOn('send_email', $emailPromoterJob);
    }
}
