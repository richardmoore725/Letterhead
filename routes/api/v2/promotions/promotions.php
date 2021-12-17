<?php
/*
|--------------------------------------------------------------------------
| Promotion routes (v2)
|--------------------------------------------------------------------------
|
| This is the root for our v2/api Promotions resource. By this we just mean that
| every route that has to do with _promotions_ - what we call advertisements -
| will start here. <3.
*/

$router->group([ 'prefix' => 'promotions', 'middleware' => []], function () use ($router) {

    $router->get('/', [
        'middleware' => [
            \App\Http\Middleware\ApiKeyMiddleware::class,
        ],

        /**
         * @uses \App\Http\Controllers\AdController::getAds()
         */
        'uses' => 'AdController@getAds',
    ]);
});
