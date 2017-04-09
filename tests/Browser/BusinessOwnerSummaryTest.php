<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

use App\BusinessOwner;
use App\Employee;
use App\Booking;
use App\Availability;

class BusinessOwnerSummaryTest extends DuskTestCase
{
    /**
     * Test whether the summary page exists
     *
     * @return void
     */
    public function testSummaryPageExists()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                ->assertPathIs('/admin/summary')
                ->assertSee('Summary of Bookings')
                ->assertSee('Employee Availability');
        });
    }

    /**
     * Test whether the correct information is displayed in the summary field
     *
     * @return void
     */
    public function testSingleEntryDisplayed()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create a booking for yesterday
        $booking = factory(Booking::class)->create([
            'date' => \Carbon\Carbon::now()->addDay(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                // //Check for the booking ID
                ->assertSee($booking->id)
                //Check for full name on page
                ->assertSee($booking->customer->firstname . " " . $booking->customer->lastname)
                //Check for start time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether future entries are not stored in history
     *
     * @return void
     */
    public function testPastEntriesNotDisplayed() 
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create a booking for yesterday
        $booking = factory(Booking::class)->create([
            'date' => \Carbon\Carbon::now()->subDay(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for full name on page
                ->assertDontSee($booking->customer->firstname . " " . $booking->customer->lastname)
                //Check for start time in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether multiple entries are displayed at the same time
     *
     * @return void
     */
    public function testMultipleEntriesDisplayed() 
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create a booking for tommorow
        $booking1 = factory(Booking::class)->create([
            'date' => \Carbon\Carbon::now()->addDay(),
        ]);
        //Create a booking for 2 days from now
        $booking2 = factory(Booking::class)->create([
            'date' => \Carbon\Carbon::now()->addDay()->addDay(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking1, $booking2) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')

                //Check for first booking
                //Check for the booking ID
                ->assertSee($booking1->id)
                //Check for full name on page
                ->assertSee($booking1->customer->firstname . " " . $booking1->customer->lastname)
                //Check for start time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking1->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking1->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking1->date)->format('d/m/y'))

                //Check for second booking
                // //Check for the booking ID
                ->assertSee($booking2->id)
                //Check for full name on page
                ->assertSee($booking2->customer->firstname . " " . $booking2->customer->lastname)
                //Check for start time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking2->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking2->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking2->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether a booking for the current day is displayed
     *
     * @return void
     */
    public function testCurrentDayEntryIsDisplayed()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create a booking for today
        $booking = factory(Booking::class)->create([
            'date' => \Carbon\Carbon::now(),
            'start_time' => \Carbon\Carbon::now(),
            'end_time' => \Carbon\Carbon::now()->addHour(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for the booking ID
                ->assertSee($booking->id)
                //Check for full name on page
                ->assertSee($booking->customer->firstname . " " . $booking->customer->lastname)
                //Check for start time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertSee(\Carbon\Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether a booking far in the future (more than 7 days, is displayed)
     *
     * @return void
     */
    public function testFarAwayBookingsNotDisplayed()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create a booking for today
        $booking = factory(Booking::class)->create( [
            'date' => \Carbon\Carbon::now()->addMonth()
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for full name on page
                ->assertDontSee($booking->customer->firstname . " " . $booking->customer->lastname)
                //Check for start time in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->start_time)->format('H:i'))
                //Check for the end time in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->end_time)->format('H:i'))
                //Check for the date in the proper format
                ->assertDontSee(\Carbon\Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether an employee is displayed under the Employee Availability table
     *
     * @return void
     */
    public function testDisplayEmployeeAvailability()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create an employee
        $employee = factory(Employee::class)->create();

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for full name on page
                ->assertSee($employee->firstname . " " . $employee->lastname)
                //Assert Employe isn't available
                ->assertSee('Not Available');
        });
    }

    /**
     * Tests employee is displayed with relevant availabilities
     *
     * @return void
     */
    public function testDisplayEmployeeWithAvailabilities()
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create an employee
        $employee = factory(Employee::class)->create();

        //Create Availabilities for Employee
        $a1 = factory(Availability::class)->create();
        $a2 = factory(Availability::class)->create( ['day' => 'tuesday', 'end_time' => '20:00:00' ] );

        $this->browse(function ($browser) use ($bo, $employee, $a1, $a2) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for full name on page
                ->assertSee($employee->firstname . " " . $employee->lastname)
                //Assert Employe isn't available for days where they are unavailable
                ->assertSee('Not Available')
                //Assert Availabilites appear
                ->assertSee(\Carbon\Carbon::parse($a1->start_time)->format('h:i A') . " - " . 
                     \Carbon\Carbon::parse($a1->end_time)->format('h:i A'))
                ->assertSee(\Carbon\Carbon::parse($a2->start_time)->format('h:i A') . " - " . 
                     \Carbon\Carbon::parse($a2->end_time)->format('h:i A'));
        });
    }

    /**
     * Tests employee is displayed with no not availables
     *
     * @return void
     */
    public function testDisplayEmployeeWithNoNotAvailables() 
    {
        //Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        //Create an employee
        $employee = factory(Employee::class)->create();

        //Create Availabilities for Employee
        $a1 = factory(Availability::class)->create();
        $a2 = factory(Availability::class)->create( ['day' => 'tuesday'] );
        $a3 = factory(Availability::class)->create( ['day' => 'wednesday'] );
        $a4 = factory(Availability::class)->create( ['day' => 'thursday'] );
        $a5 = factory(Availability::class)->create( ['day' => 'friday'] );
        $a6 = factory(Availability::class)->create( ['day' => 'saturday'] );
        $a7 = factory(Availability::class)->create( ['day' => 'sunday'] );

        $this->browse(function ($browser) use ($bo, $employee) {
            //Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                //Go to summary page (default directory of /admin)
                ->visit('/admin/summary')
                //Check for full name on page
                ->assertSee($employee->firstname . " " . $employee->lastname)
                //Assert Employe isn't available for days where they are unavailable
                ->assertDontSee('Not Available');
        });
    }
    
}
