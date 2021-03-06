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

Route::get('test', 'AuthController@test');
Route::post('login', 'AuthController@login');
Route::group(['middleware' => ['auth']], function () {
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

        Route::group(['prefix' => 'program'], function () {
            Route::get('/', 'ProgramController@index');
            Route::get('{id}', 'ProgramController@detail');

            // Route::post('{id}/ticket', 'ProgramController@detail');

            Route::post('create', 'ProgramController@create');
            Route::post('update/{id}', 'ProgramController@update');
            Route::post('delete/{id}', 'ProgramController@delete');
        });

        Route::group(['prefix' => 'ticket'], function () {
            Route::get('/', 'TicketController@index');
            Route::get('{id}', 'TicketController@detail');
            Route::post('create', 'TicketController@create');
            Route::post('update/{id}', 'TicketController@update');
            Route::post('delete/{id}', 'TicketController@delete');
        });

        Route::group(['prefix' => 'misc'], function () {
            Route::get('banner', 'MiscController@banner');
            Route::post('banner', 'MiscController@postBanner');

            Route::get('about', 'MiscController@about');
            Route::post('about', 'MiscController@postAbout');

            Route::group(['prefix' => 'faq'], function () {
                Route::get('/', 'MiscController@faqIndex');
                Route::get('{id}', 'MiscController@faqDetail');
                Route::post('create', 'MiscController@faqCreate');
                Route::post('update/{id}', 'MiscController@faqUpdate');
                Route::post('delete/{id}', 'MiscController@faqDelete');
            });

            Route::group(['prefix' => 'committee'], function () {
                Route::get('/', 'MiscController@committee');
                Route::post('/', 'MiscController@committeePost');
                // Route::get('/', 'MiscController@committeeIndex');
                // Route::get('{id}', 'MiscController@committeeDetail');
                // Route::post('create', 'MiscController@committeeCreate');
                // Route::post('update/{id}', 'MiscController@committeeUpdate');
                // Route::post('delete/{id}', 'MiscController@committeeDelete');
            });

            Route::group(['prefix' => 'partner'], function () {
                Route::get('{type}', 'MiscController@partner');
                Route::post('{type}', 'MiscController@partnerPost');
                // Route::post('create', 'MiscController@partnerCreate');
                // Route::post('update/{id}', 'MiscController@partnerUpdate');
                // Route::post('delete/{id}', 'MiscController@partnerDelete');
            });
        });

        Route::group(['prefix' => 'user'], function () {
            Route::get('/', 'UserController@index');
            Route::get('{id}', 'UserController@detail');
            Route::post('create', 'UserController@create');
            Route::post('update/{id}', 'UserController@update');
            Route::post('delete/{id}', 'UserController@delete');
            Route::post('block/{id}', 'UserController@block');
        });

        Route::group(['prefix' => 'customer'], function () {
            Route::get('/', 'CustomerController@index');
            Route::get('{id}', 'CustomerController@detail');
            Route::post('{id}/program_access', 'CustomerController@programAccess');
            Route::get('{id}/transaction', 'CustomerController@transaction');
            Route::post('block/{id}', 'CustomerController@block');
            Route::post('create', 'CustomerController@create');
        });

        Route::group(['prefix' => 'voucher'], function () {
            Route::get('/', 'VoucherController@index');
            Route::get('{id}', 'VoucherController@detail');
            Route::post('create', 'VoucherController@create');
            Route::post('update/{id}', 'VoucherController@update');
            Route::post('delete/{id}', 'VoucherController@delete');
        });
    });
    Route::group(['prefix' => 'transaction'], function () {
        Route::get('/', 'TransactionController@transaction');
        Route::get('{id}', 'TransactionController@transaction');
    });
});
