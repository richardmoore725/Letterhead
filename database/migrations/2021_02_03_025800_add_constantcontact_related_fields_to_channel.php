<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddConstantcontactRelatedFieldsToChannel extends Migration
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
            $table->string('cc_access_token')->default('');
            $table->string('cc_refresh_token')->default('');
            $table->timestamp('cc_access_token_last_used')->nullable()->default(null);
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
            $table->dropColumn('cc_access_token');
            $table->dropColumn('cc_refresh_token');
            $table->dropColumn('cc_access_token_last_used');
        });
    }
}
