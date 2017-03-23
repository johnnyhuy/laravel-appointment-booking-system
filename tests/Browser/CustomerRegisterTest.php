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

    public function testRegisterCustomer()
    {
        $customer = factory(Customer::class)->make();

        $this->browse(function ($browser) use ($customer) {
            $browser->visit('/register')
                    ->type('firstname', $customer->firstname)
                    ->type('lastname', $customer->lastname)
                    ->type('username', $customer->username)
                    ->type('password', 'secretpassword123')
                    ->type('password_confirmation', 'secretpassword123')
                    ->type('phone', $customer->phone)
                    ->type('address', $customer->address)
                    ->press('Register')
                    ->assertPathIs('/bookings');
        });
    }
}
