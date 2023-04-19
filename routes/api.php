<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// $routePrefix = config('app.site_sub_url') .config('app.api_prefix');

if (config('app.env') != 'prodaction') {
    Route::group(['middleware' => 'auth:sanctum'], function () {
        Route::get('/testapiroutewithauth', function () {
            return 'Example API route with Sanctum Auth';
        })->name('testapiroutewithauth');
    });

    Route::get('/testapiroutenoauth', function () {
        return 'Example API route without Sanctum Auth';
    })->name('testapiroutenoauth');
}
