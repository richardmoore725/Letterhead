<?php

/*
|--------------------------------------------------------------------------
| API Routes (v2)
|--------------------------------------------------------------------------
|
| This is the root of our v2 PlatformService API, and subsequent sub-routes
| should be declared here.
|
| @see https://laravel.com/docs/7.x/routing
|
| Table of contents
| =================
| 1. $Promotions
*/

$router->group([ 'prefix' => 'api/v2', 'middleware' => []], function () use ($router) {

    $router->options('/{any:.*}', function () {
        return response('', 200);
    });

    /**
     * $Promotions
     */
    require __DIR__.'/../v2/promotions/promotions.php';
});
