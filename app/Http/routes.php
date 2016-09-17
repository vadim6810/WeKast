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
    $debug = env('APP_DEBUG', false);
    if ($debug) {
        return view('welcome');
    } else {
        return 'No data';
    }
});

Route::post('/register', "WeKastController@register");

Route::post('/list/{page?}', "WeKastController@presentationsList");

Route::post('/upload', "WeKastController@upload");

Route::post('/download/{id}', "WeKastController@download");

Route::post('/check', "WeKastController@check");

Route::get('/confirm/{hash}', "WeKastController@confirm");

Route::post('/code', "WeKastController@code");

Route::post('/request', "WeKastController@request");

Route::post('/reset', "WeKastController@reset");

Route::post('/delete/{id}', "WeKastController@remove");
