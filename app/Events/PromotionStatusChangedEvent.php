<?php

namespace App\Events;

use App\Models\Promotion;

/**
 * The `PromotionPendingEvent` signals that a promotion was recently
 * created and is now pending. It'll carry a promotion and the `userId`
 * of the user who created it. We'll listen and respond to it with the
 * EventServiceProvider.
 *
 * Class PromotionPendingEvent
 * @package App\Events
 */

class PromotionStatusChangedEvent extends Event
{
    /**
     * @var Promotion
     */
    public $promotion;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
    }
}
