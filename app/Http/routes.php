<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/


Route::get('/', function () {
    return view('welcome');
});

Route::post('/register', "WeKastController@register");

Route::post('/list', "WeKastController@list");

Route::post('/upload', "WeKastController@upload");

Route::post('/download/{id}', "WeKastController@download");

Route::post('/reset', "WeKastController@password");
