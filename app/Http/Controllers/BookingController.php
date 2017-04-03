<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Customer;

class BookingController extends Controller
{
    public static function makeBooking($customer_id, $day, $start_time, $end_time) {
		return false;
	}

	public function index()
	{
		return view('bookings.index');
	}

	public static function getHistory()
	{
		$bookings = DB::table('bookings')->whereDate('booking_start_time', '<', \Carbon\Carbon::now())->get();

		return $bookings;
	}
}
