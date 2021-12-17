<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAggregatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aggregates', function (Blueprint $table) {
            $table->string('title');
            $table->string('excerpt');
            $table->string('siteName');
            $table->string('originalUrl');
            $table->string('dateOfAggregatePublication');
            $table->string('uniqueId');
            $table->string('image');
            $table->boolean('curated')->default(false);
            $table->boolean('archived')->default(false);
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->unsignedBigInteger('letterId')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique('uniqueId');

            $table->foreign('channelId')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');

            $table->foreign('letterId')
                ->references('id')
                ->on('letters')
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
        Schema::dropIfExists('aggregates');
    }
}
