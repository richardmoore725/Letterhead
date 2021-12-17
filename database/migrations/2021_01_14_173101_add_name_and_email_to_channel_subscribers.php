<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNameAndEmailToChannelSubscribers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('channel_subscribers', function (Blueprint $table) {
            $table->string('name')->default('');
            $table->string('email')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('channel_subscribers', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('email');
        });
    }
}
