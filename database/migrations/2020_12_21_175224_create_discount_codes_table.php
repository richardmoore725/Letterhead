<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountCodesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_codes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('channelId');
            $table->string('discountCode');
            $table->unsignedBigInteger('discountValue');
            $table->string('displayName');
            $table->boolean('isActive');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('channelId')
            ->references('id')
            ->on('channels')
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
        Schema::dropIfExists('discount_codes');
    }
}
