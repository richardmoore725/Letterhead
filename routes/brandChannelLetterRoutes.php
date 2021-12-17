<?php

/**
 * $Letter routes
 *
 * We use these endpoints to manage letters, or what we
 * call newsletters within the code.
 */

/**
 * @var \Illuminate\Routing\Router
 *
 * As part of the Brand prefix, this route inherits the following
 * middleware.
 *
 * @uses \App\Http\Middleware\VerifyBrandMiddleware::handle()
 */
$router->group(['prefix' => 'authors'],
    function () use ($router) {
        $router->get('/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware
                 */
                'verifyChannel',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::getAuthorsByChannel()
             */
            'uses' => 'LetterController@getAuthorsByChannel',
        ]);
    });

/**
 * @var \Illuminate\Routing\Router
 *
 * As part of the Brand prefix, this route inherits the following
 * middleware.
 *
 * @uses \App\Http\Middleware\VerifyBrandMiddleware::handle()
 */
$router->group(['prefix' => 'letters'],
    function () use ($router) {

        /**
         * Create a new Letter.
         */
        $router->post('/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\ValidateLetterMiddleware::handle()
                 */
                'validateLetter',
            ],
            /**
             * @uses \App\Http\Controllers\LetterController::createLetter()
             */
            'uses' => 'LetterController@createLetter',
          ]);

        $router->delete('/{letterId: [0-9]+}', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

            ],

            /**
             * @uses \App\Http\Controllers\LetterController::deleteLetterById()
             */
            'uses' => 'LetterController@deleteLetterById',
        ]);

        /**
         * Get letters belonging to a given channel.
         */
        $router->get('/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::getLettersByChannelId()
             */
            'uses' => 'LetterController@getLettersByChannelId',
        ]);

        /**
         * Get a letter by its id.
         */
        $router->get('/{letterId: [0-9]+}', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyLetterMiddleware::handle()
                 */
                'verifyLetter',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::getLetterById()
             */
            'uses' => 'LetterController@getLetterById',
        ]);

        /**
         * Get a letter by its id.
         */
        $router->post('/{letterId: [0-9]+}', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\VerifyLetterMiddleware::handle()
                 */
                'verifyLetter',

                /**
                 * @uses \App\Http\Middleware\ValidateLetterMiddleware::handle()
                 */
                'validateLetter',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::updateLetter()
             */
            'uses' => 'LetterController@updateLetter',
        ]);

        /**
         * Send a test email
         * @deprecated
         */
        $router->post('/{letterId: [0-9]+}/sendTestEmail/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\VerifyLetterMiddleware::handle()
                 */
                'verifyLetter',

                /**
                 * @uses \App\Http\Middleware\VerifyMailChimpMiddleware::handle()
                 */
                'verifyMailChimp',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::sendLetterTestEmail()
             */
            'uses' => 'LetterController@sendLetterTestEmail',
        ]);

        /**
         * Immediately email a newsletter to a list
         */
        $router->post('/{letterId: [0-9]+}/sendNewsletterEmail/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\VerifyLetterMiddleware::handle()
                 */
                'verifyLetter',

                /**
                 * @uses \App\Http\Middleware\VerifyMailChimpMiddleware::handle()
                 */
                'verifyMailChimp',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::sendNewsletterEmail()
             */
            'uses' => 'LetterController@sendNewsletterEmail',
        ]);

        /**
         * Send a test email.
         */
        $router->post('/{letterId: [0-9]+}/test/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\VerifyLetterMiddleware::handle()
                 */
                'verifyLetter',
            ],

            /**
             * @uses \App\Http\Controllers\LetterController::test()
             */
            'uses' => 'LetterController@test',
        ]);
});

