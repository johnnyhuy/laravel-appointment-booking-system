<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;

use Carbon\Carbon;

class BookingController extends Controller
{
	public function __construct() {
		// Check auth, if not auth then redirect to login
        $this->middleware('auth:web_user', ['only' => ['customerBookings', 'showCreateBooking']]);
        $this->middleware('auth:web_admin', [
            'only' => [
                'index',
                'store',
                'history',
            ]
        ]);
    }

    public function index()
    {
        return view('admin.booking', [
            'business' => BusinessOwner::first(),
            'bookings' => Booking::allLatest()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("An attempt to create a booking from the Business Owner Dashboard was made", $request->all());

        // Validation error messages
        $messages = [
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.exists' => 'The :attribute does not exist.',
            'employee_id.exists' => 'The :attribute does not exist.',
            'employee_id.is_employee_working' => 'The :attribute either has a conflict with another booking or :attribute is not working on that time.',
            'employee_id.is_employee_on_booking' => 'The :attribute is already working on another booking at that time.',
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'exists:employees,id|is_employee_working|is_employee_on_booking',
            'activity_id' => 'required|exists:activities,id|is_end_time_valid',
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
            'end_time' => Booking::calcEndTime(Activity::find($request->activity_id)->duration, $request->start_time),
            'date' => $request->date,
        ]);

        Log::notice("A booking was created from the Business Owner Dashboard with id " . $booking->id);

        // Session flash
        session()->flash('message', 'Booking has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/admin/booking');
    }

    /**
     *  Create a booking from the customer form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function createBooking(Request $request) {
        Log::info("An attempt to create a booking from the Customer Portal was made", $request->all());

        // Validation error messages
        $messages = [
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.exists' => 'The :attribute does not exist.',
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'activity_id' => 'required|exists:activities,id|is_end_time_valid',
            'start_time' => 'required|date_format:H:i',
            'date' => 'required|date',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'customer_id' => 'customer',
            'activity_id' => 'activity',
            'start_time' => 'start time',
            'end_time' => 'end time',
        ];

        // Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Create customer
        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'activity_id' => $request->activity_id,
            'start_time' => $request->start_time,
            'end_time' => Booking::calcEndTime(Activity::find($request->activity_id)->duration, $request->start_time),
            'date' => $request->date,
        ]);

        Log::notice("A booking was created from the Customer Portal with id " . $booking->id);

        // Session flash
        session()->flash('message', 'Booking has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/create_booking');
    }

    /**
     * Assign an employee to an existing booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignEmployee(Request $request) 
    {   
        //Iterate through each booking being set
        for($i = 0; $i < count($request['bookings']); $i++)
        {
            Log::notice("Employee with id " . $request['employee_id'] . " was assigned to booking with id " . $request['bookings'][$i]);
            //Update booking to given employee
            DB::table('bookings')->where('id', $request['bookings'][$i])->update(['employee_id' => $request['employee_id']]);
        }
        
        // Session flash
        session()->flash('message', 'Booking(s) have been successfully assigned.');

        //Redirect to the business owner admin page
        return redirect('admin/employees/assign/' . $request['employee_id']);
    }

	/**
	 * View index of customer bookings
	 */
	public function customerBookings()
	{
		return view('customer.bookings');
	}
	
	/**
	 * Shows the create booking page
	 */
	public function showCreateBooking() 
	{
		return view('customer.create_bookings');
	}

    /**
     * View index of customer bookings
     */
    public function history()
    {
        return view('admin.history', [
            'business' => BusinessOwner::first(),
            'history' => Booking::allHistory(),
        ]);
    }
}
