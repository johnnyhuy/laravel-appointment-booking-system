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

/**
 *
 * Session handling
 *
 */

// GET
Route::get('/login', 'Auth\SessionController@index')->name('login');
Route::get('/logout', 'Auth\SessionController@logout');
Route::get('/register', 'CustomerController@register');

// POST
Route::post('/login', 'Auth\SessionController@login');
Route::post('/register', 'CustomerController@create');
Route::post('/create_booking', 'BookingController@createBooking');

// Bookings
Route::get('/bookings', 'BookingController@customerBookings');
Route::get('/create_booking', 'BookingController@showCreateBooking');


/**
 *
 * Admin handling
 *
 */

// Dashboard views
Route::get('/admin', 'BusinessOwnerController@index');
Route::get('/admin/register', 'BusinessOwnerController@register');
Route::get('/admin/summary', 'BusinessOwnerController@summary');
Route::get('/admin/employees', 'EmployeeController@index');
Route::get('/admin/history', 'BookingController@history');
Route::get('/admin/roster', function() {
	return redirect('/admin/roster/' . Carbon\Carbon::now()->format('m-Y'));
});
Route::get('/admin/roster/{monthYear}', 'WorkingTimeController@index');
Route::get('/admin/booking', 'BookingController@index');

// Admin form submission handling
Route::post('/admin/register', 'BusinessOwnerController@create');
Route::post('/admin/employees', 'EmployeeController@create');
Route::post('/admin/roster', 'WorkingTimeController@create');
Route::post('/admin/roster/{monthYear}', 'WorkingTimeController@create');

// Activity management
// Custom modified resourceful controller using CRUD routes
Route::resource('admin/activity', 'ActivityController', [
	'except' => [
		'create'
	]
]);

Route::resource('admin/booking', 'BookingController', [
	'only' => [
		'store', 'edit', 'update', 'destroy'
	]
]);