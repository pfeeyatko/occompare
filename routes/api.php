<?php

use App\Http\Middleware\ResponseCache;

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

Route::middleware(['api'])->group(function () {
    Route::get('/occupations', 'OccupationsController@index');
    Route::post('/compare', 'OccupationsController@compare')->middleware(ResponseCache::class);
});
