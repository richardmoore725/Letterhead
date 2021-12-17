<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelNewsletterUrlToChannelConfiguration extends Migration
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
                    'configurationName' => 'Channel Newsletter Url',
                    'configurationSlug' => 'channelNewsletterUrl'
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
            ->where('configurationSlug', '=', 'channelNewsletterUrl')
            ->delete();
    }
}