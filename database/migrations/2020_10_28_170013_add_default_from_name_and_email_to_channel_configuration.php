<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultFromNameAndEmailToChannelConfiguration extends Migration
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
                    'configurationName' => 'Default Email From Name',
                    'configurationSlug' => 'defaultEmailFromName',
                    'dataType' => 'string',
                ],
                [
                    'configurationName' => 'Default From Email Address',
                    'configurationSlug' => 'defaultFromEmailAddress',
                    'dataType' => 'string',
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
        ->where('configurationSlug', '=', 'defaultEmailFromName')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'defaultFromEmailAddress')
        ->delete();
    }
}
