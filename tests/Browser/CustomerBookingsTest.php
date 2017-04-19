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
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('Start Time');
        });
    }

    /**
     * Test when customer is logged in and sees end time column
     *
     * @return void
     */
    public function testLoggedCustomerSeesEndTime()
    {
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('End Time');
        });
    }

    /**
     * Test when customer is logged in and sees duration column
     *
     * @return void
     */
    public function testLoggedCustomerSeesDuration()
    {
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/bookings')
                ->assertSee('Duration');
        });
    }
}