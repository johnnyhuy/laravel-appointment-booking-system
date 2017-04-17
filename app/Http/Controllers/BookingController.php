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
        $this->middleware('guest:web_user', ['only' => 'index']);
        $this->middleware('guest:web_admin', ['only' => 'store']);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation error messages
        $messages = [
        	'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.exists' => 'The :attribute does not exist.',
            'employee_id.exists' => 'The :attribute does not exist.',
            'employee_id.is_employee_free' => 'The :attribute is already working on another booking at that time.',
            'activity_id.exists' => 'The :attribute does not exist.',
        	'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'exists:employees,id|is_employee_free:' . $request->date . ',' . $request->start_time . ',' . $request->end_time,
            'activity_id' => 'required|exists:activities,id|is_end_time_valid:' . $request->start_time,
            'start_time' => 'required|date_format:H:i',
            'date' => 'required|date',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'customer_id' => 'customer',
            'employee_id' => 'employee',
            'activity_id' => 'activity',
            'start_time' => 'start time',
            'end_time' => 'end time',
        ];

        // Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Create customer
        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'activity_id' => $request->activity_id,
            'start_time' => $request->start_time,
            'end_time' => Booking::calculateEndTime($request->activity_id, $request->start_time),
            'date' => $request->date,
        ]);

        // Session flash
        session()->flash('message', 'Booking has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/admin/activity');
    }

	/**
	 *
	 * View index of customer bookings
	 *
	 */
	public function index()
	{
		return view('bookings.index');
	}
}
