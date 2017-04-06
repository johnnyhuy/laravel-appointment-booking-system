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
use App\WorkingTime;

// Use vendor classes
use Carbon\Carbon;
use Faker\Generator;

/**
 *
 * Generating dummy data for Employee
 *
 */
$factory->define(Employee::class, function (Generator $faker) {
    return [
        'title' => $faker->jobTitle,
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'phone' => $faker->phoneNumber,
    ];
});

/**
 *
 * Generating dummy data for working times
 *
 */
$factory->define(WorkingTime::class, function (Generator $faker) {
    $employee = factory(Employee::class)->create();

    return [
        'employee_id' => $employee->id,
        'start_time' => $faker->time,
        'end_time' => $faker->time,
        'date' => $faker->date,
    ];
});

/**
 *
 * Generating dummy data for Availability
 *
 */
$factory->define(Availability::class, function (Generator $faker) {
    return [
		'employee_id' => 1,
		'day' => 'Monday',
		'start_time' => '9:00',
		'end_time' => '18:30'
    ];
});

/**
 *
 * Generating dummy data for Customer
 *
 */
$factory->define(Customer::class, function (Generator $faker) {
    static $password;
    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'username' => str_replace(".", "", $faker->userName),
        'password' => $password ?: $password = bcrypt($faker->password),
        'phone' => $faker->phoneNumber,
        'address' => $faker->streetAddress,
        'phone' => $faker->phoneNumber,
        'created_at' => Carbon::now('Australia/Melbourne')->toDateTimeString(),
        'updated_at' => Carbon::now('Australia/Melbourne')->toDateTimeString(),
    ];
});

/**
 *
 * Generating dummy data for Business Owner
 *
 */
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

/**
 *
 * Generating dummy data for Booking
 *
 */
$factory->define(Booking::class, function (Generator $faker) {
    $customer = factory(Customer::class)->create();

    // Loop so that start time is always earlier than end time in hours
    while (true) {
        $startHour = rand(0, 24);
        $startMinute = rand(0, 1) == 1 ? 0 : 30;
        $endHour = rand(0, 24);
        $endMinute = rand(0, 1) == 1 ? 0 : 30;

        if ($startHour < $endHour) {
            break;
        }
    }

    // Give a random day
    $days = rand(0, 365);

     // Get the start of today
    $startTime = Carbon::now('Australia/Melbourne')->startOfDay();
    $endTime = Carbon::now('Australia/Melbourne')->startOfDay();
    $day = Carbon::now('Australia/Melbourne')->startOfDay();

    // Set future or past booking by one year
    if (rand(0, 1) == 1) {
        $day->subYears(1);
    }

    $day->addDays($days);

    // Add days and hours
    $startTime->addHours($startHour)
        ->addMinutes($startMinute);
    $endTime->addHours($endHour)
        ->addMinutes($endMinute);

    // Convert Carbon object to Time string
    $startTime = $startTime->toTimeString();
    $endTime = $endTime->toTimeString();

    // Convert day to Date string
    $day = $day->toDateString();

    return [
        'customer_id' => $customer->id,
        'start_time' => $startTime,
        'end_time' => $endTime,
        'date' => $day,
    ];
});