<?php

/**
 * Our brands have access as long as they use a brand key to an API that allows them
 * to interface with some of their own data.
 * @todo Add middleware to ensure both the BrandSlug exists and the that they have the key appropriate to that brand.
 */
$router->group([
    'prefix' => '{brandSlug:[A-Za-z0-9\-]+}/api/v1',
    'middleware' => [
    ]
], function () use ($router) {
    /**
     * This route is a wild-card catch-all that's kind of a hack in order to catch OPTIONS headers.
     * Axios and other client-side AJAX libraries will generally send an OPTIONS request to the server
     * before a subsequent POST or GET. Lumen hates that stuff, though, so we have to tell it explicitly
     * to successfully return these.
     */
    $router->options('/{any:.*}', function () {
        return response('', 200);
    });

    $router->group(['prefix' => '/channels/{channelSlug:[A-Za-z0-9\-]+}'], function () use ($router) {
        /**
         * @deprecated
         * @uses \App\Http\Controllers\BrandController::getBrandChannelsAds()
         */
        $router->get('/ads', [
            'middleware' => 'brandApiKey',
            'uses' => 'BrandController@getBrandChannelsAds'
        ]);

        /**
         * @since 1.9.0
         * @uses \App\Http\Controllers\BrandController::getBrandChannelsAds()
         * @uses \App\Http\Middleware\BrandApiKeyMiddleware::handle()
         */
        $router->get('/promotions', [
            'middleware' => 'brandApiKey',
            'uses' => 'BrandController@getBrandChannelsAds'
        ]);
    });
});

$router->get('api/v1/brands/{brandId:[0-9]+}/channels/{channelId: [0-9]+}/packages',  [
    'middleware' => [
        /**
         * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
         */
        'verifyChannel',
    ],

    /**
     * @uses \App\Http\Controllers\PackageController::getPackages()
     */
    'uses' => 'PackageController@getPackages'
]);

$router->get('api/v1/channels/{channelId: [0-9]+}/promotions/types/packages',  [
        'middleware' => [
            /**
             * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
             */
            'verifyChannel',
        ],

        /**
     * @uses \App\Http\Controllers\AdTypeController::getAdTypesWithPricesByChannel()
     */
    'uses' => 'AdTypeController@getAdTypesWithPricesByChannel'
]);

$router->get('api/v1/channels/{channelId: [0-9]+}/promotions/types/{adTypeId: [0-9]+}/available-dates',  [
    'middleware' => [
        /**
         * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
         */
        'verifyChannel',
    ],

    /**
     * @uses \App\Http\Controllers\AdTypeController::getAvailableDatesByAdType()
     */
    'uses' => 'AdTypeController@getAvailableDatesByAdType'
]);


/**
 * @uses \App\Http\Controllers\BrandController::getBrandChannelPackageById()
 */
$router->get('api/v1/brands/{brandId:[0-9]+}/channels/{channelId: [0-9]+}/packages/{packageId: [0-9]+}',  [
    'middleware' => [
        /**
         * @uses \App\Http\Middleware\VerifyBrandMiddleware
         */
        'verifyBrand',

        /**
         * @uses \App\Http\Middleware\VerifyChannelMiddleware
         */
        'verifyChannel',
    ],
    /**
     * @uses \App\Http\Controllers\BrandController::getBrandChannelPackageById()
     */
    'uses' => 'BrandController@getBrandChannelPackageById'
]);

/**
 * @deprecated
 * @uses \App\Http\Controllers\AdController::orderAdvertisingPackage()
 */
$router->post('api/v1/brands/{brandId:[0-9]+}/channels/{channelId: [0-9]+}/beacons/ads/orders',  [
    'middleware' => [
        'passport',
    ],
    'uses' => 'AdController@orderAdvertisingPackage'
]);

/**
 * @since 1.9
 * @uses \App\Http\Controllers\AdController::orderAdvertisingPackage()
 * @uses \App\Http\Middleware\PassportMiddleware::handle()
 */
$router->post('api/v1/brands/{brandId:[0-9]+}/channels/{channelId: [0-9]+}/beacons/promotions/orders',  [
    'middleware' => [
        'passport'
    ],
    'uses' => 'AdController@orderAdvertisingPackage'
]);
