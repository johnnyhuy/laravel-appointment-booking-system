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

use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Customer::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name' => $faker->name,
        'username' => $faker->userName,
        'password' => $password ?: $password = bcrypt('secret'),
        'address' => $faker->address,
        'phone' => $faker->phoneNumber,
        'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
        'updated_at' => Carbon::now()->format('Y-m-d H:i:s'),
    ];
});
