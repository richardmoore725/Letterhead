<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeConfigurationValuesToLongTest extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brand_configurations', function (Blueprint $table) {
            $table->longText('brandConfigurationValue')->nullable()->change();
        });

        Schema::table('channel_configurations', function (Blueprint $table) {
            $table->longText('channelConfigurationValue')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel_configurations', function (Blueprint $table) {
            $table->string('channelConfigurationValue')->change();
        });

        Schema::table('brand_configurations', function (Blueprint $table) {
            $table->string('brandConfigurationValue')->change();
        });
    }
}
