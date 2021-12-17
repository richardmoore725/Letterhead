<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAudienceBasedChannelConfigurations extends Migration
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
                    'configurationName' => 'Target Demographic',
                    'configurationSlug' => 'targetDemographic'
                ],
                [
                    'configurationName' => 'Publication Frequency',
                    'configurationSlug' => 'publicationFrequency'
                ], 
                [
                    'configurationName' => 'Total Subscribers',
                    'configurationSlug' => 'totalSubscribers'
                ],
                [
                    'configurationName' => 'Open Rate',
                    'configurationSlug' => 'openRate'
                ],
                [
                    'configurationName' => 'Click-through Rate',
                    'configurationSlug' => 'clickthroughRate'
                ],                
                [
                    'configurationName' => 'Average Daily Reads',
                    'configurationSlug' => 'averageDailyReads'
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
        ->where('configurationSlug', '=', 'targetDemographic')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'publicationFrequency')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'totalSubscribers')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'openRate')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'click-throughRate')
        ->delete();

        app('db')
        ->table('configurations')
        ->where('configurationSlug', '=', 'averageDailyReads')
        ->delete();
    }
}
