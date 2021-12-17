<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandFeatureAdServiceFlag extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app('db')
            ->table('features')
            ->insertOrIgnore([
                [
                    'featureFlagName' => 'Advertising Beacon',
                    'featureFlagSlug' => 'beacon-ad',
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
            ->table('features')
            ->where('featureFlagSlug', '=', 'beacon-ad')
            ->delete();
    }
}
