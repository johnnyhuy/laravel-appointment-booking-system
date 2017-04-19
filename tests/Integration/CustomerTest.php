<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Customer;
use App\BusinessOwner;
use App\Booking;

class CustomerTest extends TestCase
{
    /**
     * Customer cannot have same username as business owner
     *
     * @return void
     */
    public function testCustomerCannotHaveSameUsernameAsBusinessOwner()
    {
        // Given business owner is created
        $businessOwner = factory(BusinessOwner::class)->create();

        // Set customer username as business owner username
        $customerData = [
            'username' => $businessOwner->username
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The username has already been taken.'
        ]);
    }

    /**
     * Tests validation rules for first name field when customer registers
     *
     * @return void
     */
    public function testCustomerRegisterFirstNameValidation()
    {
        // If user inputs nothing in the firstname field
        // Create customer data
        $customerData = [
            'firstname' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The firstname field is required.'
        ]);


        // If user inputs special characters
        // Create customer data
        $customerData = [
            'firstname' => 'John(@*^*!(&'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The firstname format is invalid.'
        ]);


        // Is user inputs numbers
        // Create customer data
        $customerData = [
            'firstname' => 'John123'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The firstname format is invalid.'
        ]);


        // If user inputs less than 2 characters
        // Create customer data
        $customerData = [
            'firstname' => 'H'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The firstname must be at least 2 characters.'
        ]);


        // If user inputs greater than 32 characters
        // Create customer data
        $customerData = [
            'firstname' => 'Johnnnnnnnnnnnnnnnnnnnnnnnnnnnnnnny'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The firstname may not be greater than 32 characters.'
        ]);

    }

    /**
     * Test customer register last name validation
     *
     * @return void
     */
    public function testCustomerRegisterLastNameValidation()
    {
        // If user inputs nothing in the firstname field
        // Create customer data
        $customerData = [
            'lastname' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The lastname field is required.'
        ]);


        // Is user inputs special characters
        // Create customer data
        $customerData = [
            'lastname' => 'Smith(@*^*!(&'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The lastname format is invalid.'
        ]);


        // Is user inputs symbols
        // Create customer data
        $customerData = [
            'lastname' => 'Hi123'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The lastname format is invalid.'
        ]);


        // If user inputs less than 2 characters
        // Create customer data
        $customerData = [
            'lastname' => 'H'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The lastname must be at least 2 characters.'
        ]);


        // If user inputs greater than 32 characters
        // Create customer data
        $customerData = [
            'lastname' => 'LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremaa'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The lastname may not be greater than 32 characters.'
        ]);
    }

    /**
     * Test customer register username validation
     *
     * @return void
     */
    public function testCustomerRegisterUsernameValidation()
    {
        // If user inputs nothing in the firstname field
        $customerData = [
            'username' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The username field is required.'
        ]);


         // Is user inputs special characters
        $customerData = [
            'username' => 'jpower(@*^*!(&'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The username may only contain letters and numbers.'
        ]);


        // If user inputs a taken Username
        // Create customer data
        $customerOne = factory(Customer::class)->create();

        $customerData = [
            'username' => $customerOne->username
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The username has already been taken.'
        ]);
     }

    /**
     * Test customer register password validation
     *
     * @return void
     */
    public function testCustomerRegisterPasswordValidation()
    {
        // If user inputs nothing in the password field
        // Create customer data
        $customerData = [
            'password' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The password field is required.'
        ]);


        // If user inputs less than 6 characters
        // Create customer data
        $customerData = [
            'password' => 'Hi123',
            'password_confirmation' => 'Hi123'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The password must be at least 6 characters.'
        ]);


        // If user inputs less than 6 characters
        // Create customer data
        $customerData = [
            'password' => 'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890',
            'password_confirmation'=>'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The password may not be greater than 32 characters.'
        ]);
     }

    /**
     * Test customer register phone validation
     *
     * @return void
     */
    public function testCustomerRegisterPhoneValidation()
    {
        // If user inputs nothing in the firstname field
        // Create customer data
        $customerData = [
            'phone' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The phone field is required.'
        ]);


        // If user inputs less than 10 characters
        // Create customer data
        $customerData = [
            'phone' => '042222'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The phone must be at least 10 characters.'
        ]);


        // If user inputs more than 24 characters
        // Create customer data
        $customerData = [
            'phone' => '000000000000000000000000000000000000000000000000000000000000'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The phone may not be greater than 24 characters.'
        ]);


        // If user inputs alphabet characters
        // Create customer data
        $customerData = [
            'phone' => 'abcasdsadas'
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The phone format is invalid.'
        ]);
     }

     /**
     * Test customer register address validation
     *
     * @return void
     */
    public function testCustomerRegisterAddressValidation()
    {
        // If user inputs nothing in the firstname field
        // Create customer data
        $customerData = [
            'address' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The address field is required.'
        ]);


        // If user inputs nothing in the firstname field
        // Create customer data
        $customerData = [
            'address' => ''
        ];

        // Send a POST request to /register with customer data
        $response = $this->json('POST', '/register', $customerData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The address field is required.'
        ]);
     }
}
