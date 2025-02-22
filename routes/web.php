<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

if (config('app.env') != 'prodaction') {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/testroutewhithauth', function () {
            return 'Example WEB route with Sanctum Auth';
        })->name('testwebroutewithauth');
    });

    Route::get('/testroutenoauth', function () {
        return 'Example WEB route without Sanctum Auth test';
    })->name('testwebroutenoauth');
}
