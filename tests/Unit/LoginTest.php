<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\BusinessOwner;

use App\Http\Controllers\BusinessOwnerController;
use App\Http\Controllers\CustomerController;

class testLogin extends TestCase
{
    use DatabaseTransactions;
	
	public function testLoginCustomerPass() {
		//Adding a customer to the database and logging in with
		//their credentials should pass for customer login but also
		//fail for business owner login
		
		$customer = factory(Customer::class)->create();
		
		$this->assertTrue(CustomerController::loginCustomer($customer->username, $customer->password) &&
				!BusinessOwnerController::loginBusinessOwner($customer->username, $customer->password));
	}
	
	public function testLoginBusinessOwnerPass() {
		//Add a business ownert to the database and logging in with
		//their credients should pass for business owner login but also
		//fail for the customer login
		
		$bOwner = factory(BusinessOwner::class)->create();
		
		$this->assertTrue(CustomerController::loginCustomer($bOwner->username, $bOwner->password) &&
				!BusinessOwnerController::loginBusinessOwner($bOwner->username, $bOwner->password));
	}
	
	public function testInvalidLoginFail() {
		//Test Logging in with credentials not linked to an account fails
		$this->assertFalse(CustomerController::loginCustomer('username', 'password') ||
				BusinessOwnerController::loginBusinessOwner('username', 'password'));
	}
}
