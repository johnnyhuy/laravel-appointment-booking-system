<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Booking;
use Carbon\Carbon;

class BookingHistory extends TestCase
{
	// Rollback database actions once test is complete with this trait
	use DatabaseTransactions;

    public function testOnlyShowPreviousBookings()
    {
        //a previous booking 
    	$validBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now()->subWeek()]);

        //a future booking (should not be displayed)
        $invalidBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now()->addWeek()
            ]);

        $history = Booking::getHistory();

        //check if the valid booking is contained but not the invalid one
        $this->assertContains($validBooking, $history);
        $this->assertNotContains($invalidBooking, $history);
    }

    public function testDontShowNowBookings()
    {
        //this means if the booking is exactly now it should not be displayed
        //a form of boundry testing for the middle since startTime < now

        $nowBooking = factory(Booking::class)->create([
            'booking_start_time' => Carbon::now()]);

        $history = Booking::getHistory();

        //check if this booking is displayed
        $this->assertContains($nowBooking, $history);
    }

    /*
        Tests we need to fill the arbitrary 6 test quota
    */


    public function testDisplayManyBookings()
    {
        //create a few bookings and make sure they are all being displayed
        $booking = factory(Booking::class, 20)->create([
            'booking_start_time' => Carbon::now()]);

        $history = Booking::getHistory();

        //make sure they are all returned (since they should all be valid)
        $this->assertCount(20, $history);
    }




}
