<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublishIntentToLettersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("ALTER TABLE letters ADD    COLUMN publishIntent varchar(100) NULL AFTER status");
        DB::statement("UPDATE letters SET publishIntent = 'email'");
        DB::statement("ALTER TABLE letters MODIFY COLUMN publishIntent varchar(100) NOT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('letters', function (Blueprint $table) {
            $table->dropColumn('publishIntent');
        });
    }
}
