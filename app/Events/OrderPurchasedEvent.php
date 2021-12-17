<?php

namespace App\Events;

use App\Models\Channel;
use App\Models\PassportStamp;

class OrderPurchasedEvent extends Event
{
    public $company;
    public $channel;
    public $date;
    public $orderId;
    public $packageName;
    public $originalPackagePrice;
    public $discountValue;
    public $finalPackagePrice;
    public $passport;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(
        string $company,
        Channel $channel,
        string $date,
        int $orderId,
        string $packageName,
        int $originalPackagePrice,
        int $discountValue,
        int $finalPackagePrice,
        PassportStamp $passport
    ) {
        $this->company = $company;
        $this->channel = $channel;
        $this->date = $date;
        $this->orderId = $orderId;
        $this->packageName = $packageName;
        $this->originalPackagePrice = $originalPackagePrice;
        $this->discountValue = $discountValue;
        $this->finalPackagePrice = $finalPackagePrice;
        $this->passport = $passport;
    }
}
