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


Route::post('register', 'AuthController@register');
Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth'], function () {
    Route::get('profile', 'AuthController@profile');
    Route::post('profile', 'AuthController@updateProfile');
    Route::post('logout', 'AuthController@logout');

    Route::get('cart', 'Customer\MiscController@cart');
    Route::post('cart', 'Customer\MiscController@addCart');

    Route::post('checkout', 'Customer\MiscController@checkout');
});
