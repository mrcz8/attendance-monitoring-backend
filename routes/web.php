<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::prefix('api')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('auth')->group(function() {
            Route::post('login', 'App\Http\Controllers\AuthController@authenticate');
            Route::post('logout', 'App\Http\Controllers\AuthController@logout');
        });
    });
});