<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\BusinessTime;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon as Time;

class WorkingTimeTest extends TestCase
{
    /**
     * Calls functions before executing tests
     */
    public function setUp()
    {
        // Continue to run the rest of the test
        parent::setUp();

        // Create models
        $this->bo = factory(BusinessOwner::class)->create();
        $this->customer = factory(Customer::class)->create();
        $this->employee = factory(Employee::class)->create();
        $this->activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Date is tomorrow
        $this->date = Time::now()->addDay()->toDateString();

        // Add working times to all days
        foreach (getDaysOfWeek(true) as $day) {
            BusinessTime::create([
                'start_time' => '08:00:00',
                'end_time' => '17:00:00',
                'day' => $day,
            ]);
        }

        // Predefined data
        $this->wtData = [
            'employee_id' => $this->employee->id,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'date' => $this->date
        ];
    }

    /**
     * Get available working times of an employee on a date
     * A booking exists and is during a working time of an employee
     * The booking time should not be included working time
     *
     * @return void
     */
    public function testAvailableWorkingTimesForAnEmployeePerDate()
    {
        // Create a working time tomorrow from 09:00 AM to 10:00 PM
        $workingTime = WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00',
            'end_time' => '22:00',
            'date' => $this->date
        ]);

        // Create Bookings for tomorrow

        // 09:00 AM to 12:00 PM
        Booking::create([
            'customer_id' => $this->customer->id,
            'activity_id' => $this->activity->id,
            'employee_id' => $this->employee->id,
            'start_time' => '09:00',
            'end_time' => '12:00',
            'date' => $this->date
        ]);

        // 01:30 PM to 03:00 PM
        Booking::create([
            'customer_id' => $this->customer->id,
            'activity_id' => $this->activity->id,
            'employee_id' => $this->employee->id,
            'start_time' => '13:30',
            'end_time' => '15:00',
            'date' => $this->date
        ]);

        // 04:00 PM to 05:00 PM
        Booking::create([
            'customer_id' => $this->customer->id,
            'activity_id' => $this->activity->id,
            'employee_id' => $this->employee->id,
            'start_time' => '16:00',
            'end_time' => '17:00',
            'date' => $this->date
        ]);

        // 07:21 PM to 09:00 PM
        Booking::create([
            'customer_id' => $this->customer->id,
            'activity_id' => $this->activity->id,
            'employee_id' => $this->employee->id,
            'start_time' => '19:21',
            'end_time' => '21:00',
            'date' => $this->date
        ]);

        $avaTimes = $this->employee->availableTimes($this->date);

        // There should be a available time from 12:00 PM to 01:00 PM
        $this->assertEquals($avaTimes[1]['start_time'], '12:00');
        $this->assertEquals($avaTimes[1]['end_time'], '13:30');

        // There should be a available time from 03:00 PM to 04:00 PM
        $this->assertEquals($avaTimes[2]['start_time'], '15:00');
        $this->assertEquals($avaTimes[2]['end_time'], '16:00');

        // There should be a available time from 05:00 PM to 07:21 PM
        $this->assertEquals($avaTimes[3]['start_time'], '17:00');
        $this->assertEquals($avaTimes[3]['end_time'], '19:21');

        // There should be a available time from 09:00 PN to 10:00 PM
        $this->assertEquals($avaTimes[4]['start_time'], '21:00');
        $this->assertEquals($avaTimes[4]['end_time'], '22:00');
    }

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
        // Given there is an employee
        $employee = factory(Employee::class)->create();

        // and there are 4 working times from the employee
        $workingTimes = factory(WorkingTime::class, 4)->create([
            'employee_id' => $this->employee->id,
        ]);

        // Working time must have only one employee
        $this->assertEquals(4, count($this->employee->workingTimes));
    }

    /**
     * Add working time for employee
     *
     * @return void
     */
    public function testAddWorkingTimeForEmployee()
    {
    	// Create employee
    	$employee = factory(Employee::class)->create();

    	// Create working time data
    	// and add a two hour shift next week
    	$workingTimeData = [
    		'employee_id' => $this->employee->id,
    		'start_time' => Time::now('Australia/Melbourne')
    			->startOfDay()
    			->addHours(13)
    			// Format time to 24 hour HH:MM
    			->format('H:i'),
    		'end_time' => Time::now('Australia/Melbourne')
    			->startOfDay()
    			->addHours(15)
    			// Format time to 24 hour HH:MM
    			->format('H:i'),
    		'date' => Time::now('Australia/Melbourne')
                ->addMonth()
    			->addWeek()
    			->startOfWeek()
    			// Format to date string yyyy-mm-dd
    			->toDateString(),
    	];

    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

    	// Check for a session message
        $response->assertSessionHas('message', 'New working time has been added.');

        // Check if working time is in database
        $this->assertDatabaseHas('working_times', [
        	// Choose ID 1 since there must be only one working time in the table
        	'id' => 1
        ]);
    }

    /**
     * Business Owner add booking validation rules
     *
     * @return void
     */
    public function testAdminCreateWorkingTimeValidation()
    {
        // User selects no employee
        // Build booking data
        $this->wtData['employee_id'] = '';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/roster', $this->wtData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee field is required.'
        ]);


        // User inputs invalid start time
        // Build booking data
        $this->wtData['start_time'] = '@#@#___2323:00';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/roster', $this->wtData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time field must be in the correct time format.'
        ]);


        // User inputs invalid end time
        // Build booking data
        $this->wtData['end_time'] = '@#@#___2323:00';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/roster', $this->wtData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time field must be in the correct time format.'
        ]);


        // Working time is 3 days before today
        // Build booking data
        $this->wtData['date'] = Time::now()->subDays(3)->toDateString();

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/roster', $this->wtData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The date must be before today ' . Time::now()->format('d/m/Y') . '.'
        ]);
    }

    /**
     * See if an employee working time
     * is witin the business times
     *
     * @return void
     */
    public function testEmployeeWorkingTimeIsNotInBusinessTime()
    {
        // Create employee
        $employee = factory(Employee::class)->create();

        // Create working time data
        // working start time is before business start time
        // working end time is before business end time
        $workingTimeData = [
            'employee_id' => $this->employee->id,
            'start_time' => '00:00',
            'end_time' => '01:00',
            'date' => $this->date
        ];

        // Send a POST request to /admin/roster with working time data
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

        // Check for a validation error
        $response->assertJsonFragment([
            'The date field be within open business times.'
        ]);

        // Create working time data
        // working start time is before business start time
        // working end time is after business start time
        $workingTimeData = [
            'employee_id' => $this->employee->id,
            'start_time' => '00:00',
            'end_time' => '09:00',
            'date' => $this->date
        ];

        // Send a POST request to /admin/roster with working time data
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

        // Check for a validation error
        $response->assertJsonFragment([
            'The date field be within open business times.'
        ]);

        // Create working time data
        // working start time is after business start time
        // working end time is after business end time
        $workingTimeData = [
            'employee_id' => $this->employee->id,
            'start_time' => '08:00',
            'end_time' => '22:00',
            'date' => $this->date
        ];

        // Send a POST request to /admin/roster with working time data
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

        // Check for a validation error
        $response->assertJsonFragment([
            'The date field be within open business times.'
        ]);

        // Create working time data
        // working start time is after business end time
        // working end time is after business end time
        $workingTimeData = [
            'employee_id' => $this->employee->id,
            'start_time' => '17:00',
            'end_time' => '22:00',
            'date' => $this->date
        ];

        // Send a POST request to /admin/roster with working time data
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

        // Check for a validation error
        $response->assertJsonFragment([
            'The date field be within open business times.'
        ]);
    }

    /**
     * Working time edit success
     *
     * @return void
     */
    public function testEditWorkingTime()
    {
        // Create a working time from 09:00 AM to 05:00 PM today
        $wTime = WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'date' => Time::now('Australia/Melbourne')
                ->toDateString(),
        ]);

        // Create a booking that starts at 11:00 AM to 2:00 PM today
        $booking = factory(Booking::class)->create([
            'employee_id' => $wTime->employee_id,
            'start_time' => '11:00:00',
            'end_time' => '14:00:00',
            'date' => Time::now('Australia/Melbourne')
                ->toDateString(),
        ]);

        // Build working time data
        $wTimeData = [
            'employee_id' => $wTime->employee_id,
            'start_time' => '09:00',
            'end_time' => '17:00',
        ];

        // Send a PUT request to /admin/roster/{id} with working time data
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('PUT', '/admin/roster/' . $wTime->employee_id, $wTimeData);

        // Check for a session message
        $response->assertSessionHas('message', 'Working time successfully edited.');

        // Booking employee ID should be null after edit
        $this->assertEquals(null, Booking::find($booking->id));
    }

    /**
     * Test all fields that are required
     *
     * @return void
     */
    public function testAllFieldsThatAreRequired()
    {
        // Build working time data
        $workingTimeData = [
            'employee_id' => '',
            'start_time' => '',
            'end_time' => '',
            'date' => '',
        ];

    	// Send a POST request to /admin/roster with nothing
    	$response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

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
    	// When time fields are not in time format
    	$workingTimeData = [
    		'start_time' => 'johndoe',
    		'end_time' => 'johndoe',
    	];

    	// Send a POST request to /admin/roster with nothing
    	$response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

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
    	// Create working time data
    	// and add a one hour shift
    	$workingTimeData = [
    		'start_time' => Time::now('Australia/Melbourne')
    			->startOfDay()
    			->addHours(16)
    			->format('H:i'),
    		'end_time' => Time::now('Australia/Melbourne')
    			->startOfDay()
    			->addHours(15)
    			->format('H:i'),
    	];

    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', $workingTimeData);

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
    	// Send a POST request to /admin/roster with working time data
    	$response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', ['employee_id' => 1337]);

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
        // and add two minutes
        $laterWorkingTime = factory(WorkingTime::class)->create([
            'start_time' => Time::now('Australia/Melbourne')
                ->addMinutes(2)
                ->toTimeString(),
            'date' => Time::now('Australia/Melbourne')
                ->addMonth()
                ->startOfMonth()
                ->toDateString(),
        ]);

        // Create a working time at the start of the month
        // and add one minute
        $earlierWorkingTime = factory(WorkingTime::class)->create([
            'start_time' => Time::now('Australia/Melbourne')
                ->addMinute()
                ->toTimeString(),
            'date' => Time::now('Australia/Melbourne')
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
            'date' => Time::now('Australia/Melbourne')
                ->addMonth()
                ->startOfMonth()
                ->toDateString(),
        ]);

        // Create a working time at the end of the month
        factory(WorkingTime::class)->create([
            'date' => Time::now('Australia/Melbourne')
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
        // Create a working time
        // Two hour shift 1:00PM - 3:00PM
        // Date is within next month from today
        $workingTime = factory(WorkingTime::class)->create([
            'start_time' => Time::now('Australia/Melbourne')
                ->startOfDay()
                ->addHours(13)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'end_time' => Time::now('Australia/Melbourne')
                ->startOfDay()
                ->addHours(15)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'date' => Time::now('Australia/Melbourne')
                ->addMonth()
                ->toDateString(),
        ]);

        // Send a POST request to /admin/roster with working time data
        // Assign to the same employee
        // Two hour shift 1:00PM - 3:00PM
        // Use the same working time date as the factory
        $response = $this->actingAs($this->bo, 'web_admin')->json('POST', '/admin/roster', [
            'employee_id' => $workingTime->employee->id,
            'start_time' => Time::now('Australia/Melbourne')
                ->startOfDay()
                ->addHours(13)
                // Format time to 24 hour HH:MM
                ->format('H:i'),
            'end_time' => Time::now('Australia/Melbourne')
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

    /**
     * Business owner remove working time
     *
     * @return void
     */
    public function testRemoveWorkingTime()
    {
        // Set date to tomorrow
        $date = Time::now()->addDay()->toDateString();

        // Set date before
        $dateBefore = Time::now()->subDays(4)->toDateString();

        // Customer
        $customer = factory(Customer::class)->create();

        // Employee
        $employee = factory(Employee::class)->create();

        // Activity
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        $wTime = WorkingTime::create([
            'employee_id' => $employee->id,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'date' => $date
        ]);

        // Create a booking tomorrow
        Booking::create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'date' => $date
        ]);

        // Create a booking tomorrow
        Booking::create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'date' => $date
        ]);

        // Create a booking tomorrow
        Booking::create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => '11:00:00',
            'end_time' => '13:00:00',
            'date' => $dateBefore
        ]);

        // Send PUT/PATCH request to admin/activity/{activity}
        $response = $this->actingAs($this->bo, 'web_admin')->json('DELETE', 'admin/roster/' . $wTime->id);

        // Check edit activity success message
        $response->assertSessionHas('message', 'Working time successfully removed.');

        // There should be one booking from before
        $this->assertCount(1, Booking::all());
    }
}