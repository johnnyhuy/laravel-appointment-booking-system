<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;

class CustomerTest extends TestCase
{
	use DatabaseTransactions;
 
 	/**
     * Test customer first name exists
     *
     * @return void
     */
	public function testCustomerHasFirstName()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['firstname' => $factory->firstname]);

		// Customer has first name?
		$this->assertEquals($factory->firstname, $customer->firstname);
	}
	
	/**
     * Test customer last name exists
     *
     * @return void
     */
	public function testCustomerHasLastName()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['lastname' => $factory->lastname]);

		// Customer has last name?
		$this->assertEquals($factory->lastname, $customer->lastname);
	}
	
	/**
     * Test customer username exists
     *
     * @return void
     */
	public function testCustomerHasUsername()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['username' => $factory->username]);

		// Customer has last name?
		$this->assertEquals($factory->username, $customer->username);
	}

	/**
     * Test customer password exists
     *
     * @return void
     */
	public function testCustomerHasPassword()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['password' => $factory->password]);

		// Customer has last name?
		$this->assertEquals($factory->password, $customer->password);
	}

	/**
     * Test customer phone exists
     *
     * @return void
     */
	public function testCustomerHasPhone()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['phone' => $factory->phone]);

		// Customer has last name?
		$this->assertEquals($factory->phone, $customer->phone);
	}

	/**
     * Test customer address exists
     *
     * @return void
     */
	public function testCustomerHasAddress()
	{
		// Generate fake data
		$factory = factory(Customer::class)->make();

		// Create customer
		$customer = new Customer(['address' => $factory->address]);

		// Customer has last name?
		$this->assertEquals($factory->address, $customer->address);
	}
}
