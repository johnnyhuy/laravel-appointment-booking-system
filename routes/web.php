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

Route::get('/', 'Auth\LoginController@index');
Route::get('/login', 'Auth\LoginController@index');
Route::get('/register', 'Auth\RegisterController@index');
Route::get('/bookings', 'BookingController@index');
Route::get('/logout', 'Auth\LoginController@destroy');

Route::post('/login', 'Auth\LoginController@create');
Route::post('/register', 'Auth\RegisterController@create');

