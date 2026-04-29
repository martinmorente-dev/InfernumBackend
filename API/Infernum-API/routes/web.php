<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin', 'namespace' => 'App\Http\Controllers'], function () {

    Route::get('/dashboard', function ()
    {
        return view('welcome');
    })->middleware(['auth:sanctum' ,'refresh.token', 'abilities:admin']);


});
