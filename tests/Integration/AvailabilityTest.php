<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Employee;

use Carbon\Carbon;

class AvailabilityTest extends TestCase
{
	// Rollback database actions once test is complete with this trait
    use DatabaseTransactions;

    /**
     * Call a function and return true or false
     * If returns true, employee is available and vice versa
     *
     * @return void
     */
    public function testIsEmployeeIsAvailable()
    {

    }

    /**
     * Get the employees availability for all 7 days
     *
     * @return void
     */
    public function testGetEmployeeAvailability()
    {
    	// Create an employee
    	$employee = factory(Employee::class)->create();

    	
    }
}