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
        factory(BusinessOwner::class)->create([
            'business_name' => 'John\'s Car Service',
            'firstname' => 'John',
            'lastname' => 'Doe',
            'username' => 'john123',
            'password' => bcrypt('Password123'),
            'address' => '1 Swan Street',
            'phone' => '8382032932'
        ]);

        // 4 customers
        $customers = factory(Customer::class, 4)->create();

        // Employees
        $employees = factory(Employee::class, 5)->create([
            'title' => 'Mechanic'
        ]);

        factory(Employee::class)->create([
            'title' => 'Engineer'
        ]);

        factory(Employee::class)->create([
            'title' => 'Reception'
        ]);

        // Create a working timestamps
        for ($i = 0; $i < 5; $i++) {
            WorkingTime::create([
                'employee_id' => $employees[2]->id,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->endOfMonth()->subDays($i)->toDateString()
            ]);

            WorkingTime::create([
                'employee_id' => $employees[1]->id,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->startOfMonth()->addDays($i)->toDateString()
            ]);

            WorkingTime::create([
                'employee_id' => $employees[2]->id,
                'start_time' => '10:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->startOfMonth()->addDays($i)->toDateString()
            ]);

            WorkingTime::create([
                'employee_id' => $employees[0]->id,
                'start_time' => '13:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->startOfMonth()->addDays($i)->toDateString()
            ]);

            WorkingTime::create([
                'employee_id' => $employees[0]->id,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->startOfMonth()->addWeeks(2)->addDays($i)->toDateString()
            ]);

            WorkingTime::create([
                'employee_id' => $employees[2]->id,
                'start_time' => '09:00:00',
                'end_time' => '17:00:00',
                'date' => Carbon::now()->startOfMonth()->addWeek(3)->addDays($i)->toDateString()
            ]);
        }

        // Activities
        $activityOne = factory(Activity::class)->create([
            'name' => 'Oil Service',
            'duration' => '00:30'
        ]);
        $activityTwo = factory(Activity::class)->create([
            'name' => 'Smash Repair',
            'duration' => '06:00'
        ]);
        $activityThree = factory(Activity::class)->create([
            'name' => 'Full Service',
            'duration' => '08:00'
        ]);
        $activityFour = factory(Activity::class)->create([
            'name' => 'Car Radio Installation',
            'duration' => '03:00'
        ]);

        // Create Booking
        Booking::create([
            'customer_id' => $customers[0]->id,
            'employee_id' => $employees[0]->id,
            'activity_id' => $activityOne->id,
            'start_time' => toTime('11:00'),
            'end_time' => Booking::calcEndTime($activityOne->duration, '11:00'),
            'date' => Carbon::now()->addDay()->toDateString()
        ]);

        Booking::create([
            'customer_id' => $customers[2]->id,
            'employee_id' => $employees[1]->id,
            'activity_id' => $activityOne->id,
            'start_time' => toTime('11:30'),
            'end_time' => Booking::calcEndTime($activityOne->duration, '11:30'),
            'date' => Carbon::now()->addDays(2)->toDateString()
        ]);

        Booking::create([
            'customer_id' => $customers[2]->id,
            'employee_id' => $employees[3]->id,
            'activity_id' => $activityOne->id,
            'start_time' => toTime('10:30'),
            'end_time' => Booking::calcEndTime($activityOne->duration, '10:30'),
            'date' => Carbon::now()->subDays(2)->toDateString()
        ]);

        Booking::create([
            'customer_id' => $customers[3]->id,
            'employee_id' => $employees[1]->id,
            'activity_id' => $activityTwo->id,
            'start_time' => toTime('12:30'),
            'end_time' => Booking::calcEndTime($activityTwo->duration, '12:30'),
            'date' => Carbon::now()->subDays(2)->toDateString()
        ]);

        Booking::create([
            'customer_id' => $customers[3]->id,
            'employee_id' => $employees[0]->id,
            'activity_id' => $activityTwo->id,
            'start_time' => toTime('09:30'),
            'end_time' => Booking::calcEndTime($activityTwo->duration, '09:30'),
            'date' => Carbon::now()->startOfMonth()->toDateString()
        ]);

        Booking::create([
            'customer_id' => $customers[3]->id,
            'employee_id' => $employees[3]->id,
            'activity_id' => $activityTwo->id,
            'start_time' => toTime('12:30'),
            'end_time' => Booking::calcEndTime($activityTwo->duration, '12:30'),
            'date' => Carbon::now()->endOfMonth()->toDateString()
        ]);
    }
}
