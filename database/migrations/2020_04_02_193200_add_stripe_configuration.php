<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStripeConfiguration extends Migration
{
        /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app('db')
            ->table('configurations')
            ->insertOrIgnore([
                [
                    'configurationName' => 'Stripe Account',
                    'configurationSlug' => 'stripeAccount'
                ],
                [
                    'configurationName' => 'Stripe Publishable Key',
                    'configurationSlug' => 'stripePublishableKey'
                ],                [
                    'configurationName' => 'Stripe Access Token',
                    'configurationSlug' => 'stripeAccessToken'
                ]
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'stripeAccount')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'stripePublishableKey')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'stripeAccessToken')
            ->delete();
    }
}
