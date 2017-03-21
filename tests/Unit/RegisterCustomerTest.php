<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\Http\Controllers\CustomerController;

class RegisterCustomerTest extends TestCase
{
	use DatabaseTransactions;
 

	public function testRegisterCustomerPass() {
		//Given a customer with valid registration details, customer
		//registration should be succesfful
		$customer = factory(Customer::class)->make();
		
		$this->assertTrue(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterCustomerIdenticalUsernames() {
		//Given 2 customers, one who registers successfully with the username 'pizzaDude207'
		//The 2nd customer should not also be able to register with the username 'pizzaDude207'
		$customer1 = factory(Customer::class)->make([
			'username' => 'pizzaDude207'
		]);
		$customer2 = factory(Customer::class)->make([
			'username' => 'pizzaDude207'
		]);
		
		$this->assertTrue(
			CustomerController::registerCustomer($customer1->name, 
												$customer1->address,
												$customer1->username,
												$customer1->password,
												$customer1->phone) &&
			!CustomerController::registerCustomer($customer2->name,
												$customer2->address,
												$customer2->username,
												$customer2->password,
												$customer2->phone));
	}
	
	public function testRegisterWithBlankNameFail() {
		//Given a customer with valid registration details, customer and a blank name
		//registration should fail
		$customer = factory(Customer::class)->make([
			'name' => ''
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterWithBlankPasswordFail() {
		//Given a customer with valid registration details, customer and a blank password
		//registration should fail
		$customer = factory(Customer::class)->make([
			'password' => ''
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterWithBlankUsernameFail() {
		//Given a customer with valid registration details, customer and a blank username
		//registration should fail
		$customer = factory(Customer::class)->make([
			'username' => ''
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterWithBlankPhoneFail() {
		//Given a customer with valid registration details, customer and a blank phone number
		//registration should fail
		$customer = factory(Customer::class)->make([
			'phone' => ''
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterWithBlankAddressFail() {
		//Given a customer with valid registration details, customer and a blank address
		//registration should fail
		$customer = factory(Customer::class)->make([
			'address' => ''
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
	
	public function testRegisterWithInvalidPhoneNumberFail() {
		//Given a customer with valid registration details but a phone
		//number that contains values that aren't numbers
		$customer = factory(Customer::class)->make([
			'phone' => 'NOT A PHONE NUMBER'
		]);
		
		$this->assertFalse(CustomerController::registerCustomer($customer->name, $customer->address,
				$customer->username, $customer->password, $customer->phone));
	}
}
