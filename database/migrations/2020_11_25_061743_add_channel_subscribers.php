<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddChannelSubscribers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('channel_subscribers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('userId');
            $table->softDeletes('deletedAt', 0);
            $table->timestamps();

            $table->foreign('channelId')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');
        });

        Schema::create('channel_subscription_tiers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->softDeletes('deletedAt', 0);
            $table->string('title')->default('');
            $table->timestamps();

            $table->foreign('channelId')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');
        });

        Schema::create('channel_subscriptions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('channelSubscriberId');
            $table->softDeletes('deletedAt', 0);
            $table->unsignedBigInteger('status')->default(0);
            $table->unsignedBigInteger('tier')->default(0);
            $table->timestamps();

            $table->foreign('channelId')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');

            $table->foreign('tier')
                ->references('id')
                ->on('channel_subscription_tiers')
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
        Schema::dropIfExists('channel_subscriptions');
        Schema::dropIfExists('channel_subscription_tiers');
        Schema::dropIfExists('channel_subscribers');
    }
}
