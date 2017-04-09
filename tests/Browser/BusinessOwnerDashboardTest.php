<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Carbon\Carbon;

use App\Booking;
use App\BusinessOwner;

class BusinessOwnerDashboardTest extends DuskTestCase
{
	/**
     * Test if admin dashboard exists
     *
     * @return void
     */
    public function testDashboardExists()
    {
    	// Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
                // Visit /admin route
            	->visit('/admin')
                // Check if route is /admin
            	->assertPathIs('/admin')
                // See if business name exists on page (header title)
                ->assertSee($owner->business_name);
        });
    }

    /**
     * Test to visit roster page in dashboard
     *
     * @return void
     */
    public function testVisitRoster() {
    	// Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
            	->visit('/admin/roster')
            	->assertPathIs('/admin/roster')
                ->assertSee('Roster');
        });
    }

    /**
     * Test to visit employees page in dashboard
     *
     * @return void
     */
    public function testVisitEmployees() {
    	// Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
            	->visit('/admin/employees')
            	->assertPathIs('/admin/employees')
                ->assertSee('Employees');
        });
    }

    /**
     * Test to visit summary of bookings page in dashboard
     *
     * @return void
     */
    public function testVisitSummaryOfBookings() {
    	// Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
            	->visit('/admin/summary')
            	->assertPathIs('/admin/summary')
                ->assertSee('Summary of Bookings');
        });
    }

    /**
     * Test to visit business information page in dashboard
     *
     * @return void
     */
    public function testVisitBusinessInformation() {
        // Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
                ->visit('/admin')
                ->assertPathIs('/admin')
                ->assertSee('Business Information');
        });
    }

    /**
     * Show a message when there are no bookings at the History of Bookings page
     *
     * @return void
     */
    public function testNoBookingsMessageHistoryOfBookings() {
        // Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
            $browser->loginAs($owner, 'web_admin')
                ->visit('/admin')
                ->clickLink('History')
                ->assertPathIs('/admin/history')
                ->assertSee('No history of bookings found.')
                ->assertSee('Try add a booking before today.');
        });
    }

    /**
     * Show a message when there are no bookings at the History of Bookings page
     *
     * @return void
     */
    public function testShowHistoryOfBookings() {
        // Generate customer info
        $owner = factory(BusinessOwner::class)->create();

        // Generate a 2 hour booking a day before today
        $booking = factory(Booking::class)->create([
            'date' => Carbon::now()->subDays(1),
            'start_time' => Carbon::now()
                ->startOfDay()
                ->subDays(1)
                ->toDatetimeString(),
            'end_time' => Carbon::now()
                ->startOfDay()
                ->subDays(1)
                ->addHours(2)
                ->toDatetimeString(),
        ]);
        
        $this->browse(function ($browser) use ($owner, $booking) {
            $browser->loginAs($owner, 'web_admin')
                ->visit('/admin')
                ->clickLink('History')
                ->assertPathIs('/admin/history')
                // See booking ID
                ->assertSee($booking->id)
                // See customer full name
                ->assertSee($booking->customer->firstname . ' ' . $booking->customer->lastname);
        });
    }
}