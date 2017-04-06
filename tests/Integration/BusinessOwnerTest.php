<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\Employee;
use App\BusinessOwner;
use App\Booking;
use App\WorkingTime;

use Carbon\Carbon;

class BusinessOwnerTest extends TestCase
{
	// Rollback database actions once test is complete with this trait
	use DatabaseTransactions;

	/**
     * Business name validation assertions
     *
     * @return void
     */
    public function testBusinessNameValidation()
    {
    	// When business name is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'businessname' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name field is required.'
        ]);


        // When business name has less than 2 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'businessname' => 'a'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name must be at least 2 characters.'
        ]);


        // When business name has more than 32 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'businessname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name may not be greater than 32 characters.'
        ]);


        // When business name contains special characters
    	// Create business owner data
    	$businessOwnerData = [
    		'businessname' => 'My Bu$iness'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name is invalid, do not use special characters except "." and "-".'
        ]);
    }

    /**
     * First name validation assertions
     *
     * @return void
     */
    public function testFirstNameValidation()
    {
    	// When first name is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'firstname' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name field is required.'
        ]);


        // When first name has less than 2 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'firstname' => 'a'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The first name must be at least 2 characters.'
        ]);


        // When first name has more than 32 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'firstname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The first name may not be greater than 32 characters.'
        ]);


        // When first name contains special characters
    	// Create business owner data
    	$businessOwnerData = [
    		'firstname' => 'S@m'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The first name is invalid, field cannot contain special characters or numbers.'
        ]);
    }

    /**
     * Last name validation assertions
     *
     * @return void
     */
    public function testLastNameValidation()
    {
    	// When last name is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'lastname' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The business name field is required.'
        ]);


        // When last name has less than 2 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'lastname' => 'a'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The last name must be at least 2 characters.'
        ]);


        // When last name has more than 32 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'lastname' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The last name may not be greater than 32 characters.'
        ]);


        // When last name contains special characters
    	// Create business owner data
    	$businessOwnerData = [
    		'lastname' => 'D0e'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The last name is invalid, field cannot contain special characters or numbers.'
        ]);
    }

    /**
     * Username validation assertions
     *
     * @return void
     */
    public function testUsernameValidation()
    {
    	// When username is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'username' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The username field is required.'
        ]);


         // When username has less than 6 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'username' => 'usern'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The username must be at least 6 characters.'
        ]);


        // When username has more than 24 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'username' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The username may not be greater than 24 characters.'
        ]);


        // When username contains special characters
    	// Create business owner data
    	$businessOwnerData = [
    		'username' => 'u$ername'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The username may only contain letters and numbers.'
        ]);
    }

    /**
     * Password validation assertions
     *
     * @return void
     */
    public function testPasswordValidation()
    {
    	// When password is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'password' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The password field is required.'
        ]);


        // When password has less than 6 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'password' => 'secre'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The password must be at least 6 characters.'
        ]);


        // When password has more than 32 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'password' => 'secretsecretsecretsecretsecretsecretsecretsecretsecret'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The password may not be greater than 32 characters.'
        ]);


        // When password confirmation is blank
    	// Create business owner data
    	$businessOwnerData = [
    		'password' => 'secret123',
    		'password_confirmation' => '',
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The password confirmation does not match.'
        ]);
    }

    /**
     * Phone validation assertions
     *
     * @return void
     */
    public function testPhoneValidation()
    {
    	// When phone is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'phone' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The phone field is required.'
        ]);


         // When phone has less than 10 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'phone' => '000 000'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The phone must be at least 10 characters.'
        ]);


        // When phone has more than 24 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'phone' => '000 000 000 000 000 000 000 000 000 000'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The phone may not be greater than 24 characters.'
        ]);


        // When phone contains special characters and alpha characters
    	// Create business owner data
    	$businessOwnerData = [
    		'phone' => '00o0000abc'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The phone is invalid, field cannot contain special characters or numbers.'
        ]);
    }

    /**
     * Address validation assertions
     *
     * @return void
     */
    public function testAddressValidation()
    {
    	// When address is empty
    	// Create business owner data
    	$businessOwnerData = [
    		'address' => ''
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The address field is required.'
        ]);


        // When address has less than 6 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'address' => 'aaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The address must be at least 6 characters.'
        ]);


        // When address has more than 32 characters
    	// Create business owner data
    	$businessOwnerData = [
    		'address' => 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa'
    	];

    	// Send POST request to /register
        $response = $this->json('POST', '/admin/register', $businessOwnerData);

        // Check response for an error message
        $response->assertJsonFragment([
        	'The address may not be greater than 32 characters.'
        ]);
    }
}