<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstantContactIntegrationFieldToConfigurations extends Migration
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
                    'configurationName' => 'Constant Contact Integration',
                    'configurationSlug' => 'ccIntegration',
                    'dataType'          => 'boolean'
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
            ->where('configurationSlug', '=', 'ccIntegration')
            ->delete();
    }
}
