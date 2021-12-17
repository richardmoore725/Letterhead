<?php

namespace App\Listeners;

use App\Events\PromotionPublishedEvent;
use App\Jobs\EmailDayOnePromotionMetricsJob;
use App\Models\Promotion;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * This listener is responsible for responding to a PromotionPublished event and emailing a
 * notification to the user as well as to our sales time.
 *
 * Class NotifyPromoterOfPromotionPublicationListener
 * @package App\Listeners
 */
class NotifyPromoterOfDayOnePromotionMetricsListener implements ShouldQueue
{
    /**
     * @var Queue
     */
    private $queue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        Queue $queue
    ) {
        $this->queue = $queue;
    }

    public function handle(PromotionPublishedEvent $event)
    {
        /**
         * @var Promotion
         */
        $promotion = $event->promotion;

        /**
         * @var int
         */
        $userId = $event->userId;

        $emailJob = new EmailDayOnePromotionMetricsJob($promotion, $userId);

        $fiveHoursFromNow = CarbonImmutable::now()->add(5, 'hours');

        $this->queue->laterOn('send_email', $fiveHoursFromNow, $emailJob);
    }
}
