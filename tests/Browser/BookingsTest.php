<?php

namespace Tests\Browser;

use App\Customer;
use App\Booking;

use Tests\DuskTestCase;

class BookingsTest extends DuskTestCase
{
    /**
     * Test when customer is logged in and sees start time column
     *
     * @return void
     */
    public function testLoggedCustomerSeesStartTime()
    {
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::first())
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
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::first())
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
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::first())
                ->visit('/bookings')
                ->assertSee('Duration');
        });
    }
}