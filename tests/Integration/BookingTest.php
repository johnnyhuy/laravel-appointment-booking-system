<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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

        $nowBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->toDateString()
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
     * Do not show booking that starts now in latest bookings
     *
     * @return void
     */
    public function testDontShowNowLatestBookings()
    {
        // Create a booking that starts now
        $nowBooking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->toDateString()
        ]);

        // Use a static function to get latest bookings
        $latest = Booking::allLatest();

        // check if this booking is displayed, assuming it must return false
        $this->assertFalse($latest->contains('date', $nowBooking->date));
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