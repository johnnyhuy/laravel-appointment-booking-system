<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\BusinessOwner;

class BusinessOwnerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test business owner full name exists
     *
     * @return void
     */
    public function testBusinessOwnerHasOwnerName()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['owner_name' => $factory->owner_name]);

        // Business owner has first name?
        $this->assertEquals($factory->owner_name, $businessowner->owner_name);
    }
    
    /**
     * Test business owner business name exists
     *
     * @return void
     */
    public function testBusinessOwnerHasBusinessName()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['business_name' => $factory->business_name]);

        // Business owner has business name?
        $this->assertEquals($factory->business_name, $businessowner->business_name);
    }
    
    /**
     * Test business owner username exists
     *
     * @return void
     */
    public function testBusinessOwnerHasUsername()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['username' => $factory->username]);

        // Business owner has last name?
        $this->assertEquals($factory->username, $businessowner->username);
    }

    /**
     * Test business owner password exists
     *
     * @return void
     */
    public function testBusinessOwnerHasPassword()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['password' => $factory->password]);

        // Business owner has last name?
        $this->assertEquals($factory->password, $businessowner->password);
    }

    /**
     * Test business owner phone exists
     *
     * @return void
     */
    public function testBusinessOwnerHasPhone()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['phone' => $factory->phone]);

        // BusinessOwner has last name?
        $this->assertEquals($factory->phone, $businessowner->phone);
    }

    /**
     * Test business owner address exists
     *
     * @return void
     */
    public function testBusinessOwnerHasAddress()
    {
        // Generate fake data
        $factory = factory(BusinessOwner::class)->make();

        // Create business owner
        $businessowner = new BusinessOwner(['address' => $factory->address]);

        // Business owner has last name?
        $this->assertEquals($factory->address, $businessowner->address);
    }
}
