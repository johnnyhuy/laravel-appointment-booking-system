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

class CustomerBookingsTest extends DuskTestCase
{
    /**
     * Test when customer is logged in and sees start time column
     *
     * @return void
     */
    public function testLoggedCustomerSeesStartTime()
    {
        // There exists a customer
        $customer = factory(Customer::class)->create();

        // Create 4 bookings
        $bookings = factory(Booking::class, 4)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('Start');
        });
    }

    /**
     * Test when customer is logged in and sees end time column
     *
     * @return void
     */
    public function testLoggedCustomerSeesEndTime()
    {
        // There exists a customer
        $customer = factory(Customer::class)->create();

        // Create 4 bookings
        $bookings = factory(Booking::class, 4)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('End');
        });
    }

    /**
     * Test when customer is logged in and sees duration column
     *
     * @return void
     */
    public function testLoggedCustomerSeesDuration()
    {
        // There exists a customer
        $customer = factory(Customer::class)->create();

        // Create 4 bookings
        $bookings = factory(Booking::class, 4)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('Duration');
        });
    }

    /**
     * When a customer creates a booking
     *
     * @return void
     */
    public function testCustomerCreateBookingSuccessful()
    {
        // There exists a customer
        $customer = factory(Customer::class)->create();

        // Create 4 bookings
        $bookings = factory(Booking::class, 4)->create();

        // Create an activity that is two hours long
        $bookings = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings/new')
                ->select('activity_id', 1)
                // Start time is at 08:00 AM
                ->keys('#input_start_time', '08:00')
                ->press('Add Booking')
                ->assertSee('Booking has successfully been created. No employee is assigned to your booking, please come back soon when an adminstrator verifies your booking.');
        });
    }
}