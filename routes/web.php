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

/**
 * Session handling
 */

Route::get('/login', 'Auth\SessionController@index')->name('login');
Route::post('/login', 'Auth\SessionController@login');
Route::get('/logout', 'Auth\SessionController@logout');
Route::get('/register', 'CustomerController@register');
Route::post('/register', 'CustomerController@create');


/**
 * Admin handling
 */

// Admin views
Route::get('/admin', 'BusinessOwnerController@index');
Route::get('/admin/register', 'BusinessOwnerController@register');

// Business Info
Route::get('/admin/edit', 'BusinessOwnerController@edit');
Route::post('/admin/edit', 'BusinessOwnerController@update');

// Business Times
Route::resource('admin/times', 'BusinessTimeController', [
    'except' => [
        'create'
    ]
]);

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
Route::put('/admin/roster/{wTime}', 'WorkingTimeController@update');
Route::post('/admin/roster', 'WorkingTimeController@store');
Route::post('/admin/roster/{month_year}', 'WorkingTimeController@store');
Route::delete('/admin/roster/{wTime}', 'WorkingTimeController@destroy');

// Booking
Route::get('/admin/summary', 'BookingController@summary');
Route::get('/admin/history', 'BookingController@history');
Route::get('/admin/bookings', function() { return redirect('/admin/bookings/' . toMonthYear(getNow())); });
Route::get('/admin/bookings/{month_year}', 'BookingController@indexAdmin');
Route::get('/admin/bookings/{month_year}/{employee_id}', 'BookingController@showAdmin');
Route::post('/admin/bookings/{month_year}', 'BookingController@store');
Route::post('/admin/bookings', 'BookingController@store');

// Employee
Route::post('/admin/employees', 'EmployeeController@store');

// Business registration for
Route::post('/admin/register', 'BusinessOwnerController@store');

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

/**
 * Customer handling
 */

// Booking
Route::get('/bookings', 'BookingController@indexCustomer');
Route::get('/bookings/new', function() { return redirect('/bookings/' . toMonthYear(getNow()) . '/new'); });
Route::get('/bookings/{month_year}/new', 'BookingController@createCustomer');
Route::get('/bookings/{month_year}/new/{employee}', 'BookingController@createCustomer');
Route::post('/bookings', 'BookingController@store');