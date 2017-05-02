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
use App\Activity;

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
 * Generating dummy data for Activity
 *
 */
$factory->define(Activity::class, function (Generator $faker) {
    // Set time variables for duration
    $hour = rand(2, 4);
    $minute = rand(0, 1) == 1 ? 0 : 30;

    // Set duration
    $duration = Carbon::createFromTime($hour, $minute)
        ->format('H:i');

    return [
        'name' => $faker->word,
        'description' => $faker->sentence,
        'duration' => $duration,
    ];
});

/**
 *
 * Generating dummy data for working times
 *
 */
$factory->define(WorkingTime::class, function (Generator $faker) {
    return [
        'employee_id' => factory(Employee::class)->create()->id,
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
    while (true) {
        // Replace '.' to 'a' character in default faker username
        $username = str_replace(".", "a", $faker->userName);

        // Generate usernames that are 6 or more characters long
        if (strlen($username) >= 6) {
            break;
        }
    }

    return [
        'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
        'username' => $username,
        'password' => bcrypt($faker->password),
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
    while (true) {
        // Replace '.' to 'a' character in default faker username
        $username = str_replace(".", "a", $faker->userName);

        // Generate usernames that are 6 or more characters long
        if (strlen($username) >= 6) {
            break;
        }
    }

    return [
		'business_name' => $faker->company,
		'firstname' => $faker->firstName,
        'lastname' => $faker->lastName,
		'username' => $username,
		'password' => bcrypt($faker->password),
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
     // Get time now
    $now = Carbon::now('Australia/Melbourne');

    // Give a random day
    $days = rand(0, 365);

    // Set future or past booking by one year
    if (rand(0, 1) == 1) {
        $now->subYears(1);
    }

    // Add a random amount of days
    $now->addDays($days);

    // Convert day to Date string
    $date = $now->toDateString();

    return [
        'customer_id' => factory(Customer::class)->create()->id,
        'employee_id' => factory(Employee::class)->create()->id,
        'activity_id' => factory(Activity::class)->create()->id,
        'start_time' => '10:00',
        'end_time' => '13:00',
        'date' => $date,
    ];
});