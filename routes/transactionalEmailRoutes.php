<?php

/** transactional email */
$router->group(['prefix' => 'api/v1/brands/{brandId:[0-9]+}/channels/{channelId:[0-9]+}/transactional'], function () use ($router) {
    /**
     * @uses \App\Http\Controllers\TransactionalEmailController::createTransactionalEmail()
     * @uses \App\Http\Middleware\ValidateTransactionalEmailDataMiddleware
     */
    $router->post('/', [
        'middleware' => [
            'validateTransactional', 'verifyChannel', 'verifyBrand'
        ],
        'uses' => 'TransactionalEmailController@createTransactionalEmail',
    ]);

    /**
     * @uses \App\Http\Controllers\TransactionalEmailController::createTransactionalEmail()
     * @uses \App\Http\Middleware\ValidateTransactionalEmailDataMiddleware
     */
    $router->get('/', [
        'middleware' => [
            'verifyBrand', 'verifyChannel',
        ],
        'uses' => 'TransactionalEmailController@getTransactionalEmailsByChannel'
    ]);

    $router->group(['prefix' => '{id:[0-9]+}'], function () use ($router) {
        /**
         * @uses \App\Http\Controllers\TransactionalEmailController::updateTransactionalEmail()
         * @uses \App\Http\Middleware\VerifyTransactionalEmailMiddleware::handle()
         * @uses \App\Http\Middleware\ValidateTransactionalEmailDataMiddleware::handle()
         */
        $router->post('/', [
            'middleware' => [
              'validateTransactional', 'verifyTransactional', 'verifyBrand', 'verifyChannel'
            ],
            'uses' => 'TransactionalEmailController@updateTransactionalEmail'
        ]);

        /**
         * @uses \App\Http\Controllers\TransactionalEmailController::deleteTransactionalEmail()
         * @uses \App\Http\Middleware\VerifyTransactionalEmailMiddleware::handle()
         */
        $router->delete('/', [
            'middleware' => [
                'verifyTransactional',
            ],
            'uses' => 'TransactionalEmailController@deleteTransactionalEmail',
        ]);

        /**
         * @uses \App\Http\Controllers\TransactionalEmailController:getTransactionalEmailById()
         * @uses \App\Http\Middleware\VerifyTransactionalEmailMiddleware::handle()
         */
        $router->get('/', [
            'middleware' => [
                'verifyTransactional',
            ],
            'uses' => 'TransactionalEmailController@getTransactionalEmailById',
        ]);
    });
});