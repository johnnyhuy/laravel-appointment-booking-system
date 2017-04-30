<?php

namespace Tests\Browser;

use App\Customer;
use App\Booking;

use Tests\DuskTestCase;

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
}