<?php

use Illuminate\Database\Seeder;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create basic seeds
        factory(Activity::class, 10)->create();
        factory(Booking::class, 3)->create();
        factory(Employee::class, 5)->create();
    }
}
