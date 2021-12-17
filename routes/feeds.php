<?php
/**
 *
 * @var \Illuminate\Routing\Router $router
 */

$router->group(['prefix' => 'feeds'], function () use ($router) {
    $router->get('/promotions', [
        /**
         * @uses \App\Http\Controllers\PromotionController::getPromotionsFeed()
         */
        'uses' => 'PromotionController@getPromotionsFeed',
    ]);
});
