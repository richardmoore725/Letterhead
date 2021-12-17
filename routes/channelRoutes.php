<?php

/**
 * $Channels
 *
 * Channels belong to Brands, but every so often we want to interface with
 * the Channel directly
 */
$router->group(['prefix' => 'channels'], function () use ($router) {

    /**
     * This route is a wild-card catch-all that's kind of a hack in order to catch OPTIONS headers.
     * Axios and other client-side AJAX libraries will generally send an OPTIONS request to the server
     * before a subsequent POST or GET. Lumen hates that stuff, though, so we have to tell it explicitly
     * to successfully return these.
     */
    $router->options('/{any:.*}', function () {
        return response('', 200);
    });

    /**
    * We can get all the channels with this method.
    * @uses \App\Http\Controllers\ChannelController::getChannels()
    * @uses \App\Http\Middleware\PassportMiddleware
    */
    $router->get('/',  [
        'middleware' => [
            'passport'
        ],
        'uses' => 'ChannelController@getChannels'
    ]);

    /**
     * @uses \App\Http\Middleware\VerifyChannelMiddleware
     */
    $router->group([
        'middleware' => [
            'verifyChannel',
        ],
    ], function () use ($router) {
        /**
         * @uses \App\Http\Controllers\ChannelController::deleteChannel()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware
         */
        $router->delete('/{channelId:[0-9]+}', 'ChannelController@deleteChannel');

        /**
         * @uses \App\Http\Controllers\ChannelController::getChannel()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware
         */
        $router->get('/{channelId:[0-9]+}', 'ChannelController@getChannel');

        /**
         * @deprecated
         */
        $router->get('/{channelId:[0-9]+}/ads/types', 'AdController@getAdTypesByChannelId');

        /**
         * @since 1.9
         * @uses \App\Http\Controllers\AdController::orderSinglePromotionPackage()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware
         */
        $router->post('/{channelId: [0-9]+}/orders', [
            'middleware' => [
                'verifyPromotionOrder',
                'verifyDiscountCode'
            ],
            'uses' => 'AdController@orderSinglePromotionPackage'
        ]);

        /**
         * @uses \App\Http\Controllers\AdController::getAdTypesByChannelId()
         */
        $router->get('/{channelId:[0-9]+}/promotions/types', 'AdController@getAdTypesByChannelId');

        $router->post('/{channelId:[0-9]+}/promotions/types/scaffold', [
            /**
             * This route inherits VerifyChannelMiddleware from its parent routes. Otherwise, this is only accessible
             * with a service platform key.
             *
             * @uses \App\Http\Middleware\VerifyChannelMiddleware
             * @uses \App\Http\Middleware\ServicePlatformKeyMiddleware
             */
            'middleware' => [
                'servicePlatformKey',
            ],
            /**
             * @uses \App\Http\Controllers\AdTypeController::scaffoldDefaultPromotionTypesForNewChannel()
             */
            'uses' => 'AdTypeController@scaffoldDefaultPromotionTypesForNewChannel',
        ]);


        /**
         * Retrieve a channel by its slug.
         *
         * @uses \App\Http\Controllers\ChannelController::getChannel()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware
         */
        $router->get('/{channelSlug:[A-Za-z0-9\-]+}', 'ChannelController@getChannel');
    });

    $router->group([
        'middleware' => [
            'passport',
            'verifyChannel',
        ],
        'prefix' => '/{channelId:[0-9]+}/discounts',
    ], function () use ($router) {
        $router->post('/', [
            'middleware' => [
                'validateDiscountCode',
            ],
            'uses' => 'DiscountCodeController@createDiscountCode'
        ]);

        $router->get('/', 'DiscountCodeController@getDiscountCodes');

        $router->get('/{discountCodeId: [0-9]+}', [
            'middleware' => [
                'verifyDiscountCode',
            ],
            'uses' => 'DiscountCodeController@getDiscountCode'
        ]);

        $router->get('/code/{discountCode: [A-za-z0-9\-]+}', [
            'middleware' => [
                'verifyDiscountCode',
            ],
            'uses' => 'DiscountCodeController@getDiscountCode'
        ]);

        $router->get('/code/{discountCode: [A-za-z0-9\-]+}/check', [
            'uses' => 'DiscountCodeController@checkIfCodeWasAlreadyDefined'
        ]);

        $router->post('/{discountCodeId: [0-9]+}', [
            'middleware' => [
                'validateDiscountCode',
                'verifyDiscountCode',
            ],
            'uses' => 'DiscountCodeController@updateDiscountCode'
        ]);

        $router->delete('/{discountCodeId: [0-9]+}', [
            'middleware' => [
                'verifyDiscountCode',
            ],
            'uses' => 'DiscountCodeController@deleteDiscountCodeById'
        ]);
    });
});
