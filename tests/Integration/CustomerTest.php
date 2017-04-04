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

    public function setUp() 
    {
        parent::setUp();

        // Generate fakedata
        $customer = factory(Customer::class)->make();
        $this->customerData = [
            'firstname' => $customer->firstname,
            'lastname' => $customer->lastname,
            'username' => $customer->username,
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

        $this->customerData['username'] = $businessOwner->username;

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
        // If user inputs nothing in the firstname field
        $this->customerData = ['firstname' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname field is required.']
        ], 'Customer cannot enter nothing in firstname field.');

        // Is user inputs special characters
        $this->customerData = ['firstname' => 'John(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname format is invalid.']
        ], 'Customer firstname field must be valid.');
    }
}
