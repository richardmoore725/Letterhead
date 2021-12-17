<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrandAndChannelContactInformationConfigurations extends Migration
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
                    'configurationName' => 'Brand Contact Name',
                    'configurationSlug' => 'brandContactName',
                ],
                [
                    'configurationName' => 'Brand Contact Email',
                    'configurationSlug' => 'brandContactEmail',
                ],
                [
                    'configurationName' => 'Brand Contact Phone',
                    'configurationSlug' => 'brandContactPhone',
                ],
                [
                    'configurationName' => 'Brand Contact Address: Street',
                    'configurationSlug' => 'brandContactAddress__street',
                ],
                [
                    'configurationName' => 'Brand Contact Address: City',
                    'configurationSlug' => 'brandContactAddress__city',
                ],
                [
                    'configurationName' => 'Brand Contact Address: State',
                    'configurationSlug' => 'brandContactAddress__state',
                ],
                [
                    'configurationName' => 'Brand Contact Address: Postal Code',
                    'configurationSlug' => 'brandContactAddress__postal',
                ],
                [
                    'configurationName' => 'Brand Website',
                    'configurationSlug' => 'brandUrl',
                ],

                [
                    'configurationName' => 'Channel Contact Name',
                    'configurationSlug' => 'channelContactName',
                ],
                [
                    'configurationName' => 'Channel Contact Email',
                    'configurationSlug' => 'channelContactEmail',
                ],
                [
                    'configurationName' => 'Channel Contact Phone',
                    'configurationSlug' => 'channelContactPhone',
                ],
                [
                    'configurationName' => 'Channel Contact Address: Street',
                    'configurationSlug' => 'channelContactAddress__street',
                ],
                [
                    'configurationName' => 'Channel Contact Address: City',
                    'configurationSlug' => 'channelContactAddress__city',
                ],
                [
                    'configurationName' => 'Channel Contact Address: State',
                    'configurationSlug' => 'channelContactAddress__state',
                ],
                [
                    'configurationName' => 'Channel Contact Address: Postal Code',
                    'configurationSlug' => 'channelContactAddress__postal',
                ],
                [
                    'configurationName' => 'Channel Website',
                    'configurationSlug' => 'channelUrl',
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
            ->where('configurationSlug', '=', 'brandContactName')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactEmail')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactPhone')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactAddress__street')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactAddress__city')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactAddress__state')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'brandContactAddress__postal')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactName')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactEmail')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactPhone')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactAddress__street')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactAddress__city')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactAddress__state')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelContactAddress__postal')
            ->delete();
    }
}
