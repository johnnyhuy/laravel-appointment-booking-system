<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\Availability;
use App\Booking;
use App\BusinessOwner;
use App\Employee;
use App\WorkingTime;

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
                // Go to roster page (default directory of /admin)
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
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')
                ->assertPathIs('/admin/roster/' . Carbon::now('Australia/Melbourne')->format('m-Y'))
                ->assertSee('Roster')
                ->assertSee('Add Working Times');
        });
    }

    /**
     * Select an employee and show a working time on the calendar
     *
     * @return void
     */
    public function testSelectEmployeeAndSeeWorkingTime()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        // Set date
        $date = Carbon::now('Australia/Melbourne');

        // Create an existing working time
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => (string) $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date->toDateString()
        ]);

        $this->browse(function ($browser) use ($bo, $employee, $workingTime, $date) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select this month tomorrow
                ->select('month_year', $date->format('m-Y'))

                ->press('Add Working Time')

                ->assertSee($workingTime->start_time . ' - ' . $workingTime->end_time);
        });
    }

    /**
     * Test whether an employee in the system appear in the drop down box
     *
     * @return void
     */
    public function testEmployeeExistsInDropdown()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Look for employee selection string
                ->assertSee($employee->title . ' - ' . $employee->firstname . ' ' . $employee->lastname . ' - ' . (string) $employee->id);
        });
    }

    /**
     * Test whether added a correct working time passes
     *
     * @return void
     */
    public function testAddWorkingTimeSuccussful()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            // Set date to tomorrow
            $date = Carbon::now('Australia/Melbourne')->addDay();

            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Select this month tomorrow
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                // Set working times
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')

                ->press('Add Working Time')

                // Check if working time is added
                ->assertSee('New working time has been added.')

                // Select month year in case date goes to next month
                ->select('month_year', $date->format('m-Y'))

                ->assertSee('08:00 - 17:00');
        });
    }

    /**
     * Test whether adding two working times on the same day fails
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDayShowError()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        // Set date
        $date = Carbon::now('Australia/Melbourne')->startOfMonth();

        $this->browse(function ($browser) use ($bo, $employee, $date) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Type in times
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '09:00')

                // Select date
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                ->press('Add Working Time')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Go to roster page (default directory of /admin)
                ->keys('#input_start_time', '10:00')
                ->keys('#input_end_time', '17:00')

                // Select date
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

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
            $firstDate = Carbon::now('Australia/Melbourne')->addDay();
            $secondDate = Carbon::now('Australia/Melbourne')->addDay()->addWeek();

            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Select this month and day
                ->select('month_year', $firstDate->format('m-Y'))
                ->select('day', $firstDate->day)

                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')

                ->press('Add Working Time')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Select this month and day
                ->select('month_year', $secondDate->format('m-Y'))
                ->select('day', $secondDate->day)

                // Go to roster page (default directory of /admin)
                ->keys('#input_start_time', '10:00')
                ->keys('#input_end_time', '17:00')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.');
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
            // Set the date to tomorrow
            $date = Carbon::now('Australia/Melbourne')->addDay();

            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string) $employee1->id)

                // Select this month and day
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                // Set working times
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')

                ->press('Add Working Time')

                // Select employee
                ->select('employee_id', (string) $employee2->id)

                // Select this month and day
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                // Set working times
                ->keys('#input_start_time', '08:00')
                ->keys('#input_end_time', '17:00')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')

                // Select month year in case date goes to next month
                ->select('month_year', $date->format('m-Y'))

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
            // Set the date to tomorrow
            $date = Carbon::now('Australia/Melbourne')->addDay();

            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string)(string) $employee->id)

                // Select this month and day
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                // Set working times
                ->keys('#input_start_time', '09:00')
                ->keys('#input_end_time', '07:00')

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
            // Set the date to tomorrow
            $date = Carbon::now('Australia/Melbourne')->addDay();

            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Select employee
                ->select('employee_id', (string) $employee->id)

                // Select this month and day
                ->select('month_year', $date->format('m-Y'))
                ->select('day', $date->day)

                ->keys('#input_start_time', '09:00')
                ->keys('#input_end_time', '09:00')

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
    public function testEditWorkingTime()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        // Set date
        $date = Carbon::now('Australia/Melbourne')->startOfMonth();

        // Creates an employee
        $employee = factory(Employee::class)->create();

        // Create an existing working time
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => (string) $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date->toDateString()
        ]);

        $this->browse(function ($browser) use ($bo, $employee) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to roster page (default directory of /admin)
                ->visit('/admin/roster')

                // Click the edit button on the only working time
                ->click('.glyphicon-edit')

                ->press('Edit Working Time')

                ->assertSee('Edited working time has been successful.');
        });
    }
}
