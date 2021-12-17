<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the API routes for PlatformService.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
| Table of contents
| =================
| $ADS
| $BRANDS
| $PROMOTIONS
*/

/**
 * @see https://laravel.com/docs/7.x/routing
 */

$router->get('/', function () use ($router) {
    return $router->app->version();
});

/**
 * The `Public API` routes represent API calls that do not require
 * a passport or other authentication.
 *
 * @todo Abstract to their relevant domains, this is a non-intuitive way to org stuff.
 */
require __DIR__.'/../routes/publicApi.php';

/**
 * This is our general `api` route group. Everything here will be prefixed with `api/v1`, and the routes themselves
 * will generally follow a RESTful naming convention. <3. Each route will be passed through the Cors middleware,
 * which ensures we can talk to it from around the web.
 *
 */
$router->group(['prefix' => 'api', 'middleware' => []], function () use ($router) {
    $router->group(['prefix' => 'v1'], function () use ($router) {

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
         * $ADS
         *
         * @deprecated
         */
        $router->group([
            'middleware' => ['passport'],
            'prefix' => 'ads',
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

            /**
             * @uses \App\Http\Controllers\AdTypeController::getAdTypeById()
             */
            $router->get('/types/{adTypeId:[0-9]+}', 'AdTypeController@getAdTypeById');

            /**
             * @uses \App\Http\Controllers\AdController::deleteAd()
             */
            $router->delete('/{adId:[0-9]+}', 'AdController@deleteAd');
        });

        /**
         * $BRANDS
         *
         * We use our `brands` api to help create and maintain brands in our platform. Brands
         * are, next to Users, key to how the platform experience is really structured: Brands are
         * like "workspaces" that have any number of channels of distribution - websites, newsletters,
         * whatever - their own community of subscribers and advertisers, and so on.
         */
        $router->group(['prefix' => 'brands'], function () use ($router) {

            /**
             * @uses \App\Http\Controllers\BrandController::getBrands()
             */
            $router->get('/', 'BrandController@getBrands');

            /**
            * We can create a basic brand and a default channel belongs to it with only `slug` and `name`.
            *
            * @uses \App\Http\Controllers\BrandController::createBrandAndChannel()
            * @uses \App\Http\Middleware\PassportMiddleware
            * @uses \App\Http\Middleware\ValidateBrandDataMiddleware
            * @uses \App\Http\Middleware\ValidateChannelDataMiddleware
            * @uses \App\Http\Middleware\VerifyBrandDoesntExistMiddleware
            * @uses \App\Http\Middleware\VerifyChannelDoesntExistMiddleware
            */
            $router->post('/',  [
                'middleware' => [
                    'passport',
                    'verifyBrandDoesntExist',
                    'validateBrand',
                    'verifyChannelDoesntExist',
                    'validateChannel'
                ],
                'uses' => 'BrandController@createBrandAndChannel'
            ]);

            /**
             * Scaffold a new brand and channel from a service, using the service key.
             */
            $router->post('/scaffold',  [
                'middleware' => [
                    'servicePlatformKey',
                    'verifyBrandDoesntExist',
                    'validateBrand',
                    'verifyChannelDoesntExist',
                    'validateChannel'
                ],

                /**
                 * @uses \App\Http\Controllers\BrandController::createBrandAndChannel()
                 */
                'uses' => 'BrandController@createBrandAndChannel'
            ]);

            /**
             * @uses \App\Http\Middleware\VerifyBrandMiddleware
             */
            $router->group(['prefix' => '{brandId:[0-9]+}', 'middleware' => ['verifyBrand']], function () use ($router) {

                /**
                 * @uses \App\Http\Controllers\BrandController::getBrandById()
                 * @uses \App\Http\Middleware\VerifyBrandMiddleware::handle()
                 */
                $router->get('/', 'BrandController@getBrandById');

                /**
                 * @uses \App\Http\Controllers\BrandController::updateBrand()
                 * @uses \App\Http\Middleware\VerifyBrandMiddleware
                 */
                $router->post('/', [
                    'middleware' => [
                        'validateBrand'
                    ],
                    'uses' => 'BrandController@updateBrand',
                ]);

                /**
                 * These routes begin at /api/v1/brands/{brandId}/channels
                 */
                require __DIR__.'/../routes/brandChannelRoutes.php';

                require __DIR__.'/../routes/brandChannelSubscriberRoutes.php';

                require __DIR__.'/../routes/brandChannelDiscountsRoutes.php';

                /**
                 * Routes related to BrandConfigurations. Starting at api/v1/brands/{brandId}/configurations
                 * @uses \App\Models\BrandConfiguration
                 */
                $router->group(['prefix' => 'configurations'], function () use ($router) {

                    /**
                     * Set the value of a given BrandConfiguration by its slug. We'll confirm that the configuration exists
                     * before it reaches the controller.
                     *
                     * @uses \App\Http\Controllers\BrandConfigurationController::setBrandConfiguration()
                     * @uses \App\Http\Middleware\VerifyConfigurationMiddleware
                     */
                    $router->post('/{configurationSlug:[A-Za-z0-9\-\_]+}', [
                        'middleware' => ['verifyConfiguration'],
                        'uses' => 'BrandConfigurationController@setBrandConfiguration'
                    ]);

                });
            });

            $router->group(['prefix' => '{brandSlug:[A-Za-z0-9\-]+}'], function () use ($router) {

                /**
                 * Get a brand directly by its slug.
                 * @uses \App\Http\Controllers\BrandController::getBrandBySlug()
                 */
                $router->get('/', 'BrandController@getBrandBySlug');
            });
        });

        /**
         * $Promotion routes
         * We use these endpoints to manage promotions, which users can create and place
         * across newsletters to literally promote something they love ♥.
         *
         * @see api/v1/promotions
         * @since 1.9.0
         * @uses \App\Http\Middleware\PassportMiddleware::handle()
         */
        $router->group([
            'prefix' => 'promotions',
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

            $router->post('/events/promotion-published', [
                /**
                 * @uses \App\Http\Controllers\AdController::broadcastPromotionPublishedEvent()
                 */
                'uses' => 'AdController@broadcastPromotionPublishedEvent',
            ]);

            $router->post('/events/promotion-rescheduled', [
                /**
                 * @uses \App\Http\Controllers\AdController::broadcastPromotionRescheduledEvent()
                 */
                'uses' => 'AdController@broadcastPromotionRescheduledEvent',
            ]);

            /**
             * @see api/v1/promotions/types/
             * @uses \App\Http\Controllers\AdTypeController::getAdTypeById()
             */
            $router->get('/types/{adTypeId:[0-9]+}', [
                'middleware' => [
                    'passport',
                ],
                'uses' => 'AdTypeController@getAdTypeById',
            ]);

            /**
             * @see api/v1/promotions
             * @uses \App\Http\Controllers\AdController::deleteAd()
             */
            $router->delete('/{adId:[0-9]+}', [
                'middleware' => [
                    'passport',
                ],
                'uses' => 'AdController@deleteAd',
            ]);

             /**
             * @see api/v1/promotions
             * @uses \App\Http\Controllers\AdController::getAdById()
             */
            $router->get('/{adId:[0-9]+}', [
                'middleware' => [
                    'passport',
                ],
                'uses' => 'AdController@getAdById',
            ]);
        });

        require __DIR__.'/../routes/channelRoutes.php';
        require __DIR__.'/../routes/configurationRoutes.php';
        require __DIR__.'/../routes/userRoutes.php';
    });
});

/**
 * @deprecated
 */
$router->post('api/v1/ads/orders',  [
    'middleware' => [
        'servicePlatformKey'
    ],
    'uses' => 'AdController@orderAdsFromPipedrive'
]);

/** platform-events */
$router->group(['prefix' => 'api/v1/platform-events'], function () use ($router) {

    /**
     * @uses \App\Http\Controllers\PlatformEventController::createPlatformEvent()
     * @uses \App\Http\Middleware\ValidatePlatformEventDataMiddleware
     */
    $router->post('/', [
        'middleware' => [
            'validatePlatformEvent',
        ],
        'uses' => 'PlatformEventController@createPlatformEvent',
    ]);

    $router->group(['prefix' => '{id:[0-9]+}'], function () use ($router) {
        /**
         * @uses \App\Http\Controllers\PlatformEventController::updatePlatformEvent()
         * @uses \App\Http\Middleware\VerifyPlatformEventMiddleware::handle()
         * @uses \App\Http\Middleware\ValidatePlatformEventMiddleware::handle()
         */
        $router->post('/', [
            'middleware' => [
                'validatePlatformEvent', 'verifyPlatformEvent',
            ],
            'uses' => 'PlatformEventController@updatePlatformEvent',
        ]);

        /**
         * @uses \App\Http\Controllers\PlatformEventController::deletePlatformEvent()
         * @uses \App\Http\Middleware\VerifyPlatformEventMiddleware::handle()
         */
        $router->delete('/', [
            'middleware' => [
                'verifyPlatformEvent',
            ],
            'uses' => 'PlatformEventController@deletePlatformEvent',
        ]);
    });
});

/**
 * @since 1.9
 */
$router->post('api/v1/promotions/orders',  [
    'middleware' => [
        'servicePlatformKey'
    ],
    'uses' => 'AdController@orderAdsFromPipedrive'
]);
