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

    protected $customerData;

    protected function setUp() {
        // When customer inputs data
        $customer = factory(Customer::class)->make();

        $this->customerData = [
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
    }

    /**
     * Customer cannot have same username as business owner
     *
     * @return void
     */
    public function testCustomerCannotHaveSameUsernameAsBusinessOwner()
    {
        // Given business owner is created
        $businessOwner = factory(BusinessOwner::class)->create();

        // and send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'username' => ['The username has already been taken.']
        ], 'Customer must not have the same username as business owner username');
    }

    /**
     * Tests validation rules for first name field when customer registers
     *
     * @return void
     */
    public function testCustomerRegisterFirstNameValidation()
    {
        // Send post request to /register with included customer input data
        $response = $this->json('POST', '/register', $customerData);

        $this->customerData->firstname = '';

        // Then respond with an error
        $response->assertJson(['messages'], 'Customer must not have the same username as business owner username');
    }
}
