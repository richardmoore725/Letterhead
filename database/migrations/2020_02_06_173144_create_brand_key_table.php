<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandKeyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brand_keys', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brandId');
            $table->string('key', 64)->unique();
            $table->boolean('isActive')->default(true);
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('brand_keys');
    }
}
