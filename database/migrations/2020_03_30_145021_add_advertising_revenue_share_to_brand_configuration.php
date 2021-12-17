<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAdvertisingRevenueShareToBrandConfiguration extends Migration
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
                    'configurationName' => 'Adversting Revenue Share',
                    'configurationSlug' => 'adverstingRevenueShare'
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
            ->where('configurationName', '=', 'Adversting Revenue Share')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'adverstingRevenueShare')
            ->delete();
    }
}
