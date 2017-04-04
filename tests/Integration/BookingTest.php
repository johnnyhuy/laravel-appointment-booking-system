<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\BusinessOwner;
use App\Booking;
use App\Http\Controllers\BookingController;
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

    public function testOnlyShowPreviousBookings()
    {
        // a previous booking 
        $validBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')
                ->subWeek()
                ->toDateTimeString()
        ]);

        // a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')
                ->addWeek()
                ->toDateTimeString()
        ]);

        $history = BookingController::history();

        // check if the valid booking is contained but not the invalid one
        $this->assertTrue($history->contains('booking_start_time', $validBooking->booking_start_time));
        $this->assertFalse($history->contains('booking_start_time', $invalidBooking->booking_start_time));
    }

    public function testBookingsAreOrdered()
    {
        // earlier booking
        $earlierBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')->subWeek()->toDateTimeString()
        ]);

        // later booking by 2 weeks
        $laterBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')->subWeek(2)->toDateTimeString()
        ]);
        
        $history = BookingController::history();

        // check if the earlier booking is first
        $this->assertEquals($history->first()->booking_start_time, $earlierBooking->booking_start_time);
        $this->assertNotEquals($history->first()->booking_start_time, $laterBooking->booking_start_time);
    }

    public function testDontShowNowBookings()
    {
        // this means if the booking is exactly now it should not be displayed
        // a form of boundry testing for the middle since startTime < now

        $nowBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')->toDateTimeString()
        ]);

        $history = BookingController::history();

        // check if this booking is displayed, assuming it must return false
        $this->assertFalse($history->contains('booking_start_time', $nowBooking->booking_start_time));
    }

    /*
        Tests we need to fill the arbitrary 6 test quota
    */


    public function testDisplayManyBookings()
    {
        //create a few bookings and make sure they are all being displayed
        $booking = factory(Booking::class, 20)->create([
            'booking_start_time' => Carbon::now('Australia/Melbourne')->subWeek()->toDateTimeString()
        ]);

        $history = BookingController::history();

        //make sure they are all returned (since they should all be valid)
        $this->assertCount(20, $history);
    }

    /**
     * Sort future bookings by ascending order by booking start time
     *
     * @return void
     */
    // public function testSortFutureBookingsByAscendingOrder()
    // {

    // }
}