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
Route::group(['namespace' => 'Customer'], function () {

    Route::group(['prefix' => 'program'], function () {
        Route::get('{type}', 'ProgramController@index');
        Route::get('/detail/{slug}', 'ProgramController@detail');
    });

    Route::group(['prefix' => 'ticket'], function () {
        Route::get('{type}', 'TicketController@index');
        Route::get('/detail/{slug}', 'TicketController@detail');
    });

    Route::group(['prefix' => 'penampil'], function () {
        //     Route::get('{program_id}', 'ProgramController@index');
        Route::get('/detail/{slug}', 'PenampilController@detail');
    });
    Route::get('/banner', 'MiscController@banner');
    Route::get('/about', 'MiscController@about');


    Route::post('recieve_payment_test', 'MiscController@paymentTest');
    Route::post('redirect_payment_test', 'MiscController@paymentRedirect');
});
