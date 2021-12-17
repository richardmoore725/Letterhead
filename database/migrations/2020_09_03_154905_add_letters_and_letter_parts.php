<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLettersAndLetterParts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->dateTimeTz('publicationDate');
            $table->unsignedBigInteger('status');
            $table->string('slug');
            $table->string('subtitle');
            $table->string('title');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

            $table->foreign('channelId')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');
        });

        Schema::create('letters_emails', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('letterId');
            $table->unsignedBigInteger('emailId');

            $table->foreign('letterId')
                ->references('id')
                ->on('letters')
                ->onDelete('cascade');
        });

        Schema::create('letters_users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('letterId');
            $table->unsignedBigInteger('userId');

            $table->foreign('letterId')
                ->references('id')
                ->on('letters')
                ->onDelete('cascade');
        });

        Schema::create('letter_parts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('copy');
            $table->string('heading');
            $table->unsignedBigInteger('letterId');
            $table->softDeletes('deleted_at', 0);
            $table->timestamps();

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
        Schema::dropIfExists('letter_parts');
        Schema::dropIfExists('letters_users');
        Schema::dropIfExists('letters_emails');
        Schema::dropIfExists('letters');
    }
}
