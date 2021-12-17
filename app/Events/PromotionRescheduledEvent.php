<?php

namespace App\Events;

use App\Models\Promotion;

class PromotionRescheduledEvent extends Event
{
    /**
     * @var Promotion
     */
    public $promotion;
    public $userId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Promotion $promotion, int $userId)
    {
        $this->promotion = $promotion;
        $this->userId = $userId;
    }
}
