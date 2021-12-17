<?php

namespace App\DTOs;

use Stripe\StripeObject;

class StripeConnectedAccountDto
{
    public $accountId;
    public $publishableKey;
    public $accessToken;
    public function __construct(StripeObject $object)
    {
        if (empty($object)) {
            return;
        }

        $this->accountId = $object->stripe_user_id;
        $this->publishableKey = $object->stripe_publishable_key;
        $this->accessToken = $object->access_token;
    }
}
