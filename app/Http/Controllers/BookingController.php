<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Booking;
use App\Customer;
use Carbon\Carbon;

class BookingController extends Controller
{
	public function __construct() {
		// Check auth, if not auth then redirect to login
        $this->middleware('auth:web_user');
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
