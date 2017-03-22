<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Booking;
use App\Customer;

class testGetBookingSummary extends TestCase
{
	
	//Rolls back any changes to the database after completion of a test
	use DatabaseTransactions;
	
	public function testGetBookingSummaryPass() {
		//Given there are two bookings made for today
		//The booking summary should return those two bookings
		
		$booking1 = factory(Booking::class)->create();
		$booking2 = factory(Booking::class)->create();
		
		$this->assertCount(2, Booking::GetBookingsSummary());
	}
	
 	public function testGetBookingSummaryOldBookings() {
		//Given 2 bookings, 1 current and 1 that is a month old,
		//only the current booking should be shown in the summary
		$booking1 = factory(Booking::class)->create();
		$booking2 = factory(Booking::class)->create([
			'booking_start_time' => \Carbon\Carbon::now()->subMonth(),
			'booking_end_time' => \Carbon\Carbon::now()->subMonth()
		]);

		$this->assertCount(1, Booking::GetBookingsSummary());
	}
	
	public function testGetBookingSummaryFutureBookings() {
		//Given 2 bookings, 1 current and 1 that is a month in the future,
		//the summary should only return the current booking as the 
		//future booking is too far away
		$booking1 = factory(Booking::class)->create();
		$booking2 = factory(Booking::class)->create([
			'booking_start_time' => \Carbon\Carbon::now()->addMonth(),
			'booking_end_time' => \Carbon\Carbon::now()->addMonth()
		]);

		$this->assertCount(1, Booking::GetBookingsSummary());
	}

	public function testGetBookingSummaryShowAllValidBookings(){
		//Given 50 valid bookings in the database
		//the summary should return all bookings
		$bookings = factory(Booking::class, 50)->create();

		$this->assertCount(50, Booking::GetBookingsSummary());
	}

	public function testGetBookingSummaryNoValidBookings(){
		//if there is no bookings in the database
		//then the summary should return nothing
		$this->assertCount(0, Booking::GetBookingsSummary());
	}

	public function testGetBookingSummaryDBChange()
	{
		//if something is changed/deleted from the database then
		//the summaries before and after the change should be different

		//populate the database with 10 bookings
		$bookings = factory(Booking::class, 10)->create();

		//store the result of calling the fucntion the first time
		$firstCall = Booking::GetBookingsSummary();

		//add a booking to the database 
		$extra =factory(Booking::class)->create();

		//store the second result
		$secondCall = Booking::GetBookingsSummary();

		//check to see if the results are different
		$this->assertNotEquals($firstCall, $secondCall);
	}

}
