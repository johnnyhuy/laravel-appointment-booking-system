<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Employee;

class EmployeeTest extends TestCase
{
    // Rollback database actions once test is complete with this trait
    use DatabaseTransactions;


    public function setUp() 
    {
        parent::setUp();

        // Generate fakedata
        $employee = factory(Employee::class)->make();
        $this->employeeData = [
            'title' => $employee->title,
            'firstname' => $employee->firstname,
            'lastname' => $employee->lastname,
            'phone' => $employee->phone,
        ];
    }

    /**
     * Tests validation rules for title field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeTitleValidation()
    {
        // If user inputs nothing in the firstname field
        $this->employeeData = ['title' => ''];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'title' => ['The title field is required.']
        ]);


         // Is user inputs special characters
        $this->employeeData = ['title' => 'Smith(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'title' => ['The title format is invalid.']
        ]);
    }

     /**
     * Tests validation rules for Firstname field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeFirstnameValidation()
    {
        // If user inputs nothing in the firstname field
        $this->employeeData = ['firstname' => ''];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname field is required.']
        ]);


         // Is user inputs special characters
        $this->employeeData = ['firstname' => 'Smith(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'firstname' => ['The firstname format is invalid.']
        ]);
    }

    /**
     * Tests validation rules for lastname field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeeLastnameValidation()
    {
        // If user inputs nothing in the lastname field
        $this->employeeData = ['lastname' => ''];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname field is required.']
        ]);


         // Is user inputs special characters
        $this->employeeData = ['lastname' => 'Smith(@*^*!(&'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'lastname' => ['The lastname format is invalid.']
        ]);
    }

    /**
     * Tests validation rules for phone field when adding a new employee
     *
     * @return void
     */
    public function testAddEmployeePhoneValidation()
    {
        // If user inputs nothing in the phone field
        $this->employeeData = ['phone' => ''];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone field is required.']
        ]);


        // If user inputs less than 10 characters
        $this->employeeData = ['phone' => '12345'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone must be at least 10 characters.']
        ]);


        // If user inputs alphabetical characters
        $this->employeeData = ['phone' => 'abcdefghijk'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone format is invalid.']
        ]);


        // If user invalid characters
        $this->employeeData = ['phone' => '04%^&!123456'];

        // Send request
        $response = $this->json('POST', '/admin/employees', $this->employeeData);

        // Then respond with an error
        $response->assertJson([
            'phone' => ['The phone format is invalid.']
        ]);


    }
}
