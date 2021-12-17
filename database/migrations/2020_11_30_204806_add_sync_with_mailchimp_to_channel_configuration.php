<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSyncWithMailchimpToChannelConfiguration extends Migration
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
                    'configurationName' => 'Auto Update Channel Stats From Mailchimp',
                    'configurationSlug' => 'autoUpdateChannelStatsFromMailchimp',
                    'dataType' => 'boolean',
                ],
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
            ->where('configurationSlug', '=', 'autoUpdateChannelStatsFromMailchimp')
            ->delete();
    }
}
