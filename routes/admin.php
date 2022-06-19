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

Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth'], function () {
    Route::get('profile', 'AuthController@profile');
    Route::post('logout', 'AuthController@logout');

    Route::group(['namespace' => 'Admin'], function () {

        Route::group(['prefix' => 'penampil'], function () {
            Route::get('/', 'PenampilController@index');
            Route::get('{id}', 'PenampilController@detail');
            Route::post('create', 'PenampilController@create');
            Route::post('update/{id}', 'PenampilController@update');
            Route::post('delete/{id}', 'PenampilController@delete');
        });
    });
});
