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

          // Is user inputs symbols
        $this->customerData = ['firstname' => 'Hi123'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname format is invalid.']
        ], 'Customer firstname field must be valid.');

          // If user inputs less than 2 characters
        $this->customerData = ['firstname' => 'H'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname must be at least 2 characters.']
        ], 'Customer firstname field must be valid.');

           // If user inputs greater than 32 characters
        $this->customerData = ['firstname' => 'LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremaa'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname may not be greater than 32 characters.']
        ], 'Customer firstname field must be valid.');

    }

    public function testCustomerRegisterSurnameValidation()
    {
    
        // If user inputs nothing in the firstname field
        $this->customerData = ['lastname' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname field is required.']
        ], 'Customer cannot enter nothing in lastname field.');

        // Is user inputs special characters
        $this->customerData = ['lastname' => 'Smith(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname format is invalid.']
        ], 'Customer lastname field must be valid.');

          // Is user inputs symbols
        $this->customerData = ['lastname' => 'Hi123'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname format is invalid.']
        ], 'Customer lastname field must be valid.');

          // If user inputs less than 2 characters
        $this->customerData = ['lastname' => 'H'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname must be at least 2 characters.']
        ], 'Customer lastname field must be valid.');

           // If user inputs greater than 32 characters
        $this->customerData = ['lastname' => 'LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremaa'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname may not be greater than 32 characters.']
        ], 'Customer lastname field must be valid.');

    }

     public function testCustomerRegisterUsernameValidation()
     {

        // If user inputs nothing in the firstname field
        $this->customerData = ['username' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'username' => ['The username field is required.']
        ], 'Customer cannot enter nothing in username field.');


         // Is user inputs special characters
        $this->customerData = ['username' => 'jpower(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'username' => ['The username may only contain letters and numbers.']
        ], 'Customer username field must be valid.');

        //If user inputs a taken Username
        $customerOne = factory(Customer::class)->create();

        $this->customerData = ['username' => $customerOne->username];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'username' => ['The username has already been taken.']
        ], 'The username has already been taken.');
     }
      public function testCustomerRegisterPasswordValidation()
     {

        // If user inputs nothing in the password confirmation field
        

         // If user inputs nothing in the password field
        $this->customerData = ['password' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'password' => ['The password field is required.']
        ], 'Customer cannot enter nothing in password field.');

          // If user inputs less than 6 characters
        $this->customerData = ['password' => 'Hi123','password_confirmation'=>'Hi123'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'password' => ['The password must be at least 6 characters.']
        ], 'Customer password field must be valid.');

           // If user inputs less than 6 characters
        $this->customerData = ['password' => 'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890','password_confirmation'=>'verylongsecretpassword1234567890verylongsecretpassword1234567890erylongsecretpassword1234567890'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'password' => ['The password may not be greater than 32 characters.']
        ], 'Customer password field must be valid.');
     }

     public function testCustomerRegisterPhoneValidation()
     {

        // If user inputs nothing in the firstname field
        $this->customerData = ['phone' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone field is required.']
        ], 'Customer cannot enter nothing in phone field.');

        // If user inputs less than 10 characters
        $this->customerData = ['phone' => '042222'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone must be at least 10 characters.']
        ], 'Customer phone field must be valid.');

        // If user inputs more than 24 characters
        $this->customerData = ['phone' => '000000000000000000000000000000000000000000000000000000000000'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone may not be greater than 24 characters.']
        ], 'Customer phone field must be valid.');

        //When user inputs spaces with valid number



        // If user inputs alphabet characters
        $this->customerData = ['phone' => 'abcasdsadas'];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone format is invalid.']
        ], 'Customer phone field must be valid.');

     }

     public function testCustomerRegisterAddressValidation()
     {

        // If user inputs nothing in the firstname field
        $this->customerData = ['address' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'address' => ['The address field is required.']
        ], 'Customer cannot enter nothing in address field.');

        // If user inputs nothing in the firstname field
        $this->customerData = ['address' => ''];

        // Send request
        $response = $this->json('POST', '/register', $this->customerData);

        // Then respond with an error
        $response->assertJson([
            'address' => ['The address field is required.']
        ], 'Customer cannot enter nothing in address field.');
     }
}
