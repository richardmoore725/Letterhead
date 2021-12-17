<?php

$router->group(['prefix' => 'channels'], function () use ($router) {

    /**
     * @uses \App\Http\Controllers\ChannelController::createBrandChannel()
     * @uses \App\Http\Middleware\ValidateChannelDataMiddleware
     */
    $router->post('/', [
        'middleware' => [
            'validateChannel',
        ],
        'uses' => 'ChannelController@createBrandChannel',
    ]);

    $router->group(['prefix' => '{channelId:[0-9]+}'], function () use ($router) {
        /**
         * @uses \App\Http\Controllers\ChannelController::updateBrandChannel()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
         */
        $router->post('/', [
            'middleware' => [
                'validateChannel', 'verifyChannel'
            ],
            'uses' => 'ChannelController@updateBrandChannel',
        ]);

        $router->post('/images', 'LetterController@uploadImages');

        /**
         * @uses \App\Http\Controllers\AuthController@getUserById
         * @uses \App\Http\Middleware\PassportMiddleware::handle()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
         * @uses \App\Http\Middleware\AuthorizeUserMiddleware::handle()
         */
        $router->get('/orders/{orderId: [0-9]+}/customers/{customerId: [0-9]+}', [
            'middleware' => [
                'passport',
                'verifyChannel',
                'authorize'
            ],
            'uses' => 'AuthController@getUserById',
        ]);

        /**
         * The following routes live within the `ads` domain belonging to a specific channel,
         * and we use them predominately to control the management of ads, ad types, and more.
         *
         * The current middleware requires that a valid passport exists and will verify that
         * the channel exists.
         *
         * @deprecated
         *
         * @uses \App\Http\Middleware\PassportMiddleware::handle()
         * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
         */
        $router->group([
            'middleware' => [
                'passport',
                'verifyChannel',
            ],
            'prefix' => 'ads',
        ], function () use ($router) {
            /**
             * @uses \App\Http\Controllers\AdController::getAds()
             */
            $router->get('/', 'AdController@getAds');

            /**
             * @uses \App\Http\Controllers\AdController::createBrandChannelAd()
             */
            $router->post('/', 'AdController@createBrandChannelAd');

            /**
             * @uses \App\Http\Controllers\AdController::updateAd()
             */
            $router->post('/{adId: [0-9]+}', 'AdController@updateAd');


            /**
             * @uses \App\Http\Controllers\PackageController::createPackage()
             */
            $router->post('/packages', 'PackageController@createPackage');

            /**
             * @uses \App\Http\Controllers\PackageController::deletePackage()
             */
            $router->delete('/packages/{packageId: [0-9]+}', 'PackageController@deletePackage');

            /**
             * @uses \App\Http\Controllers\PackageController::updatePackage()
             */
            $router->post('/packages/{packageId:[0-9]+}', 'PackageController@updatePackage');

            /**
             * @uses \App\Http\Controllers\AdTypeController::createAdType()
             */
            $router->post('/types', 'AdTypeController@createAdType');

            /**
             * @uses \App\Http\Controllers\AdTypeController::getAdTypesByChannel()
             */
            $router->get('/types', 'AdTypeController@getAdTypesByChannel');

            /**
             * @uses \App\Http\Controllers\AdTypeController::getAdTypeById()
             */
            $router->get('/types/{adTypeId:[0-9]+}', 'AdTypeController@getAdTypeById');

            /**
             * @uses \App\Http\Controllers\AdTypeController::deleteAdType()
             */
            $router->delete('/types/{adTypeId:[0-9]+}', 'AdTypeController@deleteAdType');

            /**
             * @uses \App\Http\Controllers\AdTypeController::updateAdType()
             */
            $router->post('/types/{adTypeId:[0-9]+}', 'AdTypeController@updateAdType');

            /**
             * @uses \App\Http\Controllers\AdTypeController::getDisabledDatesByAdType()
             */
            $router->get('/types/booked/{adTypeId:[0-9]+}', 'AdTypeController@getDisabledDatesByAdType');
        });

        $router->group([
            'middleware'    => ['verifyChannel'],
            'prefix'        => 'constant-contact'
        ], function ()  use ($router) {
             /**
             * Get the Constant Contact Access token and Refresh Token and store to channel table.
             * @call api/v1/brands/{brandId}/channels/{channelId}/constant-contact/get_access_token
             * @uses \App\Http\Controller\ConstantContactController@getAccessToken
             */
            $router->post('/get_access_token', 'ConstantContactController@getAccessToken');
            $router->group([
                'middleware'    => ['verifyConstantContact'],
                'prefix'        => 'main'
            ], function() use ($router) {

                /**
                 * Get the email campaign list
                 * @call api/v1/brands/{brandId}/channels/{channelId}/constant-contact/main/emails
                 */
                $router->post('/emails', 'ConstantContactController@getEmailCampaignList');
            });
        });
        /**
         * $Promotions
         *
         * `brandChannelPromotionRoutes` contain our endpoints related to the creation and
         * management of promotions, or ads.
         */
        require __DIR__.'/../routes/brandChannelPromotionRoutes.php';

        /**
         * Beacons are the high-level services we provide through our platform, like "Ads," or
         * down the road "Newsletters." These routes are our entry-points for interfacing with
         * these at the channel level.
         *
         * @deprecated
         */
        $router->group(['prefix' => 'beacons/{beaconSlug:ads}'], function () use ($router) {
            /**
             * Get the resources from a brand channel's beacon.
             * @uses \App\Http\Controllers\BeaconController::getResource()
             */
            $router->get('/', [
                'middleware' => [
                    'passport',
                    'verifyChannel',
                ],
                'uses' => 'BeaconController@getResource',
            ]);

            /**
             * Get the resources from a given brand channel's beacon, passing addition paths.
             * @uses \App\Http\Controllers\BeaconController::getResource()
             */
            $router->get('{resource:.*}', 'BeaconController@getResource');

            /**
             * @uses \App\Http\Controllers\BeaconController::createResource()
             */
            $router->post('/{resource:.*}', 'BeaconController@createResource');
        });


        /**
         * Beacons are the high-level services we provide through our platform, like "Promotions," or
         * down the road "Newsletters." These routes are our entry-points for interfacing with
         * these at the channel level.
         *
         * @since 1.9.0
         */
        $router->group(['prefix' => 'beacons/{beaconSlug:promotions}'], function () use ($router) {
            /**
             * Get the resources from a brand channel's beacon.
             * @uses \App\Http\Controllers\BeaconController::getResource()
             */
            $router->get('/', [
                'middleware' => [
                    'passport',
                    'verifyChannel',
                ],
                'uses' => 'BeaconController@getResource',
            ]);

            /**
             * Get the resources from a given brand channel's beacon, passing addition paths.
             * @uses \App\Http\Controllers\BeaconController::getResource()
             */
            $router->get('{resource:.*}', 'BeaconController@getResource');

            /**
             * @uses \App\Http\Controllers\BeaconController::createResource()
             */
            $router->post('/{resource:.*}', 'BeaconController@createResource');
        });

        $router->group(['prefix' => 'configurations'], function () use ($router) {
            /**
             * @uses \App\Http\Controllers\MailChimpController::getLists()
             */
            $router->get('/lists',  [
                'middleware' => [
                    'verifyChannel',
                    'verifyMailChimp',
                ],
                'uses' => 'MailChimpController@getLists'
            ]);

            /**
             * @uses \App\Http\Controllers\MailChimpController::getListById()
             */
            $router->get('/list/{id:[A-Za-z0-9\-]+}',  [
                'middleware' => [
                    'verifyChannel',
                    'verifyMailChimp',
                ],
                'uses' => 'MailChimpController@getListById'
            ]);


            /**
             * @uses \App\Http\Controllers\BrandController::updateChannelConfigurationValue()
             */
            $router->post('/', 'BrandController@updateChannelConfigurationValue');

            /**
             * Use `api/v1/brands/#/channels/#/configurations/mcSelectedEmailListId` to enable
             * the channel's MailChimp integration if it's not yet, as well as to set the value.
             *
             * @since 1.14.0
             */
            $router->post('/{configurationSlug:mcSelectedEmailListId}', [
                'middleware' => [
                    /**
                     * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                     */
                    'verifyChannel',

                    /**
                     * @uses \App\Http\Middleware\VerifyConfigurationMiddleware::handle()
                     */
                    'verifyConfiguration',
                ],

                /**
                 * @uses \App\Http\Controllers\ChannelController::updateChannelConfigurationMailChimpListId()
                 */
                'uses' => 'ChannelController@updateChannelConfigurationMailChimpListId',
            ]);

            /**
             * Use `api/v1/brands/#/channels/#/configurations/mcApiKey` to validate the mc api key,
             * as well as to set the value.
             */
            $router->post('/{configurationSlug:mcApiKey}', [
                'middleware' => [
                    /**
                     * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                     */
                    'verifyChannel',

                    /**
                     * @uses \App\Http\Middleware\VerifyConfigurationMiddleware::handle()
                     */
                    'verifyConfiguration',
                ],

                /**
                 * @uses \App\Http\Controllers\ChannelController::updateChannelConfigurationMailChimpApiKey()
                 */
                'uses' => 'ChannelController@updateChannelConfigurationMailChimpApiKey',
            ]);

            /**
             * @uses \App\Http\Middleware\VerifyConfigurationMiddleware
             * @uses \App\Http\Controllers\ChannelController::updateChannelConfiguration()
             */
            $router->post('/{configurationSlug:[A-Za-z0-9\-\_]+}', [
                'middleware' => ['verifyChannel', 'verifyConfiguration'],
                'uses' => 'ChannelController@updateChannelConfiguration'
            ]);
        });

        /**
         * $Letters
         *
         * `brandChannelLetterRoutes` contain our endpoints related to the creation and
         * management of letters, or newsletters.
         * @since 1.15.0
         */
        require __DIR__.'/../routes/brandChannelLetterRoutes.php';

        require __DIR__.'/../routes/brandChannelAggregateRoutes.php';

        require __DIR__.'/../routes/orderRoutes.php';
    });
});
