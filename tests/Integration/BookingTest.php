<?php

namespace Tests\Integration;

use Tests\TestCase;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class BookingTest extends TestCase
{
    /**
     * Customer successfully create a booking
     *
     * @return void
     */
    public function testCustomerCreateBookingSuccessful()
    {
        // There exists a customer and employee
        $customer = factory(Customer::class)->create();

        // Create an activity that is 2 hours long
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Build booking data
        // Booking is set to tomorrow at 09:00 AM
        $bookingData = [
            'activity_id' => $activity->id,
            'start_time' => '09:00',
            'date' => Carbon::now('Australia/Melbourne')->addDay()->toDateString(),
        ];

        // Send POST request to /bookings
        $response = $this->actingAs($customer, 'web_user')
            ->json('POST', 'bookings', $bookingData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created. No employee is assigned to your booking, please come back soon when an adminstrator verifies your booking.');

        // Check the database if booking exists
        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'customer_id' => $customer->id,
            'employee_id' => null,
            'activity_id' => $activity->id,
            'start_time' => $bookingData['start_time'],
            'end_time' => Booking::calcEndTime($activity->duration, $bookingData['start_time']),
            'date' => $bookingData['date'],
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
        $this->assertCount(4, Customer::first()->bookings);
    }

    /**
     * Creating a booking through the Business Owner view (admin)
     *
     * @return void
     */
    public function testAdminAddBookingSuccessful()
    {
        // Build fake data
        $bo = factory(BusinessOwner::class)->create();

        // Create an activity that is 2 hours
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Get the current date
        $date = Carbon::now('Australia/Melbourne')->toDateString();

        // Create fake data that adds to database
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 5:00 PM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date,
        ]);

        // Booking start time is 09:00 AM in 24 hour format
        $startTime = '09:00';

        // Calculate end time given by activity duration
        $endTime = Booking::calcEndTime($activity->duration, $startTime);

        // Add a booking from activity duration
        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $date,
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');

        // Check the database if booking exists
        $this->assertDatabaseHas('bookings', [
            'id' => 1,
            'customer_id' => $bookingData['customer_id'],
            'employee_id' => $bookingData['employee_id'],
            'activity_id' => $bookingData['activity_id'],
            'start_time' => $bookingData['start_time'],
            'end_time' => $bookingData['end_time'],
            'date' => $bookingData['date'],
        ]);
    }

    /**
     * Business Owner add booking validation rules
     *
     * @return void
     */
    public function testAdminAddBookingValidation()
    {
        // Build fake data
        $bo = factory(BusinessOwner::class)->create();

        // User selects no customer
        // Build booking data
        $bookingData = [
            'customer_id' => '',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The customer field is required.'
        ]);


        // User selects a customer that does not exist
        // Build booking data
        $bookingData = [
            'customer_id' => 1337,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The customer does not exist.'
        ]);


        // User selects no activity
        // Build booking data
        $bookingData = [
            'activity_id' => '',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity field is required.'
        ]);


        // User selects a activity that does not exist
        // Build booking data
        $bookingData = [
            'activity_id' => 1337,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity does not exist.'
        ]);


        // User selects a employee that does not exist
        // Build booking data
        $bookingData = [
            'employee_id' => 1337,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee does not exist.'
        ]);


        // User inputs invalid start time
        // Build booking data
        $bookingData = [
            'start_time' => '@@@',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

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
        // Business Owner must be created and logged in
        $bo = factory(BusinessOwner::class)->create();

        // Generate fake data
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();

        // Create an acitivity with a duration of 2 hours
        $activity = factory(Activity::class)->create([
            'duration' => '02:00',
        ]);

        // Get the current date
        $date = Carbon::now('Australia/Melbourne')->toDateString();

        // Create a working time for given employee today
        // Employee starts at 6:00 AM to 5:00 PM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '06:00',
            'end_time' => '17:00',
            'date' => $date,
        ]);

        // Start time is 10:00AM
        $startTime = '10:00';

        // Calculate the end time depending on the activity duration
        $endTime = Booking::calcEndTime($activity->duration, $startTime);

        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $date,
        ];

        // There exists a booking
        $booking = factory(Booking::class)->create([
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $bookingData['start_time'],
            'end_time' => $bookingData['end_time'],
            'date' => $bookingData['date'],
        ]);

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);


        // When employee starts before start time and finishes before end time
        // Subtract an hour from start and end time (offset)
        $bookingData['start_time'] = Carbon::parse($startTime)->subHour()->format('H:i');
        $bookingData['end_time'] = Carbon::parse($endTime)->subHour()->format('H:i');

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);


        // When employee starts after start time and finishes after end time
        // Add an hour from start and end time (offset)
        $bookingData['start_time'] = Carbon::parse($startTime)->addHour()->format('H:i');
        $bookingData['end_time'] = Carbon::parse($endTime)->addHour()->format('H:i');

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is already working on another booking at that time.'
        ]);

        // When employee working on booking starts before existing booking
        $bookingData['start_time'] = Carbon::parse($booking->start_time)
            ->subHours($activity->hour)
            ->subMinutes($activity->minute)
            ->format('H:i');
        $bookingData['end_time'] = Carbon::parse($booking->end_time)
            ->subHours($activity->hour)
            ->subMinutes($activity->minute)
            ->format('H:i');

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check message add booking is successful
        $response->assertSessionHas('message', 'Booking has successfully been created.');


        // When employee working on booking starts after existing booking
        $bookingData['start_time'] = Carbon::parse($booking->start_time)
            ->addHours($activity->hour)
            ->addMinutes($activity->minute)
            ->format('H:i');
        $bookingData['end_time'] = Carbon::parse($booking->end_time)
            ->addHours($activity->hour)
            ->addMinutes($activity->minute)
            ->format('H:i');

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

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
        // Business Owner must be created and logged in
        $bo = factory(BusinessOwner::class)->create();

        // Generate fake data
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();

        // Set duration for 2 hours
        $activity = factory(Activity::class)->create([
            'duration' => '2:00'
        ]);

        // Booking starts at 22:00
        $startTime = '22:00';

        // End time is 24:00 or 00:00, which is the next day
        $endTime = Booking::calcEndTime($activity->duration, $startTime);

        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => Carbon::now('Australia/Melbourne')->toDateString(),
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

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
        // Business Owner must be created and logged in
        $bo = factory(BusinessOwner::class)->create();

        // Create an activity that is 2 hours
        $activity = factory(Activity::class)->create([
            'duration' => '02:00'
        ]);

        // Get the current date
        $date = Carbon::now('Australia/Melbourne')->toDateString();

        // Create fake data that adds to database
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 11:00 AM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '09:00',
            'end_time' => '11:00',
            'date' => $date,
        ]);

        // Booking starts at 1:00 PM
        $startTime = '13:00';

        // Calculate the end time depending on the activity duration
        $endTime = Booking::calcEndTime($activity->duration, $startTime);

        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $date,
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee is not working on that time.'
        ]);
    }

    /**
     * When a booking activity is added and employee finishes before the end time
     *
     * @return void
     */
    public function testAddBookingWhenEmployeeFinishesEarly()
    {
        // Business Owner must be created and logged in
        $bo = factory(BusinessOwner::class)->create();

        // Create an activity that is 4 hours
        $activity = factory(Activity::class)->create([
            'duration' => '04:00'
        ]);

        // Get the current date
        $date = Carbon::now('Australia/Melbourne')->toDateString();

        // Create fake data that adds to database
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();

        // Create a working time for given employee today
        // Employee starts at 9:00 AM to 5:00 PM
        $workingTime = factory(WorkingTime::class)->create([
            'employee_id' => $employee->id,
            'start_time' => '09:00',
            'end_time' => '17:00',
            'date' => $date,
        ]);

        // Booking starts at 8:00 AM
        $startTime = '08:00';

        // Calculate the end time depending on the activity duration
        $endTime = Booking::calcEndTime($activity->duration, $startTime);

        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'date' => $date,
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo, 'web_admin')
            ->json('POST', 'admin/booking', $bookingData);

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
            'date' => Carbon::now('Australia/Melbourne')
                ->subWeek()
                ->toDateString()
        ]);

        // a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')
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
            'date' => Carbon::now('Australia/Melbourne')->subWeek()->toDateString()
        ]);

        // later booking by 2 weeks
        $laterBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->subWeek(2)->toDateString()
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
        $timeNow = Carbon::now('Australia/Melbourne');

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
        $booking = factory(Booking::class, 20)->create([
            'date' => Carbon::now('Australia/Melbourne')->subWeek()->toDateString()
        ]);

        // Use a static function to get history bookings
        $history = Booking::allHistory();

        //make sure they are all returned (since they should all be valid)
        $this->assertCount(20, $history);
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
            'date' => Carbon::now('Australia/Melbourne')
                ->addWeek()
                ->toDateString()
        ]);

        // a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')
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
            'date' => Carbon::now('Australia/Melbourne')->addWeek(1)->toDateString()
        ]);

        // later booking by 2 weeks
        $laterBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->addWeeks(2)->toDateString()
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
        $now = Carbon::now('Australia/Melbourne');

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
            'date' => Carbon::now('Australia/Melbourne')->addWeek()->toDateString()
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
            'date' => Carbon::now('Australia/Melbourne')->addDay()->toDateString()
        ]);

        // Create an invalid booking for the next week
        $invalidBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->addWeek()->addDay()->toDateString()
        ]);

        // Call relative time parameter to get all bookings of this week
        $latest = Booking::allLatest('+7 days');

        // Count 1 valid booking
        $this->assertCount(1, $latest);
    }
}