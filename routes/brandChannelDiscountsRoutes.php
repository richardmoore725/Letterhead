<?php

$router->group([
    'middleware' => [
        'passport',
        'verifyChannel',
    ],
    'prefix' => 'channels/{channelId:[0-9]+}/discounts',
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