<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RenameAdvertisingRevenueShareInBrandConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('configurations')
        ->where([
            ['configurationName', 'Adversting Revenue Share'],
            ['configurationSlug', 'adverstingRevenueShare']
        ])
        ->update([
            'configurationName' => 'Advertising Revenue Share',
            'configurationSlug' => 'advertisingRevenueShare'
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('configurations')
        ->where([
            ['configurationName', 'Advertising Revenue Share'],
            ['configurationSlug', 'advertisingRevenueShare']
        ])
        ->update([
            'configurationName' => 'Adversting Revenue Share',
            'configurationSlug' => 'adverstingRevenueShare'
        ]);
    }
}
