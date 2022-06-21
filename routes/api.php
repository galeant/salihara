<?php

use Illuminate\Http\Request;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'program'], function () {
    Route::get('/', 'ProgramController@index');
    Route::get('{id}', 'ProgramController@detail');

    // Route::post('{id}/ticket', 'ProgramController@detail');

    Route::post('create', 'ProgramController@create');
    Route::post('update/{id}', 'ProgramController@update');
    Route::post('delete/{id}', 'ProgramController@delete');
});
