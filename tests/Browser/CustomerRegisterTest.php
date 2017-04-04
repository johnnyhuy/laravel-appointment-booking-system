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
     * Test when customer is logged in and tries to visit /register
     *
     * @return void
     */
    public function testLoggedCustomerCannotVisitRegister()
    {
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::first())
                ->visit('/register')
                ->assertPathIs('/bookings');
        });
    }

    /**
     * Test when customer is logged in and tries to visit /login
     *
     * @return void
     */
    public function testLoggedCustomerCannotVisitLogin()
    {
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::find(1))
                ->visit('/login')
                ->assertPathIs('/bookings');
        });
    }

    /**
     * Test when customer is logged in and tries to visit /login
     *
     * @return void
     */
    public function testLoggedCustomerCanLogout()
    {
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::find(1))
                ->visit('/logout')
                ->assertSee('Successfully logged out!');
        });
    }

    /**
     * Test to retain user input after errors and registration as guest
     *
     * @return void
     */
    public function testRetainInputForm()
    {
        // Generate customer info
        $customer = factory(Customer::class)->make();

        $this->browse(function ($browser, $secondBrowser) use ($customer) {
            // When user does not enter last name
            $browser->visit('/register')
                ->type('firstname', $customer->firstname)
                ->press('Register')

                // See if firstname field still has previous input
                ->assertInputValue('firstname', $customer->firstname);

            // Register a customer
            $browser->visit('/register')
                ->type('firstname', $customer->firstname)
                ->type('lastname', $customer->lastname)
                ->type('username', $customer->username)
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $customer->phone)
                ->type('address', $customer->address)
                ->press('Register')

                // Log out user
                ->visit('/logout')

                // See if previous form input exists
                ->visit('/register')

                // Only need to see one field to assume all fields
                ->assertInputValueIsNot('firstname', $customer->firstname);

            // Visit register page and see if one field does not have previous input
            $secondBrowser->visit('/register')
                ->assertInputValueIsNot('firstname', $customer->firstname);
        });
    }

    /**
     * When a user inputs the registration form, see if notice is shown
     *
     * @return void
     */
    public function testRegisterCustomer()
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
                ->assertPathIs('/login')

                // Get the alert after registering 
                ->assertSee('Thank you for registering! You can now Login!');
        });
    }

    /**
     * When a user submits nothing all errors should occur
     * 
     * @return void
     */
    public function testShowAllValidationErrors()
    {
        $this->browse(function ($browser) {
            $browser->visit('/register')
                ->press('Register')

                // Show alerts
                ->assertSee('The firstname field is required.')
                ->assertSee('The lastname field is required.')
                ->assertSee('The username field is required.')
                ->assertSee('The password field is required.')
                ->assertSee('The phone field is required.')
                ->assertSee('The address field is required.');
        });
    }

    /**
     * Testing the input of the first name field
     * 
     * @return void
     */
    public function testFirstNameInputValidate()
    {
        $this->browse(function ($browser) {
            // If first name is required
            $browser->visit('/register')
                ->press('Register')
                ->assertSee('The firstname field is required.');

            // If first name is correct
            $browser->visit('/register')
                ->type('firstname', 'John')
                ->press('Register')
                ->assertDontSee('The firstname format is invalid.');

            // If first name contains numbers or special characters throw an error
            $browser->visit('/register')
                ->type('firstname', 'John123@#$%')
                ->press('Register')
                ->assertSee('The firstname format is invalid.');

            // If first name contains a ' symbol
            $browser->visit('/register')
                ->type('firstname', "John'O")
                ->press('Register')
                ->assertDontSee('The firstname format is invalid.');

            // If first name is less than 2 characters
            $browser->visit('/register')
                ->type('firstname', "a")
                ->press('Register')
                ->assertSee('The firstname must be at least 2 characters.');

            // If first name is less than 32 characters
            $browser->visit('/register')
                ->type('firstname', "LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem")
                ->press('Register')
                ->assertSee('The firstname may not be greater than 32 characters.');
        });
    }

    /**
     * Testing the input of the last name field
     * 
     * @return void
     */
    public function testLastNameInputValidate()
    {
        $this->browse(function ($browser) {
            // If last name is required
            $browser->visit('/register')
                ->press('Register')
                ->assertSee('The lastname field is required.');

             // If last name contains special characters and numbers
            $browser->visit('/register')
                ->type('lastname', 'Doe123@#$%')
                ->press('Register')
                ->assertSee('The lastname format is invalid.');

            // If last name is less than 2 characters
            $browser->visit('/register')
                ->type('lastname', "a")
                ->press('Register')
                ->assertSee('The lastname must be at least 2 characters.');

            // If last name is less than 32 characters
            $browser->visit('/register')
                ->type('lastname', "LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem")
                ->press('Register')
                ->assertSee('The lastname may not be greater than 32 characters.');
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
        $this->browse(function ($browser, $secondBrowser) use ($customer) {
            // If username name is required
            $browser->visit('/register')
                ->press('Register')

                // Show alerts
                ->assertSee('The username field is required.');

            // If username contains special characters throw an error
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

            // If username is filled correctly
            $browser->visit('/register')
                ->type('username', 'johndoe123')
                ->press('Register')
                ->assertDontSee('The username may only contain letters and numbers.');

            // If username already exists
            $browser->visit('/register')
                ->type('firstname', $customer->firstname)
                ->type('lastname', $customer->lastname)
                ->type('username', 'sameusername123')
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $customer->phone)
                ->type('address', $customer->address)
                ->press('Register')
                ->assertSee('Thank you for registering! You can now Login!')
                ->visit('/logout');
            $secondBrowser->visit('/register')
                ->type('firstname', $customer->firstname)
                ->type('lastname', $customer->lastname)
                ->type('username', 'sameusername123')
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $customer->phone)
                ->type('address', $customer->address)
                ->press('Register')
                ->assertSee('The username has already been taken.');
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
            // Password is required
            $browser->visit('/register')
                ->type('password_confirmation', 'secretpassword123')
                ->press('Register')

                // Show alerts
                ->assertSee('The password field is required.');

             // If password has 6 or more characters
            $browser->visit('/register')
                ->type('password', 'secret')
                ->type('password_confirmation', 'secret')
                ->press('Register')
                ->assertDontSee('The password must be at least 6 characters.');

            // If password is less than 6 characters
            $browser->visit('/register')
                ->type('password', 'secr')
                ->type('password_confirmation', 'secr')
                ->press('Register')
                ->assertSee('The password must be at least 6 characters.');

            // If password is greater than 32 characters
            $browser->visit('/register')
                ->type('password', 'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890')
                ->type('password_confirmation', 'secr')
                ->press('Register')
                ->assertSee('The password may not be greater than 32 characters.');

            // If password confirmation is not filled
            $browser->visit('/register')
                ->type('password', 'secretpassword123')
                ->press('Register')

                // Show password confirmation alert
                ->assertSee('The password confirmation does not match.');
        });
    }

    /**
     * Testing the input of the phone field
     * 
     * @return void
     */
    public function testPhoneInputValidate()
    {
        // Start browser
        $this->browse(function ($browser) {
            // When a user does not enter a phone field, alert and error
            $browser->visit('/register')
                ->press('Register')
                ->assertSee('The phone field is required.');

            // Phone must be at least 10 characters
            $browser->visit('/register')
                ->type('phone', '0000000000')
                ->press('Register')
                ->assertDontSee('The phone must be at least 10 characters.');

            // Phone must be less than 24 characters
            $browser->visit('/register')
                ->type('phone', '000000000000000000000000000000000000000000000000000000000000')
                ->press('Register')
                ->assertSee('The phone may not be greater than 24 characters.');

            // Phone can contain spaces
            $browser->visit('/register')
                ->type('phone', '000 000 000 000')
                ->press('Register')
                ->assertDontSee('The phone format is invalid.');

            // Phone contains alphabet characters
            $browser->visit('/register')
                ->type('phone', 'abcabcabcabc')
                ->press('Register')
                ->assertSee('The phone format is invalid.');

        });
    }

    /**
     * Testing the input of the address field
     * 
     * @return void
     */
    public function testAddressInputValidate()
    {
        $this->browse(function ($browser) {
            // When a user inputs nothing in address field, alert
            $browser->visit('/register')
                ->press('Register')

                ->assertSee('The address field is required.');

            // Lower bound
            $browser->visit('/register')
                ->type('address', 'Lorem')
                ->press('Register')
                ->assertSee('The address must be at least 6 characters.');

            // Middle bound
            $browser->visit('/register')
                ->type('address', '1 Swan Street')
                ->press('Register')
                ->assertDontSee('The address must be at least 6 characters.');

            // Upper bound
            $browser->visit('/register')
                ->type('address', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vel gravida erat. In eleifend turpis et lacus laoreet aliquam et eu purus')
                ->press('Register')
                ->assertSee('The address may not be greater than 32 characters.');
        });
    }

    /**
     * Customer retains first name field after one redirect
     *
     * @return void
     */
    public function testCustomerFirstNameRetainsForOneRedirect()
    {
        $this->browse(function ($browser, $secondBrowser) {
            $browser->visit('/register')
                ->type('firstname', 'John')
                ->press('Register')
                ->assertInputValue('firstname', 'John');
            $browser->visit('/register')
                ->assertInputValueIsNot('firstname', 'John');
            $secondBrowser->visit('/register')
                ->assertInputValueIsNot('firstname', 'John');
        });
    }
}
