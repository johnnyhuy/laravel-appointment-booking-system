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
use App\BusinessOwner;

// Use vendor classes
use Carbon\Carbon;
use Faker\Generator;

$factory->define(Employee::class, function (Generator $faker) {
    return [
        'title' => "WHO EVEN CARES",
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'phone' => $faker->phoneNumber,
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
        'username' => str_replace(".", "", $faker->userName),
        'password' => $password ?: $password = bcrypt('secret'),
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'phone' => $faker->phoneNumber,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});

$factory->define(BusinessOwner::class, function (Generator $faker) {
    return [
		'business_name' => $faker->company,
		'owner_name' => $faker->name,
		'username' => str_replace(".", "", $faker->userName),
		'password' => $password = bcrypt($faker->password),
		'address' => $faker->streetAddress,
		'phone' => $faker->phoneNumber,
    ];
});

$factory->define(Booking::class, function (Generator $faker) {
    $customer = factory(Customer::class)->make();

    return [
        'customer_id' => $customer->id,
        'booking_start_time' => Carbon::now(),
        'booking_end_time' => Carbon::now(),
    ];
});