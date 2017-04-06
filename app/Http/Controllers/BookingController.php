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
	 * View index og customer bookings
	 *
	 */
	public function index()
	{
		return view('bookings.index');
	}
}
