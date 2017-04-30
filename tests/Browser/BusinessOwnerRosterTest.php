<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\BusinessOwner;
use App\Employee;
use App\Booking;
use App\Availability;

use Carbon\Carbon;

class BusinessOwnerRosterTest extends DuskTestCase
{
    /**
     * Changing the month by using the selector
     *
     * @return void
     */
    public function testChangeMonthOnRoster()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Change to the next two months from now
        $nextTwoMonths = Carbon::now('Australia/Melbourne')->addMonths(2);

        $this->browse(function ($browser) use ($bo, $nextTwoMonths) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->click('#input_month_year')
                ->click('option[value="' . $nextTwoMonths->format('m-Y') . '"]')
                ->assertPathIs('/admin/roster/' . $nextTwoMonths->format('m-Y'))
                ->assertSee($nextTwoMonths->format('F Y'));
        });
    }

    /**
     * Test whether the roster page exists
     *
     * @return void
     */
    public function testRosterPageExists()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->assertPathIs('/admin/roster/' . Carbon::now('Australia/Melbourne')->format('m-Y'))
                ->assertSee('Roster')
                ->assertSee('Add Working Times');
        });
    }

    /**
     * Test whether an employee in the system appear in the drop down box
     *
     * @return void
     */
    public function testEmployeeExistsInDropDown()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                // Look for employee selection string
                ->assertSee($employee->id . ' - ' . $employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname);
        });
    }

    /**
     * Test whether added a correct working time passes
     *
     * @return void
     */
    public function testAddWorkingTime()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->format('d-m-Y'))
                ->press('Add Working Time')

                // Check if working time is added
                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 05:00 PM');
        });
    }

    /**
     * Test whether adding two working times on the same day fails
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDaya()
    {

        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        // Set time
        $date = Carbon::now('Australia/Melbourne')->startOfMonth();

        $this->browse(function ($browser) use ($bo, $employee, $date) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '09:00')
                ->keys('#input_date', $date->format('d-m-Y'))
                ->press('Add Working Time')

                // Check if working time is added
                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 09:00 AM')

                // Go to summary page (default directory of /admin)
                ->keys('#input_start_time', '10:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', $date->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertSee('The employee can only have one working time per day.')
                ->assertDontSee('New working time has been added.');
        });
    }

    /**
     * Test whether adding two working times on the same day but on different weeks passes
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDayButDifferentWeeks()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->startOfMonth()->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 05:00 PM')

                // Go to summary page (default directory of /admin)
                ->keys('#input_start_time', '10:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->startOfMonth()->addWeek()->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 05:00 PM')
                ->assertSee('10:00 AM - 05:00 PM');
        });
    }

    /**
     * Test whether adding two working times on the same day but with different employees passes
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDayButDifferentEmployees()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates 2 employees
        $employee1 = factory(Employee::class)->create();
        $employee2 = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee1, $employee2) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee1->id)
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee($employee1->firstname . ' ' . $employee1->lastname)

                ->select('employee_id', (string)$employee2->id)
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee($employee1->firstname . ' ' . $employee1->lastname)
                ->assertSee($employee2->firstname . ' ' . $employee2->lastname);
        });
    }

    /**
     * Test putting the start time after the end time fails
     *
     * @return void
     */
    public function testStartTimeAfterEndTime()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee->id)
                ->keys('#input_start_time', '09:00')
                ->keys('#input_end_time', '07:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertDontSee('New working time has been added.')
                ->assertSee('The start time must be a date before end time.')
                ->assertSee('The end time must be a date after start time.');
        });
    }

    /**
     * Test start time and end time are the same fails
     *
     * @return void
     */
    public function testStartTimeEqualsEndTime()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee->id)
                ->keys('#input_start_time', '09:00')
                ->keys('#input_end_time', '09:00')
                ->keys('#input_date', Carbon::now('Australia/Melbourne')->format('d-m-Y'))
                ->press('Add Working Time')

                ->assertDontSee('New working time has been added.')
                ->assertSee('The start time must be a date before end time.')
                ->assertSee('The end time must be a date after start time.');
        });
    }
}
