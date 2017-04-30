<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\Activity;
use App\Customer;
use App\BusinessOwner;
use App\Employee;
use App\Booking;
use App\WorkingTime;

use Carbon\Carbon;

class BusinessOwnerBookingTest extends DuskTestCase
{
	/**
     * Check if booking page exists
     *
     * @return void
     */
    public function testBookingPageExists()
    {
    	// Generate business owner
        $owner = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
                // Visit booking page
            	->visit('/admin/booking')
                // Check if route is /admin
            	->assertPathIs('/admin/booking')
                // See if business name exists on page (header title)
                ->assertSee($owner->business_name);
        });
    }

    /**
     * Add booking at at the admin booking page
     * Add a 2 hour booking and starts at 11:00 AM
     *
     * @return void
     */
    public function testAddBooking()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create existing data
        $customer = factory(Customer::class)->create();
        $employee = factory(Employee::class)->create();
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Set the date as tomorrow
        $date = Carbon::now('Australia/Melbourne')->addDay();

        // Create a working time where employee is working on the booking
        // Employee is working 09:00 AM to 05:00 PM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date->toDateString(),
        ]);

        $this->browse(function ($browser) use ($bo, $date, $customer, $employee, $activity) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Visit booking page
                ->visit('/admin/booking')
                ->select('customer_id', $customer->id)
                ->select('employee_id', $employee->id)
                ->select('activity_id', $activity->id)
                ->keys('#inputStartTime', '11:00')
                ->keys('#inputDate', $date->format('d/m/Y'))
                ->press('Add Booking')

                // Check success message
                ->assertSee('Booking has successfully been created.');
        });
    }
}