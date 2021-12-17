<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionalEmailTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactional_email', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('brandId');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('emailId');
            $table->unsignedBigInteger('eventId');
            $table->boolean('isActive');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('brandId')
            ->references('id')
            ->on('brands')
            ->onDelete('cascade');

            $table->foreign('channelId')
            ->references('id')
            ->on('channels')
            ->onDelete('cascade');

            $table->foreign('eventId')
            ->references('id')
            ->on('platform_events')
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
        Schema::dropIfExists('transactional_email');
    }
}
