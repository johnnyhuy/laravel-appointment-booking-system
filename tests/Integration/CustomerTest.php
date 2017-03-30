<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\BusinessOwner;
use App\Booking;

class CustomerTest extends TestCase
{
    // Rollback database actions once test is complete with this trait
    use DatabaseTransactions;

    /**
     * Customer cannot have same username as business owner
     *
     * @return void
     */
    public function testCustomerCannotHaveSameUsernameAsBusinessOwner()
    {
        // Given business owner is created
        $businessOwner = factory(BusinessOwner::class)->create();

        // When customer inputs data
        $customer = factory(Customer::class)->make();
        $customerData = [
            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            // Customer has same username as business owner
            'username' => $businessOwner->username,
            // Password is hard-coded since factory calls bcrypt()
            'password' => 'secretpassword123',
            'password_confirmation' => 'secretpassword123',
            'phone' => $customer->phone,
            'address' => $customer->address,
        ];

        // and send request
        $response = $this->json('POST', '/register', $customerData);

        // Then respond with an error
        $response->assertJson([
            'username' => ['The username has already been taken.']
        ], 'Customer must not have the same username as business owner username');
    }
}
