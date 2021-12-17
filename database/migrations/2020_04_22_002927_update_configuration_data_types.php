<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConfigurationDataTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'publicationScheduleDaily']
            ])
            ->update([
                'dataType' => 'array'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'publicationHolidays']
            ])
            ->update([
                'dataType' => 'array'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'advertisingRevenueShare']
            ])
            ->update([
                'dataType' => 'float'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'clickThroughRate']
            ])
            ->update([
                'dataType' => 'float'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'targetDemographic']
            ])
            ->update([
                'dataType' => 'array'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'totalSubscribers']
            ])
            ->update([
                'dataType' => 'integer'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'openRate']
            ])
            ->update([
                'dataType' => 'float'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'averageDailyReads']
            ])
            ->update([
                'dataType' => 'integer'
            ]);

        app('db')->table('configurations')
            ->where([
                ['configurationSlug', 'adSchedulingBuffer']
            ])
            ->update([
                'dataType' => 'integer'
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
