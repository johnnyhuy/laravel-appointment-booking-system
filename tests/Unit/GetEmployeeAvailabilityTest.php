<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Employee;
use App\Availability;

class GetEmployeeAvailabilityTest extends TestCase
{
	use DatabaseTransactions;
    
	public function testGetEmployeeAvailabilityPass() {
		//Given an employee with 2 available days, the get employee availablility
		//Should return the employee's 2 available days
		$employee = factory(Employee::class)->create();
		$availablility1 = factory(Availability::class)->create([
			'employee_id' => $employee->id
		]);
		$availablility2 = factory(Availability::class)->create([
			'employee_id' => $employee->id
		]);
		
		$this->assertCount(2, Employee::GetEmployeeAvailability('', $employee->id)); 
	}
	
	public function testGetEmployeeAvailabilityMultipleEmployees() {
		//Given 2 employees with different days available, the returned availablility
		//of the first employee should not include the availablility of the 2nd employee
		
		$employee1 = factory(Employee::class)->create();
		$availablilityE1_1 = factory(Availability::class)->create([
			'employee_id' => $employee1->id
		]);
		$availablilityE1_2 = factory(Availability::class)->create([
			'employee_id' => $employee1->id
		]);
		
		$employee2 = factory(Employee::class)->create();
		$availablilityE2_1 = factory(Availability::class)->create([
			'employee_id' => $employee2->id
		]);
		
		$this->assertCount(2, Employee::GetEmployeeAvailability('', $employee1->id)); 
	}
}
