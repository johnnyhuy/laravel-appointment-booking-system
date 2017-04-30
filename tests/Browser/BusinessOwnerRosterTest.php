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
                ->click('#inputMonthYear')
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
                ->assertPathIs('/admin/roster')
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
                ->keys('#inputStartTime', '08:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 1)
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
    public function testAddWorkingTimesOnSameDay()
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
                ->keys('#inputStartTime', '08:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 1)
                ->press('Add Working Time')

                // Check if working time is added
                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 05:00 PM')

                // Go to summary page (default directory of /admin)
                ->keys('#inputStartTime', '010:00 AM')
                ->keys('#inputEndTime', '05:00 PM')
                ->select('day', 1)
                ->select('week', 1)
                ->press('Add Working Time')

                ->assertSee('Employee can only have one working time per day.')
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
                ->keys('#inputStartTime', '08:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 1)
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee('08:00 AM - 05:00 PM')

                // Go to summary page (default directory of /admin)
                ->keys('#inputStartTime', '10:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 2)
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
                ->keys('#inputStartTime', '08:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 1)
                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee($employee1->firstname . ' ' . $employee1->lastname)

                ->select('employee_id', (string)$employee2->id)
                ->keys('#inputStartTime', '08:00')
                ->keys('#inputEndTime', '17:00')
                ->select('day', 1)
                ->select('week', 1)
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
                ->keys('#inputStartTime', '09:00')
                ->keys('#inputEndTime', '07:00')
                ->select('day', 1)
                ->select('week', 1)
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
                ->keys('#inputStartTime', '09:00')
                ->keys('#inputEndTime', '09:00')
                ->select('day', 1)
                ->select('week', 1)
                ->press('Add Working Time')

                ->assertDontSee('New working time has been added.')
                ->assertSee('The start time must be a date before end time.')
                ->assertSee('The end time must be a date after start time.');
        });
    }
}
