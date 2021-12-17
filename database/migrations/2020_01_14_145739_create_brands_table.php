<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::disableForeignKeyConstraints();

        Schema::create('brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brandName');
            $table->string('brandSlug')->unique();
            $table->timestamps();
        });

        Schema::create('configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('configurationName');
            $table->string('configurationSlug')->unique();
            $table->timestamps();
        });

        Schema::create('features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('featureFlagName');
            $table->string('featureFlagSlug')->unique();
            $table->timestamps();
        });

        Schema::create('brand_configurations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('brandConfigurationValue');
            $table->unsignedBigInteger('brandId');
            $table->unsignedBigInteger('configurationId');
            $table->timestamps();

            $table->foreign('brandId')
                ->references('id')->
                on('brands')
                ->onDelete('cascade');

            $table->foreign('configurationId')
                ->references('id')
                ->on('configurations')
                ->onDelete('cascade');
        });

        Schema::create('brand_features', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('brandId');
            $table->unsignedBigInteger('featureId');
            $table->boolean('isFeatureEnabled')->default(false);
            $table->timestamps();

            $table->foreign('brandId')
                ->references('id')
                ->on('brands')
                ->onDelete('cascade');

            $table->foreign('featureId')
                ->references('id')
                ->on('features')
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
        Schema::dropIfExists('brand_configurations');
        Schema::dropIfExists('brand_features');
        Schema::dropIfExists('brands');
        Schema::dropIfExists('configurations');
        Schema::dropIfExists('features');
    }
}
