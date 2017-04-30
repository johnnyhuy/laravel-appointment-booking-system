<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Customer;
use App\BusinessOwner;
use App\Booking;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class WorkingTimeTest extends TestCase
{
    /**
     * Add working time for employee
     *
     * @return void
     */
    public function testWorkingTimeBelongsToOneEmployee()
    {
        // Given there is a working time
        $workingTime = factory(WorkingTime::class)->create();

        // Working time must have only one employee
        $this->assertEquals(1, count($workingTime->employee));
    }

    /**
     * Add working time for employee
     *
     * @return void
     */
    public function testEmployeeHasManyWorkingTimes()
    {
        // Given there is aan employee
        $employee = factory(Employee::class)->create();

        // and there are 20 working times from the employee
        $workingTimes = factory(WorkingTime::class, 20)->create([
            'employee_id' => $employee->id,
        ]);

        // Working time must have only one employee
        $this->assertEquals(20, count($employee->workingTimes));
    }

    /**
     * Add working time for employee
     *
     * @return void
     */
    public function testAddWorkingTimeForEmployee()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

    	// Create employee
    	$employee = factory(Employee::class)->create();

    	// Create working time data
    	// and add a two hour shift next week
    	$workingTimeData = [
    		'employee_id' => $employee->id,
    		'start_time' => Carbon::now()
    			->startOfDay()
    			->addHours(13)
    			// Format time to 24 hour HH:MM
    			->format('H:i'),
    		'end_time' => Carbon::now()
    			->startOfDay()
    			->addHours(15)
    			// Format time to 24 hour HH:MM
    			->format('H:i'),
    		'date' => Carbon::now()
                ->addMonth()
    			->addWeek()
    			->startOfWeek()
    			// Format to date string yyyy-mm-dd
    			->toDateString(),
    	];

    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Check for a session message
        $response->assertSessionHas('message', 'New working time has been added.');

        // Check if working time is in database
        $this->assertDatabaseHas('working_times', [
        	// Choose ID 1 since there must be only one working time in the table
        	'id' => 1
        ]);
    }

    /**
     * Working time edit success
     *
     * @return void
     */
    public function testEditWorkingTimeSuccessful()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create a working time from 09:00 AM to 05:00 PM today
        $workingTime = factory(WorkingTime::class)->create([
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => Carbon::now()
                ->toDateString(),
        ]);

        // Create a booking that starts at 11:00 AM to 2:00 PM today
        $booking = factory(Booking::class)->create([
            'employee_id' => $workingTime->employee_id,
            'start_time' => '11:00',
            'end_time' => '14:00',
            'date' => Carbon::now()
                ->toDateString(),
        ]);

        // Build working time data
        $workingTimeData = [
            'employee_id' => $workingTime->employee_id,
            'start_time' => $workingTime->start_time,
            'end_time' => $workingTime->end_time,
            'date' => $workingTime->date,
        ];

        // Send a PUT request to /admin/roster/{id} with working time data
        $response = $this->actingAs($bo, 'web_admin')
            ->json('PUT', '/admin/roster/' . $workingTime->employee_id, $workingTimeData);

        // Check for a session message
        $response->assertSessionHas('message', 'Edited working time has been successful.');

        // Booking employee ID should be null after edit
        $this->assertEquals(null, Booking::find($booking->id)->employee_id);
    }

    /**
     * Test all fields that are required
     *
     * @return void
     */
    public function testAllFieldsThatAreRequired()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Build working time data
        $workingTimeData = [
            'employee_id' => '',
            'start_time' => '',
            'end_time' => '',
            'date' => '',
        ];

    	// Send a POST request to /admin/roster with nothing
    	$response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Check if errors occured
        $response->assertJson([
        	'employee_id' => ['The employee field is required.'],
        	'start_time' => ['The start time field is required.'],
        	'end_time' => ['The end time field is required.'],
        	'date' => ['The date field is required.'],
        ]);
    }

    /**
     * Start and end time fields must be in a time format
     *
     * @return void
     */
    public function testStartTimeAndEndTimeFieldsMustBeATimeFormat()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

    	// When time fields are not in time format
    	$workingTimeData = [
    		'start_time' => 'johndoe',
    		'end_time' => 'johndoe',
    	];

    	// Send a POST request to /admin/roster with nothing
    	$response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Find in JSON response for error
        $response->assertJsonFragment(['The start time field must be in the correct time format.']);
        $response->assertJsonFragment(['The end time field must be in the correct time format.']);
    }

    /**
     * If start time is later than end time then respond with an error
     *
     * @return void
     */
    public function testErrorIfStartTimeIsLaterThanEndTime()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

    	// Create working time data
    	// and add a one hour shift
    	$workingTimeData = [
    		'start_time' => Carbon::now()
    			->startOfDay()
    			->addHours(16)
    			->format('H:i'),
    		'end_time' => Carbon::now()
    			->startOfDay()
    			->addHours(15)
    			->format('H:i'),
    	];

    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Find in JSON response for error
        $response->assertJsonFragment([
        	'The start time must be a date before end time.'
        ]);
        $response->assertJsonFragment([
        	'The end time must be a date after start time.'
        ]);
    }

    /**
     * If employee does not exist then respond with an error
     *
     * @return void
     */
    public function testErrorIfEmployeeDoesNotExist()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

    	// Create working time data
    	// and set employee ID to 1 (non-existant)
    	$workingTimeData = [
    		'employee_id' => 1
    	];

    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Find in JSON response for error
        $response->assertJsonFragment([
        	'The employee does not exist.'
        ]);
    }

    /**
     * Get the roster and test if roster is sorted by start time of each working time
     *
     * @return void
     */
    public function testGetRosterIsSortedByStartTime()
    {
        // Create a working time at the start of the month
        // and add two hours
        $laterWorkingTime = factory(WorkingTime::class)->create([
            'start_time' => Carbon::now()
                ->addHours(2)
                ->toTimeString(),
            'date' => Carbon::now()
                ->addMonth()
                ->startOfMonth()
                ->toDateString(),
        ]);

        // Create a working time at the start of the month
        // and add one hour
        $earlierWorkingTime = factory(WorkingTime::class)->create([
            'start_time' => Carbon::now()
                ->addHour()
                ->toTimeString(),
            'date' => Carbon::now()
                ->addMonth()
                ->startOfMonth()
                ->toDateString(),
        ]);

        // Check get roster first object returns the earlier working time
        $this->assertEquals(WorkingTime::getRoster()->first()->start_time, $earlierWorkingTime->start_time);
    }

    /**
     * Get the roster of working times for next month
     *
     * @return void
     */
    public function testGetRosterOfNextMonth()
    {
        // Create a working time at the start of next month
        factory(WorkingTime::class)->create([
            'date' => Carbon::now()
                ->addMonth()
                ->startOfMonth()
                ->toDateString(),
        ]);

        // Create a working time at the end of the month
        factory(WorkingTime::class)->create([
            'date' => Carbon::now()
                ->addMonth()
                ->endOfMonth()
                ->toDateString(),
        ]);

        // Assert that all 20 working times is shown
        $this->assertEquals(2, count(WorkingTime::getRoster()));
    }

    /**
     * Employee can only have one booking per day
     *
     * @return void
     */
    public function testEmployeeCanOnlyHaveOneWorkingTimePerDay()
    {
        // Login as a business owner
        $bo = factory(BusinessOwner::class)->create();

        // Create a working time
        // Two hour shift 1:00PM - 3:00PM
        // Date is within next month from today
        $workingTime = factory(WorkingTime::class)->create([
            'start_time' => Carbon::now()
                ->startOfDay()
                ->addHours(13)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'end_time' => Carbon::now()
                ->startOfDay()
                ->addHours(15)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'date' => Carbon::now()
                ->addMonth()
                ->toDateString(),
        ]);

        // Send a POST request to /admin/roster with working time data
        // Assign to the same employee
        // Two hour shift 1:00PM - 3:00PM
        // Use the same working time date as the factory
        $response = $this->actingAs($bo, 'web_admin')->json('POST', '/admin/roster', [
            'employee_id' => $workingTime->employee->id,
            'start_time' => Carbon::now()
                ->startOfDay()
                ->addHours(13)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'end_time' => Carbon::now()
                ->startOfDay()
                ->addHours(15)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'date' => $workingTime->date,
        ]);

        // Find in JSON response for error
        $response->assertJsonFragment([
            'The employee can only have one working time per day.'
        ]);

        // If session message exists, then fail
        // Message in session is the success message shown after adding a working time
        $response->assertSessionMissing('message');

        // There must be only 1 working time (no duplicates)
        $this->assertCount(1, WorkingTime::all());
    }
}