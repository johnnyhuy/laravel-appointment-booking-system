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
            'business_name' => 'Haircut Business',
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
            'title' => 'Hairdresser'
        ]);

        // Create a working times
        for ($i = 0; $i < count($employees); $i++) {
            factory(WorkingTime::class)->create([
                'employee_id' => $employees[0]->id,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'date' => Carbon::now()->addDays($i)->toDateString()
            ]);

            factory(WorkingTime::class)->create([
                'employee_id' => $employees[0]->id,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'date' => Carbon::now()->addWeeks(2)->addDays($i)->toDateString()
            ]);

            factory(WorkingTime::class)->create([
                'employee_id' => $employees[$i]->id,
                'start_time' => '09:00',
                'end_time' => '17:00',
                'date' => Carbon::now()->addWeek()->addDays($i)->toDateString()
            ]);
        }

        // Haircut for 30 mins
        $activityOne = factory(Activity::class)->create([
            'name' => 'Haircut',
            'duration' => '00:30'
        ]);

        // Hair colouring for 2 hours
        $activityTwo = factory(Activity::class)->create([
            'name' => 'Hair Colouring',
            'duration' => '02:00'
        ]);

        // Booking tomorrow at 11:00 AM
        factory(Booking::class)->create([
            'customer_id' => $customers[0]->id,
            'employee_id' => $employees[0]->id,
            'activity_id' => $activityOne->id,
            'start_time' => '11:00',
            'date' => Carbon::now()->addDay()->toDateString()
        ]);

        factory(Booking::class)->create([
            'customer_id' => $customers[2]->id,
            'employee_id' => null,
            'activity_id' => $activityOne->id,
            'start_time' => '11:00',
            'date' => Carbon::now()->addDays(2)->toDateString()
        ]);

        factory(Booking::class)->create([
            'customer_id' => $customers[2]->id,
            'employee_id' => $employees[0]->id,
            'activity_id' => $activityOne->id,
            'start_time' => '12:00',
            'date' => Carbon::now()->subDays(2)->toDateString()
        ]);
        factory(Booking::class)->create([
            'customer_id' => $customers[1]->id,
            'employee_id' => $employees[1]->id,
            'activity_id' => $activityOne->id,
            'start_time' => '13:00',
            'date' => Carbon::now()->subDays(2)->toDateString()
        ]);
        factory(Booking::class)->create([
            'customer_id' => $customers[3]->id,
            'employee_id' => $employees[0]->id,
            'activity_id' => $activityTwo->id,
            'start_time' => '12:00',
            'date' => Carbon::now()->subDays(5)->toDateString()
        ]);
    }
}
