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

Route::post('/auth', function () {
    return "auth";
});

Route::post('/reset', function () {
    return "password";
});

Route::get('/list', function () {
    return "list";
});

Route::post('/upload', function () {
    return "upload";
});

Route::get('/download/{id}', function ($id) {
    return "download " . $id;
});
