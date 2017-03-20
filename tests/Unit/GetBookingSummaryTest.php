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

}
