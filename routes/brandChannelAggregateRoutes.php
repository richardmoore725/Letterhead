<?php

/**
 * $Aggregate routes
 *
 * We use these endpoints to manage Aggregates
 */

/**
 * @var $router \Illuminate\Routing\Router
 *
 * As part of the Brand prefix, this route inherits the following
 * middleware.
 *
 * @uses \App\Http\Middleware\VerifyBrandMiddleware::handle()
 */
$router->group(['prefix' => 'aggregates'], function () use ($router) {

    /**
     * @uses \App\Http\Controllers\AggregateController::getAggregates()
     */
    $router->get('/', [
        /**
         * @uses \App\Http\Controllers\AggregateController::getAggregates()
         */
        'uses' => 'AggregateController@getAggregates',
    ]);

    /**
     * @uses \App\Http\Controllers\AggregateController::createAggregate()
     */
    $router->post('/', [
        'middleware' => [
            /**
             * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
             */
            'verifyChannel',

            /**
             * @uses \App\Http\Middleware\ValidateAggregateDateMiddleware::handle()
             */
            'validateAggregate'
        ],
        'uses' => 'AggregateController@createAggregate',
    ]);

    $router->group(['prefix' => '{aggregateId:[0-9]+}'], function () use ($router) {

        /**
         * @uses \App\Http\Controllers\AggregateController::updateAggregate()
         */
        $router->post('/', [
            'middleware' => [

                /**
                 * @uses \App\Http\Middleware\VerifyChannelMiddleware::handle()
                 */
                'verifyChannel',

                /**
                 * @uses \App\Http\Middleware\VerifyAggregateMiddleware::handle()
                 */
                'verifyAggregate',

                /**
                 * @uses \App\Http\Middleware\ValidateAggregateDateMiddleware::handle()
                 */
                'validateAggregate'
            ],
            'uses' => 'AggregateController@updateAggregate',
        ]);

        /**
         * @uses \App\Http\Controllers\AggregateController::deleteAggregateById()
         */
        $router->delete('/', [
            'middleware' => [

                /**
                 * @uses \App\Http\Middleware\VerifyAggregateMiddleware::handle()
                 */
                'verifyAggregate',
            ],
            'uses' => 'AggregateController@deleteAggregateById',
        ]);

        /**
         * @uses \App\Http\Controllers\AggregateController::getAggregateById()
         */
        $router->get('/', [
            'middleware' => [

                /**
                 * @uses \App\Http\Middleware\VerifyAggregateMiddleware::handle()
                 */
                'verifyAggregate',
            ],
            'uses' => 'AggregateController@getAggregateById',
        ]);
    });
});
