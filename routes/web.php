<?php

$router->get('stripe-connect/callback', 'BrandConfigurationController@connectBrandAccountToStripe');

/**
 * @uses App\Http\Controllers\BrandConfigurationController::deauthorizedFromStripe()
 */
$router->post('stripe-connect/account-deauthorized', 'BrandConfigurationController@deauthorizedFromStripe');

/**
 * promotions.tryletterhead.com/feed/
 */
$router->get('/feed', [
    'middleware' => \App\Http\Middleware\BrandApiKeyMiddleware::class,

    /**
     * @uses \App\Http\Controllers\PromotionController::getPromotionsFeed()
     */
    'uses' => 'PromotionController@getPromotionsFeed',
]);
