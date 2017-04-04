<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\Customer;
use App\BusinessOwner;

class BusinessOwnerRegisterTest extends DuskTestCase
{
    /**
     * Test when admin registers with valid data the form accepts the input
     *
     * @return void
     */
    public function testRegisterBusinessOwner()
    {
        // Generate customer info
        $bo = factory(BusinessOwner::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($bo) {
            
            // Visit register page
            $browser->visit('/admin')

                // Fill the form with customer details and submit
                ->type('businessname', $bo->business_name)
                ->type('fullname', $bo->owner_name)
                ->type('username', $bo->username)
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $bo->phone)
                ->type('address', $bo->address)
                ->press('Register')

                // Get the alert after registering 
                ->assertSee('Business Owner registration success');
        });
    }

     /**
     * A test to determine whether the admin registration page exists
     *
     * @return void
     */
    public function testRegisterPageExists()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin')
                ->assertPathIs('/admin')
                ->assertSee('Register');
        });
    }

    /**
     * Test registering button to exist at the registration page
     *
     * @return void
     */
    public function testRegisterButtonShown()
    {
        $this->browse(function ($browser) {
            $browser->visit('/admin')
                    ->assertSee('Register');
        });
    }

    /**
     * Test when customer is logged in and tries to visit /admin registration
     *
     * @return void
     */
    public function testLoggedCustomerCannotVisitRegister()
    {
        factory(Customer::class)->create();

        $this->browse(function ($browser) {
            $browser->loginAs(Customer::first())
                ->visit('/admin')
                ->assertPathIs('/bookings');
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
            $browser->visit('/admin')
                ->press('Register')

                // Show alerts
                ->assertSee('The fullname field is required.')
                ->assertSee('The businessname field is required.')
                ->assertSee('The username field is required.')
                ->assertSee('The password field is required.')
                ->assertSee('The phone field is required.')
                ->assertSee('The address field is required.');
        });
    }

    /**
     * Testing the input of the fullname field
     * 
     * @return void
     */
    public function testFullNameInputValidate()
    {
        // Generate customer info
        $bo = factory(BusinessOwner::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($bo) {

            // If full name field is empty
            $browser->visit('/admin')
                ->type('businessname', $bo->business_name)
                ->type('fullname', '')
                ->type('username', $bo->username)
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $bo->phone)
                ->type('address', $bo->address)
                ->press('Register')
                // Show alerts
                ->assertSee('The fullname field is required.');

            // If fullname name is too long
            $browser->visit('/admin')
                ->type('fullname', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
                ->press('Register')
                ->assertSee('The fullname may not be greater than 255 characters.');

            // If fullname name contains special characters
            $browser->visit('/admin')
                ->type('fullname', 'fullname !@#$%^&*() !@#$%^&*()')
                ->press('Register')
                ->assertSee('The fullname format is invalid.');

            // If fullname name contains a hypen
            $browser->visit('/admin')
                ->type('fullname', 'John-Smith Smithy')
                ->press('Register')
                ->assertDontSee('The fullname format is invalid.');
        });
    }

     /**
     * Testing the input of the businessname field
     * 
     * @return void
     */
    public function testBusinessNameInputValidate()
    {
        // Generate customer info
        $bo = factory(BusinessOwner::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($bo) {

            // If business name field is empty
            $browser->visit('/admin')
                ->type('businessname', '')
                ->type('fullname', $bo->owner_name)
                ->type('username', $bo->username)
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $bo->phone)
                ->type('address', $bo->address)
                ->press('Register')
                // Show alerts
                ->assertSee('The businessname field is required.');

            // If business name is too long
            $browser->visit('/admin')
                ->type('businessname', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa')
                ->press('Register')
                ->assertSee('The businessname may not be greater than 255 characters.');

            // If business name contains special characters
            $browser->visit('/admin')
                ->type('businessname', 'business !@#$%^&*() !@#$%^&*()')
                ->press('Register')
                ->assertDontSee('The businessname format is invalid.');
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
        $bo = factory(BusinessOwner::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($bo) {

            // If username contains special characters throw an error
            $browser->visit('/admin')
                ->type('username', '')
                ->type('businessname', $bo->business_name)
                ->type('fullname', $bo->owner_name)
                ->type('password', 'secretpassword123')
                ->type('password_confirmation', 'secretpassword123')
                ->type('phone', $bo->phone)
                ->type('address', $bo->address)
                ->press('Register')
                // Show alerts
                ->assertSee('The username field is required.');

            // If username is filled correctly
            $browser->visit('/admin')
                ->type('username', 'johndoe123')
                ->press('Register')
                ->assertDontSee('The username may only contain letters and numbers.');

            // If username is too short
            $browser->visit('/admin')
                ->type('username', 'john5')
                ->press('Register')
                ->assertSee('The username must be at least 6 characters.');

            // If username is too long
            $browser->visit('/admin')
                ->type('username', 'johndoe123345678901234567')
                ->press('Register')
                ->assertSee('The username may not be greater than 24 characters.');
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
        $bo = factory(BusinessOwner::class)->make();

        // Start browser
        $this->browse(function ($browser) use ($bo) {
            // Password is required
            $browser->visit('/admin')
                ->type('password_confirmation', 'secretpassword123')
                ->press('Register')

                // Show alerts
                ->assertSee('The password field is required.');

             // If password has 6 or more characters
            $browser->visit('/admin')
                ->type('password', 'secret')
                ->type('password_confirmation', 'secret')
                ->press('Register')
                ->assertDontSee('The password must be at least 6 characters.');

            // If password is less than 6 characters
            $browser->visit('/admin')
                ->type('password', 'secr')
                ->type('password_confirmation', 'secr')
                ->press('Register')
                ->assertSee('The password must be at least 6 characters.');

            // If password is greater than 32 characters
            $browser->visit('/admin')
                ->type('password', 'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890')
                ->type('password_confirmation', 'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890')
                ->press('Register')
                ->assertSee('The password may not be greater than 32 characters.');

            // If password confirmation is not filled
            $browser->visit('/admin')
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
            $browser->visit('/admin')
                ->press('Register')
                ->assertSee('The phone field is required.');

            // Phone must be at least 10 characters
            $browser->visit('/admin')
                ->type('phone', '0000000000')
                ->press('Register')
                ->assertDontSee('The phone must be at least 10 characters.');

            // Phone must be less than 24 characters
            $browser->visit('/admin')
                ->type('phone', '000000000000000000000000000000000000000000000000000000000000')
                ->press('Register')
                ->assertSee('The phone may not be greater than 24 characters.');

            // Phone can contain spaces
            $browser->visit('/admin')
                ->type('phone', '000 000 000 000')
                ->press('Register')
                ->assertDontSee('The phone format is invalid.');

            // Phone contains alphabet characters
            $browser->visit('/admin')
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
            $browser->visit('/admin')
                ->press('Register')

                ->assertSee('The address field is required.');

            // Lower bound
            $browser->visit('/admin')
                ->type('address', 'Lorem')
                ->press('Register')
                ->assertSee('The address must be at least 6 characters.');

            // Middle bound
            $browser->visit('/admin')
                ->type('address', '1 Swan Street')
                ->press('Register')
                ->assertDontSee('The address must be at least 6 characters.');

            // Upper bound
            $browser->visit('/admin')
                ->type('address', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla vel gravida erat. In eleifend turpis et lacus laoreet aliquam et eu purus')
                ->press('Register')
                ->assertSee('The address may not be greater than 32 characters.');
        });
    }

}
