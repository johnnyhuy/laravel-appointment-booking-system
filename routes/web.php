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
Route::post('/bookings', 'BookingController@store');

// Bookings
Route::get('/bookings', 'BookingController@indexCustomer');
Route::get('/bookings/new', function() { return redirect('/bookings/' . toMonthYear(getNow()) . '/new'); });
Route::get('/bookings/{month_year}/new', 'BookingController@newCustomer');
Route::get('/bookings/{month_year}/new/{employee_id}', 'BookingController@showCustomer');


/**
 *
 * Admin handling
 *
 */

// Admin views
Route::get('/admin', 'BusinessOwnerController@index');
Route::get('/admin/register', 'BusinessOwnerController@register');
Route::get('/admin/summary', 'BookingController@summary');
Route::get('/admin/history', 'BookingController@history');

// Business Times
Route::resource('admin/times', 'BusinessTimeController');

// Employees
Route::get('/admin/employees', 'EmployeeController@index');
Route::get('/admin/employees/assign', 'EmployeeController@assign');
Route::get('/admin/employees/assign/{employee_id}', 'EmployeeController@assign');
Route::post('/admin/employees/assign', 'BookingController@assignEmployee');

// Roster
Route::get('/admin/roster', function() { return redirect('/admin/roster/' . toMonthYear(getNow())); });
Route::get('/admin/roster/{month_year}', 'WorkingTimeController@index');
Route::get('/admin/roster/{month_year}/{employee_id}', 'WorkingTimeController@show');
Route::get('/admin/roster/{month_year}/{employee_id}/{working_time_id}/edit', 'WorkingTimeController@edit');
Route::put('/admin/roster/{id}', 'WorkingTimeController@update');
Route::post('/admin/roster', 'WorkingTimeController@create');
Route::post('/admin/roster/{month_year}', 'WorkingTimeController@create');

// Booking
Route::get('/admin/bookings', function() { return redirect('/admin/bookings/' . toMonthYear(getNow())); });
Route::get('/admin/bookings/{month_year}', 'BookingController@indexAdmin');
Route::get('/admin/bookings/{month_year}/{employee_id}', 'BookingController@showAdmin');
Route::post('/admin/bookings/{month_year}', 'BookingController@store');
Route::post('/admin/bookings', 'BookingController@store');

// Admin form submission handling
Route::post('/admin/register', 'BusinessOwnerController@create');
Route::post('/admin/employees', 'EmployeeController@create');

// Activity management
// Custom modified resourceful controller using CRUD routes
Route::resource('admin/activity', 'ActivityController', [
	'except' => [
		'create'
	]
]);

Route::resource('admin/booking', 'BookingController', [
	'only' => [
        'edit', 'update', 'destroy'
	]
]);