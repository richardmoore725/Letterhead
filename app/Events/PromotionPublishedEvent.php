<?php

namespace App\Events;

use App\Models\Promotion;
use App\Models\User;

/**
 * The `PromotionPublishedEvent` signals is that a promotion has recently been published.
 * It carries with it a Promotion object and a User object, responsible for ultimately
 * placing that promotion. We can choose to listen for and respond to this event in
 * our EventServiceProvider.
 *
 * Class PromotionPublishedEvent
 * @package App\Events
 */
class PromotionPublishedEvent extends Event
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
