<?php

namespace Tests\Browser;

use App\Customer;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CustomerLoginTest extends DuskTestCase
{
	 /**
     * A test to determine whether the admin registration page exists
     *
     * @return void
     */
    public function testRegisterPageExists()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                ->assertPathIs('/login')
                ->assertSee('Sign in');
        });
    }

    /**
     * Test sign in button to exist at the login page
     *
     * @return void
     */
    public function testSignInButtonShown()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->assertSee('Sign in');
        });
    }

    /**
     * Test registering button to exist at the login page
     *
     * @return void
     */
    public function testRegisterButtonShown()
    {
        $this->browse(function ($browser) {
            $browser->visit('/login')
                    ->assertSee('Register');
        });
    }

     /**
     * Test a valid customer can login
     *
     * @return void
     */
    public function testLoginCustomer()
    {
        // Create a customer and set the password
        $customer = factory(Customer::class)->create([
            'password' => bcrypt('secret'),
        ]);

        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/login')
                ->visit('/login')
                ->type('username', $customer->username)
                ->type('password', 'secret')
                ->press('Sign in')
                ->assertSee('Successfully logged in!');
        });
    }

    /**
     * Test a valid customer with wrong password
     *
     * @return void
     */
    public function testLoginCustomerBadPasswords()
    {
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/logout')
                ->type('username', $customer->username)
                // Encrypted version of the password shouldn't pass
                ->type('password', bcrypt($customer->password))
                ->press('Sign in')
                ->assertSee('Error! Invalid credentials.');

            $browser->visit('/logout')
                ->type('username', $customer->username)
                // Blank password shouldn't pass
                ->type('password', '')
                ->press('Sign in')
                ->assertSee('Error! Invalid credentials.');

            $browser->visit('/logout')
                ->type('username', $customer->username)
                // Correct password with incorrect case should fail
                ->type('password', 'Secret')
                ->press('Sign in')
                ->assertSee('Error! Invalid credentials.');

            $browser->visit('/logout')
                ->type('username', $customer->username)
                // Correct password with extra whitespace should fail
                ->type('password', 'secret ')
                ->press('Sign in')
                ->assertSee('Error! Invalid credentials.');
        });
    }

    /**
     * Test when customer is logged in and tries to visit login page
     *
     * @return void
     */
    public function testLoggedCustomerCannotVisitLogin()
    {
        $customer = factory(Customer::class)->create();

        $this->browse(function ($browser) use ($customer) {
            $browser->loginAs($customer)
                ->visit('/login')
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
            $browser->visit('/login')
                ->press('Sign in')

                // Show alerts
                ->assertSee('Error! Invalid credentials.');
        });
    }
}