<?php
/**
 * These routes govern the reading, writing, and management of advertisement orders.
 *
 * @var \Illuminate\Routing\Router $router
 */
$router->group(['prefix' => 'orders'], function () use ($router) {
    /**
     * Get all the orders for a given channel.
     *
     * @uses \App\Http\Controllers\OrderController::getOrders()
     * @uses \App\Http\Middleware\PassportMiddleware::handle()
     * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
     */
    $router->get('/', [
        'middleware' => ['passport', 'verifyChannel'],
        'uses' => 'OrderController@getOrders',
    ]);
});

