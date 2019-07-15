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

Route::get('/', function () {
    return view('welcome');
});


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/verify-payment', 'PaymentController@verifyPayment');
Route::post('mpesa','PaymentController@checkout')->name('grace');
Route::post('confirm','PaymentController@getCallback');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
