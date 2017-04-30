<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class BusinessOwnerAssignEmployeeTest extends DuskTestCase
{
    /**
     * Successfully assign an employee to a booking
     *
     * @return void
     */
    public function testAssignEmployeeToBookingSuccessful()
    {
        // Get the date
        $date = Carbon::now('Australia/Melbourne')->addWeek()->toDateString();

        // There exists a business owner, customer & employee
        $bo = factory(BusinessOwner::class)->create();
        $customer = factory(Customer::class)->create();
        $employee = factory(Employee::class)->create();

        // Set a working time from 09:00 AM to 05:00 PM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date
        ]);

        // Create 4 bookings
        // Next week
        $bookings = factory(Booking::class, 4)->create([
            'date' => $date,
            'employee_id' => null
        ]);

        // Create an activity that is two hours long
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        $this->browse(function ($browser) use ($bo, $customer, $employee) {
            $browser->loginAs($bo, 'web_admin')
                ->visit('/admin/employees/assign/' . $employee->id)
                ->check('bookings[]')
                // Start time is at 08:00 AM
                ->press('Assign Employee')
                ->assertSee('Booking(s) have been successfully assigned.');
        });
    }
}