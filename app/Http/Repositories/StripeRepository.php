<?php

namespace App\Http\Repositories;

use App\Controllers\BrandConfigurationController;
use Rollbar\Rollbar;
use Rollbar\Payload\Level;
use App\DTOs\StripeConnectedAccountDto;

class StripeRepository implements StripeRepositoryInterface
{
    public function connectStripeAccount(string $code): ?StripeConnectedAccountDto
    {
        try {
            \Stripe\Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

            $response = \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
            ]);

            if (isset($response->error)) {
                  throw new \Exception($response->error_description);
            }

            $dto = new StripeConnectedAccountDto($response);
            return $dto;
        } catch (\Exception $e) {
            Rollbar::log(Level::ERROR, $e->getMessage());
            return null;
        }
    }
}
