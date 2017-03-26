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

//Customer handling
Route::get('/', 'Auth\LoginController@index');
Route::get('/login', 'Auth\LoginController@index');
Route::get('/bookings', 'CustomerController@index');
Route::get('/register', 'CustomerController@register')->middleware('guest');
Route::get('/logout', 'Auth\LoginController@logout');
//Customer form submission handling
Route::post('/login', 'Auth\LoginController@login');
Route::post('/register', 'CustomerController@create');

//Admin handling
Route::get('/admin', 'BusinessOwnerController@index');
//Admin form submission handling
Route::post('/admin/register', 'BusinessOwnerController@create');