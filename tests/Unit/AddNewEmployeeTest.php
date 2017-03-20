<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Database\Eloquent\Factory;

use App\Http\Controllers\EmployeeController;
use App\Availability;

class testAddNewEmployee extends TestCase
{
	use DatabaseTransactions;
	
	public function testAddNewEmployeePass() {
		//Given I add a new employee with the name Craig with two availabilities
		//The function will accept the new employee
		
		$name = 'Craig';
		$availabilities = [];
		$availabilities[0] = new Availability;
		$availabilities[0]->day = "Tuesday";
		$availabilities[0]->start_time = "9:00";
		$availabilities[0]->end_time = "18:30";
		$availabilities[1] = new Availability;
		$availabilities[1]->day = "Wednesday";
		$availabilities[1]->start_time = "9:00";
		$availabilities[1]->end_time = "18:30";
		
		$this->assertTrue(EmployeeController::addNewEmployee($name, $availabilities));
	}
	
	public function testAddNewEmployeeBlankNameFail() {
		//Given the name of the employee is blank
		//The function should reject the new employee
		
		$name = '';
		$availabilities = [];
		$availabilities[0] = new Availability;
		$availabilities[0]->day = "Tuesday";
		$availabilities[0]->start_time = "9:00";
		$availabilities[0]->end_time = "18:30";
		
		$this->assertFalse(EmployeeController::addNewEmployee($name, $availabilities));
	}
	
	public function testAddNewEmployeeNoAvailability() {
		//Given the availability of the employee is nothing,
		//the system should reject the new employee
		
		$name = 'Craig';
		$availabilities = [];
		
		$this->assertFalse(EmployeeController::addNewEmployee($name, $availabilities));
	}
	
	public function testAddNewEmployeeOverlappingAvailabilitesFail() {
		//Given I add a new employee with the name Craig with two availabilities
		//which overlap eachother, this should not be allowed to avoid confusion
		//The function should reject the new employee

		$name = 'Craig';
		$availabilities = [];
		$availabilities[0] = new Availability;
		$availabilities[0]->day = "Tuesday";
		$availabilities[0]->start_time = "9:00";
		$availabilities[0]->end_time = "18:30";
		$availabilities[1] = new Availability;
		$availabilities[1]->day = "Tuesday";
		$availabilities[1]->start_time = "8:00";
		$availabilities[1]->end_time = "10:30";
		
		$this->assertFalse(EmployeeController::addNewEmployee($name, $availabilities));
	}
}
