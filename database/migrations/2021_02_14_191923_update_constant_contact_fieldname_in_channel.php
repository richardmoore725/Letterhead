<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateConstantContactFieldnameInChannel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channels', function (Blueprint $table) {
            //
            $table->renameColumn('cc_access_token', 'ccAccessToken');
            $table->renameColumn('cc_refresh_token', 'ccRefreshToken');
            $table->renameColumn('cc_access_token_last_used', 'ccAccessTokenLastUsed');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channels', function (Blueprint $table) {
            //
        });
    }
}
