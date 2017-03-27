<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Booking;
use App\Http\Controllers\CustomerController;

class BookingsTest extends TestCase
{
	use DatabaseTransactions;
 
 	/**
     * Test booking customer id exists
     *
     * @return void
     */
	public function testBookingsHasCustomerId()
	{
		// Generate fake data
		$factory = factory(Booking::class)->make();

		// Create booking
		$booking = new Booking(['customer_id' => $factory->customer_id]);

		// Booking has customer ID?
		$this->assertEquals($factory->customer_id, $booking->customer_id);
	}

	/**
     * Test booking booking start time exists
     *
     * @return void
     */
	public function testBookingsHasBookingStartTime()
	{
		// Generate fake data
		$factory = factory(Booking::class)->make();

		// Create booking
		$booking = new Booking(['booking_start_time' => $factory->booking_start_time]);

		// Booking has start time?
		$this->assertEquals($factory->booking_start_time, $booking->booking_start_time);
	}

	/**
     * Test booking booking end time exists
     *
     * @return void
     */
	public function testBookingsHasBookingStartTime()
	{
		// Generate fake data
		$factory = factory(Booking::class)->make();

		// Create booking
		$booking = new Booking(['booking_end_time' => $factory->booking_end_time]);

		// Booking has end time?
		$this->assertEquals($factory->booking_end_time, $booking->booking_end_time);
	}
}
