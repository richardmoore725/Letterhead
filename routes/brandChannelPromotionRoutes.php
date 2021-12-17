<?php

/**
 * The following routes live within the `promotions` domain belonging to a specific channel,
 * and we use them predominately to control the management of promotions, ad types, and more.
 *
 * The current middleware requires that a valid passport exists and will verify that
 * the channel exists.
 *
 * @since 1.9.1
 *
 * @uses \App\Http\Middleware\PassportMiddleware::handle()
 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
 */
$router->group([
    'middleware' => [
        'passport',
        'verifyChannel',
    ],
    'prefix' => 'promotions',
], function () use ($router) {
    /**
     * @uses \App\Http\Controllers\PromotionController::getPromotions()
     */
    $router->get('/', 'PromotionController@getPromotions');

    /**
     * @uses \App\Http\Controllers\AdController::createBrandChannelAd()
     */
    $router->post('/', 'AdController@createBrandChannelAd');

    /**
     * @note We prefer to just use `'POST'` and an empty path `/` to create a resource,
     * - but so as not to create a breaking change we'll use `/new` until we can
     * - deprecate the above.
     * @uses \App\Http\Controllers\PromotionController::createPendingPromotion()
     */
    $router->post('/new', 'PromotionController@createPendingPromotion');

    /**
     * @uses \App\Http\Controllers\AdController::updateAd()
     */
    $router->post('/{adId: [0-9]+}', 'AdController@updateAd');

    /**
     * @uses \App\Http\Controllers\PromotionController::updatePromotionStatusToApproved()
     */
    $router->post('/{adId: [0-9]+}/approve', [
        'middleware' => [
            'authorizeUserAction:brand,create,brandId',
            'verifyPromotion'
        ],
        'uses' => 'PromotionController@updatePromotionStatusToApproved'
    ]);

    /**
     * @uses \App\Http\Controllers\PromotionController::updatePromotionStatusToRequestChanges()
     */
    $router->post('/{adId: [0-9]+}/request-changes', [
        'middleware' => [
            'authorizeUserAction:brand,create,brandId',
            'verifyPromotion'
        ],
        'uses' => 'PromotionController@updatePromotionStatusToRequestChanges'
    ]);

    /**
     * Get the aggregrate metrics of a specific channel by that's channel's ID.
     *
     * @uses \App\Http\Controllers\AdController::getPromotionMetricsByChannelId()
     * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
     * @uses \App\Http\Middleware\PassportMiddleware::handle()
     */
    $router->get('/metrics', 'AdController@getPromotionMetricsByChannelId');

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
     * Update the template of a specific promotion type. Send it MJML, and it will convert
     * and set it as the promotion type template : ).
     *
     * @call api/v1/brands/{brandId}/channels/{channelId}/promotions/types/{adTypeId}/template
     * @since 1.32.0
     * @uses \App\Http\Controllers\AdTypeController::updatePromotionTypeTemplate()
     * @uses \App\Http\Middleware\PassportMiddleware
     * @uses \App\Http\Middleware\VerifyChannelMiddleware
     */
    $router->post('/types/{adTypeId:[0-9]+}/template', 'AdTypeController@updatePromotionTypeTemplate');

    /**
     * @uses \App\Http\Controllers\AdTypeController::getDisableDatesByAdType()
     */
    $router->get('/types/booked/{adTypeId:[0-9]+}', 'AdTypeController@getDisabledDatesByAdType');

});

$router->group([
    'middleware' => [
        'passport',
    ],
    'prefix' => 'promotions/messages',
],  function () use ($router) {

        /**
         * Get Messages on a specific promotion.
         */
        $router->get('/', [
            /**
             * @uses \App\Http\Controllers\PromotionMessageController::getMessages()
             */
            'uses' => 'PromotionMessageController@getMessages',
        ]);

        /**
         * Create a new Message.
         */
        $router->post('/', [
            'middleware' => [
                /**
                 * @uses \App\Http\Middleware\ValidatePromotionMessageDataMiddleware::handle()
                 */
                'validatePromotionMessage',
            ],

            /**
             * @uses \App\Http\Controllers\PromotionMessageController::createMessage()
             */
            'uses' => 'PromotionMessageController@createMessage',
        ]);
    }
);
