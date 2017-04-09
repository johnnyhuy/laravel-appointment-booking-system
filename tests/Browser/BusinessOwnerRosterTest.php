<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\BusinessOwner;
use App\Employee;
use App\Booking;
use App\Availability;

class BusinessOwnerRosterTest extends DuskTestCase
{
    /**
     * Test whether the roster page exists
     *
     * @return void
     */
    public function testRosterPageExists()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
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
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                //Look for employee selection string
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
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#inputStartTime', '08:00 AM')
                ->keys('#inputEndTime', '09:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee(\Carbon\Carbon::parse('08:00 AM')->format('h:i A') . ' - ' . \Carbon\Carbon::parse('09:00 PM')->format('h:i A'));
        });
    }

    /**
     * Test whether adding two working times on the same day fails
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDay()
    {
      
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#inputStartTime', '08:00 AM')
                ->keys('#inputEndTime', '09:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee(\Carbon\Carbon::parse('08:00 AM')->format('h:i A') . ' - ' . \Carbon\Carbon::parse('09:00 PM')->format('h:i A'))

                //Go to summary page (default directory of /admin)
                ->keys('#inputStartTime', '010:00 AM')
                ->keys('#inputEndTime', '05:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

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
      
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->keys('#inputStartTime', '08:00 AM')
                ->keys('#inputEndTime', '09:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee(\Carbon\Carbon::parse('08:00 AM')->format('h:i A') . ' - ' . \Carbon\Carbon::parse('09:00 PM')->format('h:i A'))

                //Go to summary page (default directory of /admin)
                ->keys('#inputStartTime', '10:00 AM')
                ->keys('#inputEndTime', '05:00 PM')
                ->select('day', 'Monday')
                ->select('week', '2')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee(\Carbon\Carbon::parse('08:00 AM')->format('h:i A') . ' - ' . \Carbon\Carbon::parse('09:00 PM')->format('h:i A'))
                ->assertSee(\Carbon\Carbon::parse('10:00 AM')->format('h:i A') . ' - ' . \Carbon\Carbon::parse('05:00 PM')->format('h:i A'));
        });
    }

    /**
     * Test whether adding two working times on the same day but with different employees passes
     *
     * @return void
     */
    public function testAddWorkingTimesOnSameDayButDifferentEmployees()
    {     
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates 2 employees
        $employee1 = factory(Employee::class)->create();
        $employee2 = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee1, $employee2) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee1->id)
                ->keys('#inputStartTime', '08:00 AM')
                ->keys('#inputEndTime', '09:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

                ->press('Add Working Time')

                ->assertSee('New working time has been added.')
                ->assertSee($employee1->firstname . ' ' . $employee1->lastname)

                ->select('employee_id', (string)$employee2->id)
                ->keys('#inputStartTime', '08:00 AM')
                ->keys('#inputEndTime', '09:00 PM')
                ->select('day', 'Monday')
                ->select('week', '1')

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
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee->id)
                ->keys('#inputStartTime', '0900AM')
                ->keys('#inputEndTime', '0700AM')
                ->select('day', 'Monday')
                ->select('week', '1')

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
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Creates an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/roster')
                ->select('employee_id', (string)$employee->id)
                ->keys('#inputStartTime', '0900AM')
                ->keys('#inputEndTime', '0900AM')
                ->select('day', 'Monday')
                ->select('week', '1')

                ->press('Add Working Time')

                ->assertDontSee('New working time has been added.')
                ->assertSee('The start time must be a date before end time.')
                ->assertSee('The end time must be a date after start time.');
        });
    }
}
