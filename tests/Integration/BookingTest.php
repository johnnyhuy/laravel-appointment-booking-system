<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Activity;
use App\Employee;
use App\Customer;
use App\BusinessOwner;
use App\Booking;

use Carbon\Carbon;

class BookingTest extends TestCase
{
    // Rollback database actions once test is complete with this trait
    use DatabaseTransactions;

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
    public function testAdminAddBooking()
    {
        // Build fake data
        $bo = factory(BusinessOwner::class)->create();
        $employee = factory(Employee::class)->create();
        $customer = factory(Customer::class)->create();
        $activity = factory(Activity::class)->create();

        // Get Current time
        $time = Carbon::createFromTime(0, 0);

        // Activity duration
        $duration = Carbon::parse($activity->duration);

        // Add a booking from activity duration
        // Build booking data
        $bookingData = [
            'customer_id' => $customer->id,
            'employee_id' => $employee->id,
            'activity_id' => $activity->id,
            'start_time' => $time->format('H:i'),
            'end_time' => $time->addHours($duration->hour)->addMinutes($duration->minute)->format('H:i'),
            'date' => Carbon::now()->toDateString(),
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

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
     * Booking validation rules
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
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The customer field is required.'
        ]);


        // User selects a customer that does not exist
        // Build booking data
        $bookingData = [
            'customer_id' => 1,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'Customer does not exist.'
        ]);


        // User selects no activity
        // Build booking data
        $bookingData = [
            'activity_id' => '',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The activity field is required.'
        ]);


        // User selects a activity that does not exist
        // Build booking data
        $bookingData = [
            'activity_id' => 1,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'Activity does not exist.'
        ]);


        // User selects no employee
        // Build booking data
        $bookingData = [
            'employee_id' => '',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The employee field is required.'
        ]);


        // User selects a employee that does not exist
        // Build booking data
        $bookingData = [
            'employee_id' => 1,
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'Employee does not exist.'
        ]);


        // User inputs invalid start time
        // Build booking data
        $bookingData = [
            'start_time' => '@@@',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The start time field must be in the correct time format.'
        ]);


        // User inputs invalid end time
        // Build booking data
        $bookingData = [
            'end_time' => '@@@',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time field must be in the correct time format.'
        ]);


        // User inputs start time after end time
        // Build booking data
        $bookingData = [
            'start_time' => '04:00',
            'end_time' => '02:00',
        ];

        // Send POST request to admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'The end time must be a date after start time.'
        ]);
    }

    /**
     * Booking belongs to one activity, make 4 bookings and assign it to an activity
     *
     * @return void
     */
    public function testIfBookingAlreadyExistsThenError()
    {
        // Business Owner must be created and logged in
        $bo = factory(BusinessOwner::class)->create();

        // Get Current time
        $time = Carbon::createFromTime(0, 0);

        // There exists a booking
        $booking = factory(Booking::class)->create();

        // Add a booking from activity duration
        // Build booking data
        $bookingData = [
            'customer_id' => $booking->customer_id,
            'employee_id' => $booking->employee_id,
            'activity_id' => $booking->activity_id,
            'start_time' => $booking->start_time,
            'end_time' => $booking->end_time,
            'date' => $booking->date,
        ];

        // Send POST request to /admin/booking
        $response = $this->actingAs($bo)->json('POST', 'admin/booking', $bookingData);

        // Check response for an error message
        $response->assertJsonFragment([
            'Thes start time field must be in the correct time format.'
        ]);
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