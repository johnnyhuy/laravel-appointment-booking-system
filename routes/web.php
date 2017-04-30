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
Route::post('/bookings', 'BookingController@storeCustomerBooking');

// Bookings
Route::get('/bookings', 'BookingController@indexCustomer');
Route::get('/bookings/new', 'BookingController@newCustomerBooking');



// Admin handling

// Admin views
Route::get('/admin', 'BusinessOwnerController@index');
Route::get('/admin/register', 'BusinessOwnerController@register');
Route::get('/admin/summary', 'BusinessOwnerController@summary');
Route::get('/admin/history', 'BookingController@history');

// Employees
Route::get('/admin/employees', 'EmployeeController@index');
Route::get('/admin/employees/assign', 'EmployeeController@assign');
Route::get('/admin/employees/assign/{employee_id}', 'EmployeeController@assign');
Route::post('/admin/employees/assign', 'BookingController@assignEmployee');

// Roster
Route::get('/admin/roster', function() {
	return redirect('/admin/roster/' . Carbon\Carbon::now()->format('m-Y'));
});
Route::get('/admin/roster/{monthYear}', 'WorkingTimeController@index');
Route::get('/admin/roster/{id}/edit', 'WorkingTimeController@edit');
Route::put('/admin/roster/{id}', 'WorkingTimeController@update');
Route::post('/admin/roster', 'WorkingTimeController@create');
Route::post('/admin/roster/{monthYear}', 'WorkingTimeController@create');

// Booking
Route::get('/admin/booking', function() {
    return redirect('/admin/booking/' . Carbon\Carbon::now()->format('m-Y'));
});
Route::get('/admin/booking/{monthYear}', 'BookingController@indexAdmin');
Route::post('/admin/booking/{monthYear}', 'BookingController@storeAdminBooking');
Route::post('/admin/booking', 'BookingController@storeAdminBooking');

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
