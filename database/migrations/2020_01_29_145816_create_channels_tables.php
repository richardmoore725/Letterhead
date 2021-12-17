<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChannelsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('channels', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->timestamps();
            $table->unsignedBigInteger('brandId');

            $table->foreign('brandId')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');
        });

        Schema::create('channel_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('channelConfigurationValue');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('configurationId');
            $table->timestamps();

            $table->foreign('channelId')
                ->references('id')->
                on('channels')
                ->onDelete('cascade');

            $table->foreign('configurationId')
                ->references('id')
                ->on('configurations')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('channels');
        Schema::dropIfExists('channel_configurations');
    }
}
