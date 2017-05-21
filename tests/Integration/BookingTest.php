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

use Carbon\Carbon;

class BookingTest extends TestCase
{
    /**
     * Calls functions before executing tests
     */
    public function setUp()
    {
        // Continue to run the rest of the test
        parent::setUp();

        // Build fake data
        $this->bo = factory(BusinessOwner::class)->create();

        // Get the tomorrow date
        $this->date = Carbon::now()->addDay()->toDateString();

        // Create fake data that adds to database
        $this->employee = factory(Employee::class)->create();
        $this->customer = factory(Customer::class)->create();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 5:00 PM
        WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00:00',
            'end_time' => '17:00:00',
            'date' => $this->date,
        ]);

        // Create an activity that is 2 hours
        $this->activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Booking start time is 09:00 AM in 24 hour format
        $startTime = '09:00';

        // Calculate end time given by activity duration
        $endTime = Booking::calcEndTime($this->activity->duration, $startTime);

        // Add a booking from activity duration
        // Build booking data
        $this->bData = [
            'customer_id' => $this->customer->id,
            'employee_id' => $this->employee->id,
            'activity_id' => $this->activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $this->date,
        ];
    }

    /**
     * Customer successfully create a booking
     *
     * @return void
     */
    public function testCustomerCreateBookingSuccessful()
    {
        // Build booking data
        // Booking is set to tomorrow at 10:00 AM
        $this->bData['start_time'] = '10:00';

        // Let end time be calculated
        $this->bData['end_time'] = null;

        // Send POST request to /bookings
        $response = $this->actingAs($this->customer, 'web_user')
            ->json('POST', 'bookings', $this->bData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');

        // Check the database if booking exists
        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'customer_id' => $this->customer->id,
            'employee_id' => $this->employee->id,
            'activity_id' => $this->activity->id,
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'date' => $this->date,
        ]);
    }

    /**
     * Customer has many bookings, make 4 bookings and assign it to a customer
     *
     * @return void
     */
    public function testCustomerHasManyBookings()
    {
        // Given customer created
        $customer = factory(Customer::class)->create();

        // When customer has 4 bookings
        factory(Booking::class, 4)->create([
            'customer_id' => $customer->id,
        ]);

        // Then there exists 4 bookings from customer
        $this->assertCount(4, $customer->bookings);
    }

    /**
     * Creating a booking through the Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminCreateBookingSuccessful()
    {
        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');

        // Check the database if booking exists
        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'customer_id' => $this->customer->id,
            'employee_id' => $this->employee->id,
            'activity_id' => $this->activity->id,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'date' => $this->date,
        ]);
    }

    /**
     * Business Owner add booking validation rules
     *
     * @return void
     */
    public function testAdminCreateBookingValidation()
    {
        // User selects no customer
        // Build booking data
        $this->bData['customer_id'] = '';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The customer field is required.'
        ]);


        // User selects a customer that does not exist
        // Build booking data
        $this->bData['customer_id'] = 1337;

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The customer does not exist.'
        ]);


        // User selects no activity
        // Build booking data
        $this->bData['activity_id'] = '';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity field is required.'
        ]);


        // User selects a activity that does not exist
        // Build booking data
        $this->bData['activity_id'] = 1337;

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity does not exist.'
        ]);


        // User selects a employee that does not exist
        // Build booking data
        $this->bData['employee_id'] = 1337;

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee does not exist.'
        ]);


        // User inputs invalid start time
        // Build booking data
        $this->bData['start_time'] = '@@@';

        // Send POST request to admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time field must be in the correct time format.'
        ]);
    }

    /**
     * Create an existing booking and assign it to an employee
     * Send a request to create another booking and try to assign it to an employee
     * If both bookings have the employee working at the same time
     * Then show an error
     *
     * @return void
     */
    public function testEmployeeIsAlreadyWorkingOnBooking()
    {
        // Create an acitivity with a duration of 2 hours
        $activity = factory(Activity::class)->create([
            'duration' => '02:00',
        ]);

        // Get date in the next three days
        $date = Carbon::now('AEST')->addDays(3)->toDateString();

        // Create a working time for given employee today
        // Employee starts at 6:00 AM to 5:00 PM
        WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '06:00:00',
            'end_time' => '17:00:00',
            'date' => $date,
        ]);

        // Build booking data
        $this->bData['activity_id'] = $activity->id;
        $this->bData['date'] = $date;

        // Start time is 10:00 AM
        $this->bData['start_time'] = '10:00';

        // End time is 12:00 PM
        $this->bData['end_time'] = '12:00';


        // There exists a booking
        $booking = Booking::create([
            'customer_id' => $this->bData['customer_id'],
            'employee_id' => $this->bData['employee_id'],
            'activity_id' => $this->bData['activity_id'],
            'start_time' => toTime($this->bData['start_time']),
            'end_time' => toTime($this->bData['end_time']),
            'date' => $this->bData['date'],
        ]);

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);


        // When booking starts before start time and finishes before end time
        $this->bData['start_time'] = '09:00';
        $this->bData['end_time'] = '11:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);


        // When booking starts after start time and finishes after end time
        $this->bData['start_time'] = '11:00';
        $this->bData['end_time'] = '12:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);

        // When booking working on booking starts before existing booking
        $this->bData['start_time'] = '08:00';
        $this->bData['end_time'] = '10:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');


        // When employee working on booking starts after existing booking
        $this->bData['start_time'] = Carbon::parse($booking->start_time)
            ->addHours($activity->hour)
            ->addMinutes($activity->minute)
            ->format('H:i');
        $this->bData['end_time'] = Carbon::parse($booking->end_time)
            ->addHours($activity->hour)
            ->addMinutes($activity->minute)
            ->format('H:i');

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');
    }

    /**
     * When user attempts to add a booking activity duration that goes to the next day
     * Then show an error
     *
     * @return void
     */
    public function testBookingAddActivityDurationWhereBookingEndTimeIsInvalid()
    {
        $this->bData['start_time'] = '23:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity duration added on start time is invalid. Please add a start time that does not go to the next day.'
        ]);
    }

    /**
     * When user adds a booking
     * If the employee is not working in that time
     * Then show an error
     *
     * @return void
     */
    public function testAddBookingWhenEmployeeIsNotWorking() {
        // Next two days
        $date = Carbon::now('AEST')->addDays(2)->toDateString();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 11:00 AM
        $workingTime = WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00:00',
            'end_time' => '11:00:00',
            'date' => $date,
        ]);

        // Booking starts at 1:00 PM
        $startTime = '13:00';

        // Calculate the end time depending on the activity duration
        $endTime = '15:00';

        // Build booking data
        $this->bData['date'] = $date;
        $this->bData['start_time'] = '13:00';
        $this->bData['end_time'] = '15:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is not working on that time.'
        ]);
    }

    /**
     * When a booking starts before a start working time
     *
     * @return void
     */
    public function testBookingStartsBeforeWorkingTime()
    {
        // Create an activity that is 4 hours
        $activity = factory(Activity::class)->create([
            'duration' => '04:00'
        ]);

        // Get the current date
        $date = Carbon::now('AEST')->addDays(2)->toDateString();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 5:00 PM
        $workingTime = WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date,
        ]);

        // Build booking data
        $this->bData['date'] = $date;
        $this->bData['start_time'] = '08:00';
        $this->bData['end_time'] = '12:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is not working on that time.'
        ]);
    }

    /**
     * When a booking ends after a working time
     *
     * @return void
     */
    public function testBookingEndsAfterWorkingTime()
    {
        // Create an activity that is 4 hours
        $activity = factory(Activity::class)->create([
            'duration' => '04:00'
        ]);

        // Get the current date
        $date = Carbon::now('AEST')->addDays(2)->toDateString();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 5:00 PM
        $workingTime = WorkingTime::create([
            'employee_id' => $this->employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date,
        ]);

        // Build booking data
        $this->bData['date'] = $date;
        $this->bData['start_time'] = '15:00';
        $this->bData['end_time'] = '19:00';

        // Send POST request to /admin/bookings
        $response = $this->actingAs($this->bo, 'web_admin')
            ->json('POST', 'admin/bookings', $this->bData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is not working on that time.'
        ]);
    }

    /**
     * Booking belongs to one employee, make 4 bookings and assign it to an employee
     *
     * @return void
     */
    public function testBookingHasOnlyOneEmployee()
    {
        // There exists an employee
        $employee = factory(employee::class)->create();

        // Create 4 new bookings and assign it to employee
        $bookings = factory(Booking::class, 4)->create([
            'employee_id' => $employee->id,
        ]);

        // Get employee from booking class
        // Check if 4 bookings belong to 1 employee
        // Call attribute employee will return one employee from each booking
        foreach ($bookings as $booking) {
            $this->assertEquals($employee->id, $booking->employee->id);
        }
    }

    /**
     * Booking belongs to one activity, make 4 bookings and assign it to an activity
     *
     * @return void
     */
    public function testBookingHasOnlyOneActivity()
    {
        // There exists an activity
        $activity = factory(Activity::class)->create();

        // Create 4 new bookings and assign it to activity
        $bookings = factory(Booking::class, 4)->create([
            'activity_id' => $activity->id,
        ]);

        // Get activity from booking class
        // Check if 4 bookings belong to 1 activity
        // Call attribute activity will return one activity from each booking
        foreach ($bookings as $booking) {
            $this->assertEquals($activity->id, $booking->activity->id);
        }
    }

    /**
     * Booking can only have one customer
     *
     * @return void
     */
    public function testABookingBelongsToCustomer()
    {
        // Given there is a booking
        $booking = factory(Booking::class)->create();

        // Bookings has separate customer
        $this->assertEquals(1, count($booking->customer));
    }

    /**
     * Many bookings belongs to one customer
     *
     * @return void
     */
    public function testManyBookingsBelongsToCustomer()
    {
        // Given there is 4 bookings with the same customer ID
        $bookings = factory(Booking::class, 4)->create([
            'customer_id' => 1,
        ]);

        // Count amount of bookings related to the customer by customer ID
        $count = 0;
        foreach ($bookings as $booking) {
            if ($booking->customer->id == 1) {
                $count++;
            }
        }

        // Check if all bookings total of 4 relate to customer
        $this->assertEquals(4, $count);
    }

    /**
     * Display only history bookings
     *
     * @return void
     */
    public function testOnlyShowHistoryBookings()
    {
        // a previous booking
        $validBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)
                ->subWeek()
                ->toDateString()
        ]);

        // a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)
                ->addWeek()
                ->toDateString()
        ]);

        // Use a static function to get history bookings
        $history = Booking::allHistory();

        // check if the valid booking is contained but not the invalid one
        $this->assertTrue($history->contains('date', $validBooking->date));
        $this->assertFalse($history->contains('date', $invalidBooking->date));
    }

    /**
     * Test to see if history bookings are ordered
     *
     * @return void
     */
    public function testHistoryBookingsAreOrdered()
    {
        // earlier booking
        $earlierBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->subWeek()->toDateString()
        ]);

        // later booking by 2 weeks
        $laterBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->subWeek(2)->toDateString()
        ]);

        // Use a static function to get history bookings
        $history = Booking::allHistory();

        // check if the earlier booking is first
        $this->assertEquals($history->first()->date, $earlierBooking->date);
        $this->assertNotEquals($history->first()->date, $laterBooking->date);
    }

    /**
     * Do not show booking that starts now in history bookings
     *
     * @return void
     */
    public function testDontShowNowHistoryBookings()
    {
        // this means if the booking is exactly now it should not be displayed
        // a form of boundry testing for the middle since startTime < now
        // Create a two hour booking from today

        // Current time in Melbourne Australia
        $timeNow = Carbon::now('AEST')->addDays(2);

        // Build booking data
        $nowBooking = factory(Booking::class)->create([
            'start_time' => $timeNow->toTimeString(),
            'end_time' => $timeNow->addHours(2)->toTimeString(),
            'date' => $timeNow->toDateString()
        ]);

        // Use a static function to get history bookings
        $history = Booking::allHistory();

        // check if this booking is displayed, assuming it must return false
        $this->assertFalse($history->contains('date', $nowBooking->date));
    }

    /**
     * Display many history of bookings
     *
     * @return void
     */
    public function testDisplayManyHistoryBookings()
    {
        //create a few bookings and make sure they are all being displayed
        $booking = factory(Booking::class, 4)->create([
            'date' => Carbon::now('AEST')->addDays(2)->subWeek()->toDateString()
        ]);

        // Use a static function to get history bookings
        $history = Booking::allHistory();

        //make sure they are all returned (since they should all be valid)
        $this->assertCount(4, $history);
    }

    /**
     * Display only latest bookings
     *
     * @return void
     */
    public function testOnlyShowLastestBookings()
    {
        // a previous booking
        $validBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)
                ->addWeek()
                ->toDateString()
        ]);

        // a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)
                ->subWeek()
                ->toDateString()
        ]);

        // Use a static function to get latest bookings
        $latest = Booking::allLatest();

        // check if the valid booking is contained but not the invalid one
        $this->assertTrue($latest->contains('date', $validBooking->date));
        $this->assertFalse($latest->contains('date', $invalidBooking->date));
    }

    /**
     * Test to see if latest bookings are ordered
     *
     * @return void
     */
    public function testLatestBookingsAreOrdered()
    {
        // earlier booking
        $earlierBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->addWeek(1)->toDateString()
        ]);

        // later booking by 2 weeks
        $laterBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->addWeeks(2)->toDateString()
        ]);

        // Use a static function to get latest bookings
        $latest = Booking::allLatest();

        // check if the earlier booking is first
        $this->assertEquals($latest->first()->date, $earlierBooking->date);
        $this->assertNotEquals($latest->first()->date, $laterBooking->date);
    }

    /**
     * Do show booking that starts now in latest bookings
     *
     * @return void
     */
    public function testShowNowLatestBookings()
    {
        // Get current time
        $now = Carbon::now('AEST')->addDays(2);

        // Create a two hour booking that starts now
        $nowBooking = factory(Booking::class)->create([
            'start_time' => $now->toTimeString(),
            'end_time' => $now->addHours(2)->toTimeString(),
            'date' => $now->toDateString()
        ]);

        // Use a static function to get latest bookings
        $latest = Booking::allLatest();

        // check if this booking is displayed, assuming it must return false
        $this->assertTrue($latest->contains('date', $nowBooking->date));
    }

    /**
     * Display many latest bookings
     *
     * @return void
     */
    public function testDisplayManyLatestBookings()
    {
        // create a few bookings and make sure they are all being displayed
        $booking = factory(Booking::class, 20)->create([
            'date' => Carbon::now('AEST')->addDays(2)->addWeek()->toDateString()
        ]);

        // Use a static function to get latest bookings
        $latest = Booking::allLatest();

        // make sure they are all returned (since they should all be valid)
        $this->assertCount(20, $latest);
    }

    /**
     * Display latest bookings with a given maximum 7 days
     *
     * @return void
     */
    public function testDisplayMaxSevenDaysOfLatestBookings()
    {
        // Create a valid booking for the next day
        $validBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->addDay()->toDateString()
        ]);

        // Create an invalid booking for the next week
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('AEST')->addDays(2)->addWeek()->addDay()->toDateString()
        ]);

        // Call relative time parameter to get all bookings of this week
        $latest = Booking::allLatest('+7 days');

        // Count 1 valid booking
        $this->assertCount(1, $latest);
    }
}