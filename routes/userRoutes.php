<?php

/**
 * $Users
 *
 * Routes following `api/v1/users` are designed to interface with the Platform users resource specifically.
 *
 * @uses \App\Http\Middleware\PassportMiddleware
 */
$router->group(['prefix' => 'user', 'middleware' => ['passport']], function () use ($router) {
    /**
     * @uses \App\Http\Controllers\AuthController::getUserFromPassportStamp()
     */
    $router->get('/', 'AuthController@getUserFromPassportStamp');

    /**
     * @uses \App\Http\Controllers\AuthController@updateUser
     * @uses \App\Http\Middleware\PassportMiddleware::handle()
     * @uses \App\Http\Middleware\AuthorizeUserMiddleware::handle()
     */
    $router->post('/{userId: [0-9]+}', [
        'middleware' => [
            'authorize'
        ],
        'uses' => 'AuthController@updateUser',
    ]);

    /**
     * @deprecated
     * @uses \App\Http\Controllers\AdController::getAdCreditsByUserId()
     */
    $router->get('/{id: [0-9]+}/ad-credits', 'AdController@getAdCreditsByUserId');

    /**
     * @since 1.9.0
     * @uses \App\Http\Controllers\AdController::getAdCreditsByUserId()
     */
    $router->get('/{id: [0-9]+}/promotion-credits', 'AdController@getAdCreditsByUserId');


    /**
     * @uses \App\Http\Controllers\BrandController::getBrandsUserAdministrates()
     */
    $router->get('/{id: [0-9]+}/brands', [
        /**
         * @uses \App\Http\Middleware\AuthorizeUserMiddleware::handle()
         */
        'middleware' => ['authorize'],
        'uses' => 'BrandController@getBrandsUserAdministrates',
    ]);

    /**
     * @uses \App\Http\Controllers\PlatformController::getPlatformsUserAdministrates()
     */
    $router->get('/{id: [0-9]+}/platforms', [
        /**
         * @uses \App\Http\Middleware\AuthorizeUserMiddleware::handle()
         */
       'middleware' => ['authorize'],
       'uses' => 'PlatformController@getPlatformsUserAdministrates',
    ]);

    /**
     * @uses \App\Http\Controllers\AdController::getOrdersByUserId()
     */
    $router->get('/{id: [0-9]+}/orders', 'AdController@getOrdersByUserId');
});
