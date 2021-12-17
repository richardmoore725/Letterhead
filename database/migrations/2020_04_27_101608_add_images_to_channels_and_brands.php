<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImagesToChannelsAndBrands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->string('brandSquareLogo')->default('');
            $table->string('brandHorizontalLogo')->default('');
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->string('channelHorizontalLogo')->default('');
            $table->string('channelSquareLogo')->default('');
        });

        app('db')
            ->table('configurations')
            ->insertOrIgnore([
                [
                    'configurationName' => 'Channel Storefront Hero',
                    'configurationSlug' => 'channelStorefrontImageHero',
                ],
                [
                    'configurationName' => 'Channel Storefront Content Screenshot',
                    'configurationSlug' => 'channelStorefrontImageContentScreenshot',
                ],
            ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('brands', function (Blueprint $table) {
            $table->dropColumn('brandSquareLogo');
            $table->dropColumn('brandHorizontalLogo');
        });

        Schema::table('channels', function (Blueprint $table) {
            $table->dropColumn('channelSquareLogo');
            $table->dropColumn('channelHorizontalLogo');
        });

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelStorefrontImageHero')
            ->delete();

        app('db')
            ->table('configurations')
            ->where('configurationSlug', '=', 'channelStorefrontImageContentScreenshot')
            ->delete();
    }
}
