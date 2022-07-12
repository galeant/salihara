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
Route::get('activation/{token}', 'AuthController@activation')->name('activation');
Route::post('forgot_password', 'AuthController@forgetPassword');
Route::post('forgot_password_post', 'AuthController@postForgetPassword');

Route::post('login', 'AuthController@login');
Route::group(['middleware' => 'auth'], function () {
    Route::get('profile', 'AuthController@profile');
    Route::post('profile', 'AuthController@updateProfile');
    Route::post('logout', 'AuthController@logout');

    Route::get('cart', 'TransactionController@cart');
    Route::post('cart', 'TransactionController@addCart');
    Route::post('remove_cart', 'TransactionController@removeCart');

    Route::post('checkout', 'TransactionController@checkout');
    Route::post('check_voucher', 'TransactionController@checkVoucher');

    Route::get('transaction', 'TransactionController@transaction');
    Route::get('transaction/payment_method', 'TransactionController@paymentMethod');
});
