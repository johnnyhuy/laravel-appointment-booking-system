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

Route::get('/', function() {
	return redirect('/login');
});

// Customer handling

// Session handling
// GET
Route::get('/login', 'Auth\SessionController@index')->name('login');
Route::get('/logout', 'Auth\SessionController@logout');
Route::get('/register', 'CustomerController@register');

// POST
Route::post('/login', 'Auth\SessionController@login');
Route::post('/register', 'CustomerController@create');

// Bookings
Route::get('/bookings', 'BookingController@index');


// Admin handling

// Dashboard views
Route::get('/admin', 'AdminController@index');
Route::get('/admin/register', 'BusinessOwnerController@register');
Route::get('/admin/summary', 'AdminController@summary');
Route::get('/admin/employees', 'AdminController@employees');
Route::get('/admin/history', 'AdminController@history');
Route::get('/admin/roster', 'AdminController@roster');

// Admin form submission handling
Route::post('/admin/register', 'BusinessOwnerController@create');
Route::post('/admin/employees', 'EmployeeController@create');
Route::post('/admin/roster', 'WorkingTimeController@create');