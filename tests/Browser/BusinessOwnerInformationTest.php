<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Contracts\Auth\Authenticatable;

use App\BusinessOwner;
use App\Customer;

class BusinessOwnerInformationTest extends DuskTestCase
{
    /**
     * Test whether the Business Owner information page exists
     *
     * @return void
     */
    public function testInformationPageExists() 
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin')
                ->assertPathIs('/admin')
                ->assertSee('Business Information');
        });
    }

    /**
     * Test that a guest can't access this page
     *
     * @return void
     */
    public function testPageNotAccessableAsGuest()
    {

        $this->browse(function ($browser) {
            //Go to summary page (default directory of /admin)
            $browser->visit('/admin')
                ->assertPathIs('/login');
        });
    }

    /**
     * Test that a customer can't access this page
     *
     * @return void
     */
    public function testPageNotAccessableAsCustomer()
    {
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            //Login as customer
            $browser->loginAs($customer)
                //Go to summary page (default directory of /admin)
                ->visit('/admin')
                ->assertPathIs('/bookings');
        });


    }

    /**
     * Test whether the correct information is displayed in the summary fields
     *
     * @return void
     */
    public function testCorrectInformationDisplayed()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin')
                //Check for full name on page
                ->assertSee($bo->first_name . " " . $bo->last_name)
                //Check for Business name on page
                ->assertSee($bo->business_name)
                //Check for Phone Number on page
                ->assertSee($bo->phone)
                //Check for address on the page
                ->assertSee($bo->address);
        });
    }
}
