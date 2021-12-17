<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicationInformationToChannelConfiguration extends Migration
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
                    'configurationName' => 'Publication schedule',
                    'configurationSlug' => 'publicationScheduleDaily'
                ],
                [
                    'configurationName' => 'Holidays',
                    'configurationSlug' => 'publicationHolidays',
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
            ->where('configurationSlug', '=', 'publicationScheduleDaily')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'publicationHolidays')
            ->delete();

    }
}
