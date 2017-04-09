<?php

namespace Tests\Browser;

use App\BusinessOwner;

use Tests\DuskTestCase;

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
        	$firstOwner = BusinessOwner::first();
            $browser->loginAs($firstOwner, 'web_admin')
            	->visit('/admin')
            	->assertPathIs('/admin')
                ->assertSee($firstOwner->business_name);
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
        	$firstOwner = BusinessOwner::first();
            $browser->loginAs($firstOwner, 'web_admin')
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
        	$firstOwner = BusinessOwner::first();
            $browser->loginAs($firstOwner, 'web_admin')
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
    public function testVisitBookings() {
    	// Generate customer info
        $owner = factory(BusinessOwner::class)->create();
        
        $this->browse(function ($browser) use ($owner) {
        	$firstOwner = BusinessOwner::first();
            $browser->loginAs($firstOwner, 'web_admin')
            	->visit('/admin/summary')
            	->assertPathIs('/admin/summary')
                ->assertSee('Summary of Bookings');
        });
    }
}