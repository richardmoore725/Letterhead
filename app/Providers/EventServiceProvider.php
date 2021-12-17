<?php

namespace App\Providers;

use App\Events\OrderPurchasedEvent;
use App\Events\PromotionStatusChangedEvent;
use App\Events\PromotionPublishedEvent;
use App\Events\PromotionScheduledEvent;
use App\Events\PromotionRescheduledEvent;
use App\Listeners\NotifyPromoterOfDayOnePromotionMetricsListener;
use App\Listeners\NotifyCreatorOfChangedPromotionStatusListener;
use App\Listeners\NotifyPromoterOfChangedPromotionStatusListener;
use App\Listeners\NotifyPromoterOfPromotionPublicationListener;
use App\Listeners\SendOrderNotificationToUser;
use App\Listeners\SendOrderNotificationToSales;
use App\Listeners\SendPromotionScheduledConfirmation;
use App\Listeners\SendPromotionRescheduledConfirmation;
use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        OrderPurchasedEvent::class => [
            SendOrderNotificationToUser::class,
            SendOrderNotificationToSales::class
        ],

        PromotionPublishedEvent::class => [
            NotifyPromoterOfPromotionPublicationListener::class,
            NotifyPromoterOfDayOnePromotionMetricsListener::class,
        ],

        PromotionScheduledEvent::class => [
            SendPromotionScheduledConfirmation::class,
        ],

        PromotionStatusChangedEvent::class => [
            NotifyCreatorOfChangedPromotionStatusListener::class,
            NotifyPromoterOfChangedPromotionStatusListener::class,
        ],

        PromotionRescheduledEvent::class => [
            SendPromotionRescheduledConfirmation::class,
        ],
    ];
}
