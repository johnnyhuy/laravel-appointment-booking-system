<?php

namespace Tests\Browser;

use App\Customer;

use Tests\DuskTestCase;

class CustomerRegisterTest extends DuskTestCase
{
    
    /**
     * Test registering button to exist at the homepage
     *
     * @return void
     */
    public function testRegisterPageExists()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->assertPathIs('/register')
                    ->assertSee('Register');
        });
    }

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
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            
            // Visit register page
            $browser->visit('/register')

                    // Fill the form with customer details and submit
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    
                    // Redirect to /bookings
                    ->assertPathIs('/bookings')

                    // Get the alert after registering 
                    ->assertSee('Customer ' . $customer->firstname . ' ' . $customer->lastname . ' has been registered!')

                    // Alert customer is logged in
                    ->assertSee('Logged in as ' . $customer->firstname);
        });
    }

    /**
     * When a user submits nothing all errors should occur
     * 
     * @return void
     */
    public function testShowAllValidationErrors()
    {
        // Start browser
        $this->browse(function ($browser) {
            $browser->visit('/register')
                    ->press('Register')

                    // Show alerts
                    ->assertSee('The firstname field is required')
                    ->assertSee('The lastname field is required')
                    ->assertSee('The username field is required')
                    ->assertSee('The password field is required')
                    ->assertSee('The phone field is required')
                    ->assertSee('The address field is required');
        });
    }

    /**
     * Testing the input of the first name field
     * 
     * @return void
     */
    public function testFirstNameInputValidate()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            // Test if input is required
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show alerts
                    ->assertSee('The firstname field is required');

            // Test if first name contains numbers or special characters throw an error
            $browser->visit('/register')
                    ->type('firstname', 'John123@#$%')
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('The firstname may only contain letters.');
        });
    }

    /**
     * Testing the input of the last name field
     * 
     * @return void
     */
    public function testLastNameInputValidate()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show alerts
                    ->assertSee('The lastname field is required');

             // Test if last name contains numbers or special characters throw an error
            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', 'Doe123@#$%')
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('The lastname may only contain letters.');
        });
    }

    /**
     * Testing the input of the username field
     * 
     * @return void
     */
    public function testUsernameInputValidate()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show alerts
                    ->assertSee('The username field is required');

            // Test if last name contains special characters throw an error
            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', 'johndoe123!@#')
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('The username may only contain letters and numbers.');

            // Test if last name contains special characters throw an error
            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', 'johndoe123')
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('Customer ' . $customer->firstname . ' ' . $customer->lastname . ' has been registered!');
        });
    }

    /**
     * Testing the input of the username field
     * 
     * @return void
     */
    public function testPasswordInputValidate()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show alerts
                    ->assertSee('The password field is required');

             // Test if password has 6 or more characters
            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secret')
                    ->type('password_confirmation', 'secret')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('Customer ' . $customer->firstname . ' ' . $customer->lastname . ' has been registered!');

            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secr')
                    ->type('password_confirmation', 'secr')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertSee('The password must be at least 6 characters.');
        });
    }

    /**
     * When a user does not enter the password confirmation field
     * 
     * @return void
     */
    public function testShowConfirmedPasswordRequiredError()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show password confirmation alert
                    ->assertSee('The password confirmation does not match.');
        });
    }

    /**
     * When a user does not enter the phone field
     * 
     * @return void
     */
    public function testShowPhoneRequiredError()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();
        
        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('address', $customer->address)
                    ->press('Register')

                    // Show password confirmation alert
                    ->assertSee('The phone field is required');
        });
    }

    /**
     * When a user does not enter the address field
     * 
     * @return void
     */
    public function testShowAddressRequiredError()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    // Fill the form with customer details
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->press('Register')

                    // Show password confirmation alert
                    ->assertSee('The address field is required');
        });
    }
}
