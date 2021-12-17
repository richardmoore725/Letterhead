<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('brandId');
            $table->longtext('content');
            $table->string('subject');
            $table->string('fromEmail');
            $table->string('fromName');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('channelId')
            ->references('id')
            ->on('channels')
            ->onDelete('cascade');

            $table->foreign('brandId')
            ->references('id')
            ->on('brands')
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
        Schema::dropIfExists('emails');
    }
}
