<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class BookingController extends Controller
{
	public function __construct() {
		// Check auth, if not auth then redirect to login
        $this->middleware('auth:web_user', [
            'only' => [
                'indexCustomer',
            ]
        ]);
        $this->middleware('auth:web_admin', [
            'only' => [
                'indexAdmin',
                'summary',
                'history',
                'assignEmployee',
            ]
        ]);

        $this->middleware('auth:web_user,web_admin',[
            'only' => [
                'store',
            ]
        ]);
    }

    /**
     * Show summary of bookings
     *
     * @return Response
     */
    public function summary()
    {
        return view('admin.summary', [
            'bookings' => Booking::allLatest('7'),
            'business' => BusinessOwner::first(),
            'latest' => Booking::allLatest('+7 days')
        ]);
    }

    /**
     * Show history of bookings
     *
     * @return Response
     */
    public function history()
    {
        return view('admin.history', [
            'bookings' => Booking::allLatest('7'),
            'business' => BusinessOwner::first(),
            'history' => Booking::allHistory()
        ]);
    }

    /**
     * Show booking by employee ID
     *
     * @param  String $monthYear    month year string from URL (mm-yyyy)
     * @param  String $employeeID   employee ID
     * @return view
     */
    public function show($monthYear, $employeeID = null)
    {
        // List of months
        $monthList = getMonthList($monthYear);

        // Set date string
        $date = WorkingTime::getDate($monthYear);

        // Get bookings of the month
        $bookings = Booking::where('date', '<=', $date->endOfMonth()->toDateString())
            ->where('date', '>=', $date->startOfMonth()->toDateString())
            ->get()
            ->sortBy('date');

        // Find employee
        $employee = Employee::findOrFail($employeeID);

        if ($employeeID) {
            // Find working time by employee ID
            $workingTimes = WorkingTime::where('employee_id', $employeeID);
        }
        else {
            // Else get all working times
            $workingTimes = WorkingTime::all();
        }

        $workingTimes = $workingTimes->where('date', '<=', $date->endOfMonth()->toDateString())
            ->where('date', '>=', $date->startOfMonth()->toDateString())
            ->get();

        return view('admin.bookings', [
            'bookings'      => $bookings,
            'business'      => BusinessOwner::first(),
            'employeeID'    => $employeeID,
            'employee'      => $employee,
            'roster'        => $workingTimes,
            'date'          => $date,
            'dateString'    => $date->format('m-Y'),
            'months'        => $monthList
        ]);
    }

    public function indexAdmin($monthYear)
    {
        // List of months
        // 6 months ahead and behind
        $monthList = [];

        // Get months previous
        for ($months = 6; $months > 0; $months--) {
            $monthList[] = WorkingTime::getDate($monthYear)->subMonths($months);
        }

        // Get months now and ahead
        for ($months = 0; $months < 6; $months++) {
            $monthList[] = WorkingTime::getDate($monthYear)->addMonths($months);
        }

        // Get the date from URL
        $date = WorkingTime::getDate($monthYear);

        // Get bookings of the month
        $bookings = Booking::where('date', '<=', $date->endOfMonth()->toDateString())
            ->where('date', '>=', $date->startOfMonth()->toDateString())
            ->get();

        return view('admin.bookings', [
            'business'      => BusinessOwner::first(),
            'bookings'      => $bookings,
            'employeeID'    => null,
            'employee'      => null,
            'date'          => $date,
            'dateString'    => $date->format('m-Y'),
            'months'        => $monthList,
            'roster'        => WorkingTime::all(),
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

        // If month year format requested
        if ($request->month_year) {
            $monthYear = explode('-', $request->month_year);
            $date = Carbon::createFromDate($monthYear[1], $monthYear[0], $request->day)->toDateString();
            $request->merge(['date' => $date]);
        }
        else {
            $date = $request->date;
        }

        // If none is selected
        if ($request->employee_id == 'none') {
            // Clear rules for employee ID
            $rules['employee_id'] = '';
            $request->merge(['employee_id' => null]);
        }

        // Convert start time to proper time format
        $request->merge([
            'start_time' => toTime($request->start_time)
        ]);


        // Validation error messages
        $messages = [
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.exists' => 'The :attribute does not exist.',
            'customer_id.is_on_booking' => 'The :attribute is already set on at that time.',
            'employee_id.exists' => 'The :attribute does not exist.',
            'employee_id.is_employee_working' => 'The :attribute is not working on that time.',
            'employee_id.is_on_booking' => 'The :attribute is already working on another booking at that time.',
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'activity_id' => 'required|exists:activities,id|is_end_time_valid',
            'customer_id' => 'required|exists:customers,id|is_on_booking',
            'employee_id' => 'required|exists:employees,id|is_employee_working|is_on_booking',
            'start_time' => 'required|date_format:H:i:s',
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

        // If end time is requested then do not calculate
        if ($request->end_time) {
            $request->merge([
                'end_time' => toTime($request->end_time)
            ]);
        }
        else {
            $request->merge([
                'end_time' => Booking::calcEndTime(Activity::find($request->activity_id)->duration, $request->start_time)
            ]);
        }

        // Create customer
        $booking = Booking::create([
            'customer_id' => $request->customer_id,
            'employee_id' => $request->employee_id,
            'activity_id' => $request->activity_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
        ]);

        Log::notice("A booking was created from the Business Owner Dashboard", $booking->toArray());

        // Session flash
        session()->flash('message', 'Booking has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/admin/bookings/' . toMonthYear(getNow()));
    }

    /**
     *  Create a booking from the customer form
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
	public function storeCustomerBooking(Request $request) {
        Log::info("An attempt to create a booking from the Customer Portal was made", $request->all());

        $request->merge(['customer_id' => Auth::id()]);

        // Validation error messages
        $messages = [
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
            'employee_id.exists' => 'The :attribute does not exist.',
            'employee_id.is_employee_working' => 'The :attribute is not working on that time.',
            'employee_id.is_on_booking' => 'The :attribute is already working on another booking at that time.',
            'customer_id.is_on_booking' => 'You already have an existing booking at that time.',
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
        ];

        // Validation rules
        $rules = [
            'activity_id' => 'required|exists:activities,id|is_end_time_valid',
            'employee_id' => 'required|exists:employees,id|is_employee_working|is_on_booking',
            'customer_id' => 'is_on_booking',
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
            'activity_id' => $request->activity_id,
            'start_time' => toTime($request->start_time),
            'end_time' => Booking::calcEndTime(Activity::find($request->activity_id)->duration, $request->start_time),
            'date' => $request->date,
        ]);

        Log::notice("A booking was created from the Customer Portal", $booking->toArray());

        // Session flash
        session()->flash('message', 'Booking has successfully been created. No employee is assigned to your booking, please come back soon when an adminstrator verifies your booking.');

        //Redirect to the business owner admin page
        return redirect('/bookings');
    }

    /**
     * View index of customer bookings
     */
    public function indexCustomer()
    {
        // Find customer bookings by customer ID on booking
        $bookings = Booking::all()
            ->where('customer_id', Auth::id())
            ->sortBy('date');

        return view('customer.bookings', compact('bookings'));
    }

    /**
     * Shows the create booking page
     */
    public function newCustomerBooking()
    {
        return view('customer.create_bookings');
    }
}
