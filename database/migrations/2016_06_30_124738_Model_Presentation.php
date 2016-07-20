<?php

use App\Http\Controllers\WeKastController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;

class ModelPresentation extends Migration
{
    const TABLE_NAME = 'presentations';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Storage::makeDirectory(WeKastController::PRESENTATIONS_PATH, 0777);
        $dir = 'storage/app/' . WeKastController::PRESENTATIONS_PATH;
        if (file_exists($dir)) {
            chmod($dir, 0777);
        }

        Schema::create(self::TABLE_NAME, function(Blueprint $table)
        {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('user');
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Storage::deleteDirectory(WeKastController::PRESENTATIONS_PATH);
        Schema::drop(self::TABLE_NAME);
    }
}
