<?php

/** email */
$router->group(['prefix' => 'api/v1/brands/{brandId:[0-9]+}/channels/{channelId:[0-9]+}/emails'], function () use ($router) {

    /**
     * @uses \App\Http\Controllers\EmailController::createEmail()
     * @uses \App\Http\Middleware\ValidateEmailDataMiddleware
     */
    $router->post('/', [
        'middleware' => [
            'validateEmail', 'verifyChannel', 'verifyBrand'
        ],
        'uses' => 'EmailController@createEmail',
    ]);

    /**
     * @uses \App\Http\Controllers\EmailController::createEmail()
     * @uses \App\Http\Middleware\ValidateEmailDataMiddleware
     */
    $router->get('/', [
        'middleware' => [
            'verifyBrand', 'verifyChannel',
        ],
        'uses' => 'EmailController@getEmailsByChannel',
    ]);

    $router->group(['prefix' => '{id:[0-9]+}'], function () use ($router) {
        /**
         * @uses \App\Http\Controllers\EmailController::updateEmail()
         * @uses \App\Http\Middleware\VerifyEmailMiddleware::handle()
         * @uses \App\Http\Middleware\ValidateEmailMiddleware::handle()
         */
        $router->post('/', [
            'middleware' => [
                'validateEmail', 'verifyEmail', 'verifyBrand', 'verifyChannel'
            ],
            'uses' => 'EmailController@updateEmail',
        ]);

        /**
         * @uses \App\Http\Controllers\EmailController::deleteEmail()
         * @uses \App\Http\Middleware\VerifyEmailMiddleware::handle()
         */
        $router->delete('/', [
            'middleware' => [
                'verifyEmail',
            ],
            'uses' => 'EmailController@deleteEmail',
        ]);

        /**
         * @uses \App\Http\Controllers\EmailController::getEmailById()
         * @uses \App\Http\Middleware\VerifyEmailMiddleware::handle()
         */
        $router->get('/', [
            'middleware' => [
                'verifyEmail',
            ],
            'uses' => 'EmailController@getEmailById',
        ]);
    });
});
