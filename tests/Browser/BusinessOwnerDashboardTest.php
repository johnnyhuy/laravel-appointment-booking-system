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
        	// dd($firstOwner);
            $browser->loginAs($firstOwner, 'web_admin')
            	->visit('/admin')
                ->assertSee($firstOwner->business_name);
        });
    }
}