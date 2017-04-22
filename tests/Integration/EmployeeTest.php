<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\BusinessOwner;
use App\Employee;

class EmployeeTest extends TestCase
{
    /**
     * Tests validation rules for title field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeTitleValidation()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // If user inputs nothing in the firstname field
        $employeeData = [
            'title' => ''
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The title field is required.'
        ]);


        // Is user inputs special characters
        $employeeData = [
            'title' => 'Smith(@*^*!(&'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The title format is invalid.'
        ]);
    }

     /**
     * Tests validation rules for Firstname field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeFirstnameValidation()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // If user inputs nothing in the firstname field
        $employeeData = [
            'firstname' => ''
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The firstname field is required.'
        ]);


        // Is user inputs special characters
        $employeeData = [
            'firstname' => 'Smith(@*^*!(&'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The firstname format is invalid.'
        ]);
    }

    /**
     * Tests validation rules for lastname field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeLastnameValidation()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // If user inputs nothing in the lastname field
        $employeeData = [
            'lastname' => ''
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The lastname field is required.'
        ]);


         // Is user inputs special characters
        $employeeData = [
            'lastname' => 'Smith(@*^*!(&'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The lastname format is invalid.'
        ]);
    }

    /**
     * Tests validation rules for phone field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeePhoneValidation()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();
        
        // If user inputs nothing in the phone field
        $employeeData = [
            'phone' => ''
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The phone field is required.'
        ]);


        // If user inputs less than 10 characters
        $employeeData = [
            'phone' => '12345'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The phone must be at least 10 characters.'
        ]);


        // If user inputs alphabetical characters
        $employeeData = [
            'phone' => 'abcdefghijk'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The phone format is invalid.'
        ]);


        // If user invalid characters
        $employeeData = [
            'phone' => '04%^&!123456'
        ];

        // Send request
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/employees', $employeeData);

        // Then respond with an error
        $response->assertJsonFragment([
            'The phone format is invalid.'
        ]);
    }
}
