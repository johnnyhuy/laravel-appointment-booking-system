<?php

namespace Tests\Browser;

use App\Customer;
use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CustomerRegisterTest extends DuskTestCase
{
    /**
     * Test registering button to exist at the homepage
     *
     * @return void
     */
    public function testRegisterButtonShown()
    {
        $this->browse(function ($browser) {
            $browser->visit('/')
                    ->assertSee('Register');
        });
    }

    /**
     * When a user inputs the registration form, see if notice is shown
     *
     * @return void
     */
    public function testRegisterCustomerNoticeAndLoggedIn()
    {
        $customer = factory(Customer::class)->make();

        $this->browse(function ($browser) use ($customer) {
            
            // Visit register page
            $browser->visit('/register')

                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    
                    // Redirect to /bookings
                    ->assertPathIs('/bookings')

                    // Get the alert after registering 
                    ->assertSee('Customer ' . $customer->firstname . ' ' . $customer->lastname . 'has been registered!')
                    ->assertSee('Logged in as ' . $customer->firstname);
        });
    }

    /**
     * When user submits nothing all errors should occur
     * 
     * @return void
     */
    public function testShowAllValidationErrors()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->clickLink('Register')
                    ->assertSee('The firstname field is required')
                    ->assertSee('The lastname field is required')
                    ->assertSee('The username field is required')
                    ->assertSee('The password field is required')
                    ->assertSee('The phone field is required')
                    ->assertSee('The address field is required');
        });
    }
}
