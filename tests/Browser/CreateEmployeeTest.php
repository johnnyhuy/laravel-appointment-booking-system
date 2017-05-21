<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\BusinessOwner;
use App\Employee;

class CreateEmployeeTest extends DuskTestCase
{
    public function setUp()
    {
        parent::setUp();

        // Creates business owner
        $this->bo = factory(BusinessOwner::class)->create();
    }

    /**
     * A test to determine whether the admin employees page exists
     *
     * @return void
     */
    public function testAddEmployeeExists()
    {
        $this->browse(function ($browser) {
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
                // Go to employees page
                ->visit('/admin/employees')
                // Verify you are on employees page
                ->assertPathIs('/admin/employees')
                // Verify that Add Employee title exists on page
                ->assertSee('Create Employee');
        });
    }

    /**
     * Test show all validation errors
     *
     * @return void
     */
    public function testShowAllValidationErrors()
    {
        // Start browser
        $this->browse(function ($browser) {
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
                // Visit register page
                ->visit('/admin/employees')

                // Add employee with no data in form
                ->press('Create Employee')

                // Get the validation alerts after adding employee
                ->assertSee('The firstname field is required.')
                ->assertSee('The lastname field is required.')
                ->assertSee('The title field is required.')
                ->assertSee('The phone field is required.');
        });
    }

    /**
     * Test no employees found msg appears
     *
     * @return void
     */
    public function testNoEmployeesMessageAppears()
    {
        // Start browser
        $this->browse(function ($browser) {
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
               ->visit('/admin/employees')

                // Get the alert that no employees have been found
                ->assertSee('No employees found');
        });
    }

    /**
     * Test when admin registers an employee with valid data it works
     *
     * @return void
     */
    public function testAddEmployeeValid()
    {
        // Start browser
        $this->browse(function ($browser) {
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
               ->visit('/admin/employees')

                // Fill the form with valid employee details and submit
                ->type('title', 'Team Member')
                ->type('firstname', 'John')
                ->type('lastname', 'Doe')
                ->type('phone', '0400000000')
                ->press('Create Employee')

                // Get the alert after adding employee
                ->assertSee('New Employee Added')

                //See if employee added to employees list
                ->assertSee('Team Member')
                ->assertSee('John')
                ->assertSee('Doe');
        });
    }

    /**
     * Testing the input of the title input
     *
     * @return void
     */
    public function testTitleInputValidate()
    {
        $this->browse(function ($browser) {
            // If title is required
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->press('Create Employee')
                ->assertSee('The title field is required.')
                ->assertSee('No employees found');

            // If title is correct
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('title', 'Doctor')
                ->press('Create Employee')
                ->assertDontSee('The title format is invalid.')
                ->assertSee('No employees found');

            // If title contains numbers or special characters throw an error
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('title', 'Doctor123@#$%')
                ->press('Create Employee')
                ->assertSee('The title format is invalid.')
                ->assertSee('No employees found');

            // If title contains a ' symbol
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('title', "Doc'")
                ->press('Create Employee')
                ->assertDontSee('The title format is invalid.')
                ->assertSee('No employees found');

            // If title is less than 2 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('title', "a")
                ->press('Create Employee')
                ->assertSee('The title must be at least 2 characters.')
                ->assertSee('No employees found');

            // If title is less than 32 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('title', "LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem")
                ->press('Create Employee')
                ->assertSee('The title may not be greater than 32 characters.')
                ->assertSee('No employees found');
        });
    }


    /**
     * Testing the input of the first name field
     *
     * @return void
     */
    public function testFirstNameInputValidate()
    {
        $this->browse(function ($browser) {
            // If first name is required
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->press('Create Employee')
                ->assertSee('The firstname field is required.')
                ->assertSee('No employees found');

            // If first name is correct
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('firstname', 'John')
                ->press('Create Employee')
                ->assertDontSee('The firstname format is invalid.')
                ->assertSee('No employees found');

            // If first name contains numbers or special characters throw an error
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('firstname', 'John123@#$%')
                ->press('Create Employee')
                ->assertSee('The firstname format is invalid.')
                ->assertSee('No employees found');

            // If first name contains a ' symbol
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('firstname', "John'O")
                ->press('Create Employee')
                ->assertDontSee('The firstname format is invalid.')
                ->assertSee('No employees found');

            // If first name is less than 2 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('firstname', "a")
                ->press('Create Employee')
                ->assertSee('The firstname must be at least 2 characters.')
                ->assertSee('No employees found');

            // If first name is less than 32 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('firstname', "LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem")
                ->press('Create Employee')
                ->assertSee('The firstname may not be greater than 32 characters.')
                ->assertSee('No employees found');
        });
    }

    /**
     * Testing the input of the last name field
     *
     * @return void
     */
    public function testLastNameInputValidate()
    {
        $this->browse(function ($browser) {
            // If last name is required
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->press('Create Employee')
                ->assertSee('The lastname field is required.')
                ->assertSee('No employees found');

            // If last name contains special characters and numbers
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('lastname', 'Doe123@#$%')
                ->press('Create Employee')
                ->assertSee('The lastname format is invalid.')
                ->assertSee('No employees found');

            // If last name is less than 2 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('lastname', "a")
                ->press('Create Employee')
                ->assertSee('The lastname must be at least 2 characters.')
                ->assertSee('No employees found');

            // If last name is less than 32 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('lastname', "LoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLoremLorem")
                ->press('Create Employee')
                ->assertSee('The lastname may not be greater than 32 characters.')
                ->assertSee('No employees found');
        });
    }

    /**
     * Testing the input of the phone field
     *
     * @return void
     */
    public function testPhoneInputValidate()
    {
        // Start browser
        $this->browse(function ($browser) {
            // When a user does not enter a phone field, alert and error
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->press('Create Employee')
                ->assertSee('The phone field is required.')
                ->assertSee('No employees found');

            // Phone must be at least 10 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('phone', '0000000000')
                ->press('Create Employee')
                ->assertDontSee('The phone must be at least 10 characters.')
                ->assertSee('No employees found');

            // Phone must be less than 24 characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('phone', '000000000000000000000000000000000000000000000000000000000000')
                ->press('Create Employee')
                ->assertSee('The phone may not be greater than 24 characters.')
                ->assertSee('No employees found');

            // Phone can contain spaces
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('phone', '000 000 000 000')
                ->press('Create Employee')
                ->assertDontSee('The phone format is invalid.')
                ->assertSee('No employees found');

            // Phone contains alphabet characters
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
                ->visit('/admin/employees')
                ->type('phone', 'abcabcabcabc')
                ->press('Create Employee')
                ->assertSee('The phone format is invalid.')
                ->assertSee('No employees found');
        });
    }

    /**
     * Test form retains input
     *
     * @return void
     */
    public function testFormRetainsInput()
    {
        // Start browser
        $this->browse(function ($browser) {
            // Login as Business Owner
            $browser->loginAs($this->bo, 'web_admin')
            // Visit register page
               ->visit('/admin/employees')

                // Fill the form with invalid employee details and submit
                ->type('title', 'Title')
                ->type('firstname', 'First Name')
                ->type('lastname', 'Last Name')
                ->type('phone', 'Phone')
                ->press('Create Employee')

                // Don't get successful alert when adding employee
                ->assertDontSee('New Employee Added')

                //See if values still inside boxes
                ->assertSee('Title')
                ->assertSee('First Name')
                ->assertSee('Last Name')
                ->assertSee('Phone');
        });
    }
}
