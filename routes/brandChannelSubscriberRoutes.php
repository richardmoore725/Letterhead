<?php

$router->group([
    'middleware' => [
        'verifyChannel'
    ],
    'prefix' => 'channels/{channelId:[0-9]+}'
], function () use ($router) {
    $router->get('/subscribers', [
        'middleware' => [
            'authorizeUserAction:brand,read,brandId',
        ],
        'uses' => 'AuthoringSubscriberController@getSubscribersByChannel',
    ]);
});