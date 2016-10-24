<?php

use App\Http\Controllers\WeKastController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class Init extends Migration
{
    const TABLE_USERS = 'users';

    const TABLE_PRES = 'presentations';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create(self::TABLE_USERS, function(Blueprint $table)
        {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->string('login')->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->smallInteger('code')->nullable();
            $table->string('confirmed', 32)->nullable();
            $table->index('confirmed');
            $table->timestamps();
        });


        Storage::makeDirectory(WeKastController::PRESENTATIONS_PATH, 0777);
        $dir = 'storage/app/' . WeKastController::PRESENTATIONS_PATH;
        if (file_exists($dir)) {
            chmod($dir, 0777);
        }

        Schema::create(self::TABLE_PRES, function(Blueprint $table)
        {
            $table->engine = 'MyISAM';
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->foreign('user_id')->references('id')->on('user');
            $table->string('name');
            $table->string('hash', 32);
            $table->integer('size');
            $table->string('type');
            $table->unique(['user_id', 'name']);
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
        Schema::drop(self::TABLE_PRES);

        Schema::drop(self::TABLE_USERS);
    }
}
