<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMailchimpApiKeyAndSelectedListIdToChannelConfiguration extends Migration
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
                    'configurationName' => 'MailChimp Api Key ',
                    'configurationSlug' => 'mcApiKey'
                ],
                [
                    'configurationName' => 'MailChimp Selected Email List Id',
                    'configurationSlug' => 'mcSelectedEmailListId'
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
        ->where('configurationSlug', '=', 'mcApiKey')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'mcSelectedEmailListId')
        ->delete();
    }
}
