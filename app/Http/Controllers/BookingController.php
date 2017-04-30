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
                'storeCustomerBooking'
            ]
        ]);
        $this->middleware('auth:web_admin', [
            'only' => [
                'indexAdmin',
                'storeAdminBooking',
                'history',
                'assignEmployee',
            ]
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

        return view('admin.booking', [
            'business' => BusinessOwner::first(),
            'bookings' => $bookings,
            'roster' => WorkingTime::all(),
            'date' => $date,
            'months' => $monthList,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeAdminBooking(Request $request)
    {
        if ($request->month_year) {
            $monthYear = explode('-', $request->month_year);
            $date = Carbon::createFromDate($monthYear[1], $monthYear[0], $request->day)->toDateString();
            $request->merge(['date' => $date]);
        }
        else {
            $date = $request->date;
        }

        Log::info("An attempt to create a booking from the Business Owner Dashboard was made", $request->all());

        // Validation error messages
        $messages = [
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.exists' => 'The :attribute does not exist.',
            'employee_id.exists' => 'The :attribute does not exist.',
            'employee_id.is_employee_working' => 'The :attribute either has a conflict with another booking or :attribute is not working on that time.',
            'employee_id.is_on_booking' => 'The :attribute is already working on another booking at that time.',
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'customer_id' => 'required|exists:customers,id',
            'employee_id' => 'exists:employees,id|is_employee_working|is_on_booking',
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

        // If none is selected
        if ($request->employee_id == 'none') {
            // Clear rules for employee ID
            $rules['employee_id'] = '';
            $request->merge(['employee_id' => null]);
        }

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

        Log::notice("A booking was created from the Business Owner Dashboard", $booking->toArray());

        // Session flash
        session()->flash('message', 'Booking has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/admin/booking/' . Carbon::now()->format('m-Y'));
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
            'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'customer_id.is_on_booking' => 'You already have an existing booking at that time.',
            'activity_id.exists' => 'The :attribute does not exist.',
            'activity_id.is_end_time_valid' => 'The :attribute duration added on start time is invalid. Please add a start time that does not go to the next day.',
        ];

        // Validation rules
        $rules = [
            'activity_id' => 'required|exists:activities,id|is_end_time_valid',
            'customer_id' => 'is_on_booking',
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

    /**
     * Assign an employee to an existing booking
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function assignEmployee(Request $request)
    {
        // Iterate through each booking being set
        for($i = 0; $i < count($request['bookings']); $i++) {
            Log::notice("Employee with id " . $request['employee_id'] . " was assigned to booking with id " . $request['bookings'][$i]);
            // Update booking to given employee
            DB::table('bookings')->where('id', $request['bookings'][$i])->update(['employee_id' => $request['employee_id']]);
        }

        // Session flash
        session()->flash('message', 'Booking(s) have been successfully assigned.');

        // Redirect to the business owner admin page
        return redirect('admin/employees/assign/' . $request['employee_id']);
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
