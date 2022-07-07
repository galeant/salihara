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
Route::get('404', function () {
    abort(404);
});
Route::get('bebek', function () {
    $user = [
        'name' => 'kadal'
    ];
    return view('email.register', [
        'url' => 'url',
        'user' => (object)$user
    ]);
});
Route::get('/', function () {
    header('Location: https://salihara.org/');
});
Route::get('loc', 'AuthController@loc');

// Route::get('/storage_link', function () {
//     Artisan::call('storage:link');
// });
