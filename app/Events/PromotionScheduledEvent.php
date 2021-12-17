<?php

namespace App\Events;

use App\Models\Channel;
use App\Models\PassportStamp;

class PromotionScheduledEvent extends Event
{
    /**
     * @var Channel
     */
    public $newsletter;

    /**
     * @var PassportStamp
     */
    public $passport;

    /**
     * @var
     */
    public $promotion;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Channel $newsletter, PassportStamp $passportStamp, $promotionScheduled)
    {
        $this->newsletter = $newsletter;
        $this->passport = $passportStamp;
        $this->promotion = $promotionScheduled;
    }
}
