<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSMSCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table(ModelUser::TABLE_NAME, function ($table) {
            $table->smallInteger('code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(ModelUser::TABLE_NAME, function ($table) {
            $table->dropColumn('code');
        });
    }
}
