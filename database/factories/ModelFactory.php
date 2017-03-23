<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

// Use app classes
use App\Booking;
use App\Customer;
use App\Employee;
use App\Availability;

// Use vendor classes
use Carbon\Carbon;
use Faker\Generator;

$factory->define(Booking::class, function (Generator $faker) {
    return [
        'customer_id' => $faker->numberBetween(1,10),
        'booking_start_time' => Carbon::now(),
        'booking_end_time' => Carbon::now(),
    ];
});

$factory->define(Employee::class, function (Generator $faker) {
    return [
        'name' => $faker->name
    ];
});

$factory->define(Availability::class, function (Generator $faker) {
    return [
		'employee_id' => 1,
		'day' => 'Monday',
		'start_time' => '9:00',
		'end_time' => '18:30'
    ];
});

$factory->define(Customer::class, function (Generator $faker) {
    static $password;
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'username' => $faker->username,
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});

$factory->define(BusinessOwner::class, function (Generator $faker) {
    return [
		'business_name' => $faker->company,
		'owner_name' => $faker->name,
		'username' => $faker->userName,
		'password' => $password = bcrypt($faker->password),
		'address' => $faker->address,
		'phone' => $faker->phoneNumber,
    ];
});