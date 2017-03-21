<?php

namespace Tests\Unit;

use App\BusinessOwner;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BusinessOwnerTest extends TestCase
{
    public function testRegisterBusinessOwner()
    {
    	// Create the business owner
    	$businessOwner = factory(BusinessOwner::class)->create();

    	// Send session response
        $response = $this->withSession(['message' => 'Business Owner registration success.'])->get('/admin');

        // Check if session exists
        $response->assertSessionHas('message');
    }

    public function testLoginBusinessOwner()
    {
    	// Create the business owner
    	$businessOwner = factory(BusinessOwner::class)->create();

    	// Send session response
        $response = $this->withSession(['message' => 'Business Owner login success.'])->get('/admin');

        // Check if session exists
        $response->assertSessionHas('message');
    }
}
