<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Booking;
use App\Customer;
use Carbon\Carbon;

class BookingController extends Controller
{
	/**
	 *
	 * Show all preivous bookings
	 *
	 */
	public static function history() {
		// Return past bookings eloquent model
		return Booking::whereDate('booking_start_time', '<', Carbon::now()->startOfDay())			
			// Get eloquent model
			->get()
			// Sort by using an eloquent collection function
			->sortByDESC('booking_start_time');
	}

	/**
	 *
	 * View index og customer bookings
	 *
	 */
	public function index()
	{
		return view('bookings.index');
	}
}
