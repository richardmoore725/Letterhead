<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelDescriptionConfiguration extends Migration
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
                    'configurationName' => 'Channel Description',
                    'configurationSlug' => 'channelDescription'
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
            ->where('configurationName', '=', 'Channel Description')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelDescription')
            ->delete();
    }
}
