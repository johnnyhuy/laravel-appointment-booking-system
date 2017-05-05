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

class WorkingTimeController extends Controller
{
    public function __construct() {
        // Business Owner auth
        $this->middleware('auth:web_admin', [
            'only' => [
                'index',
                'roster',
                'create',
            ]
        ]);
    }

    /**
     * Roster index
     *
     * @param  String $monthYear    month year string from URL (mm-yyyy)
     * @param  String $employeeID   employee ID
     * @return view
     */
    public function show($monthYear, $employeeID)
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

        // Set date string
        $date = WorkingTime::getDate($monthYear);

        // Find employee
        $employee = Employee::find($employeeID);

        // Find working time by employee ID
        $workingTimes = WorkingTime::where('employee_id', $employeeID)->get();

        return view('admin.roster', [
            'business'      => BusinessOwner::first(),
            'employeeID'    => $employeeID,
            'employee'      => $employee,
            'roster'        => $workingTimes,
            'date'          => $date,
            'dateString'    => $date->format('m-Y'),
            'months'        => $monthList
        ]);
    }

    /**
     * Roster index
     *
     * @param  String $monthYear    month year string from URL (mm-yyyy)
     * @return view
     */
    public function index($monthYear)
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

        $date = WorkingTime::getDate($monthYear);

        return view('admin.roster', [
            'business'      => BusinessOwner::first(),
            'roster'        => WorkingTime::all(),
            'employeeID'    => null,
            'employee'      => null,
            'date'          => $date,
            'dateString'    => $date->format('m-Y'),
            'months'        => $monthList
        ]);
    }

    public function roster()
    {
        return view('admin.roster', [
            'business' => BusinessOwner::first(),
            'roster' => WorkingTime::getRoster()
        ]);
    }

    // Create a new working time
	public function create(Request $request, $monthYear = null)
	{
        if ($request->month_year) {
            $temp = explode('-', $request->month_year);
            $date = Carbon::createFromDate($temp[1], $temp[0], $request->day)->toDateString();
            $request->merge(['date' => $date]);
        }
        else {
            $date = toDate($request->date);
        }

        Log::info("An attempt was made to create a new working time", $request->all());

		// Custom error messages
		$messages = [
			'employee_id.exists' => 'The :attribute does not exist.',
			'start_time.date_format' => 'The :attribute field must be in the correct time format.',
            'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'date.unique' => 'The employee can only have one working time per day.',
			'date.date_format' => 'The :attribute field must be in the correct date format.',
		];

		// Validation rules
		$rules = [
            // Employee ID is required and must exist in employees table
            'employee_id' => 'required|exists:employees,id',

            // Start time is required
            'start_time' => 'required|before:end_time|date_format:H:i',

            // End time is required and must be AFTER the start time (they can't be the same either)
            'end_time' => 'required|after:start_time|date_format:H:i',

        	// Date must be unique where employee ID is unique
            'date' => 'required|date_format:Y-m-d|unique:working_times,date,NULL,id,employee_id,' . $request->employee_id,
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'employee_id' => 'employee',
        ];

		// Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Create a working time
        $workingTime = WorkingTime::create([
            'employee_id' => $request->employee_id,
            'start_time' => toTime($request->start_time),
            'end_time' => toTime($request->end_time),
            'date' => $date,
        ]);

        Log::notice("A new working time was created for employee with id " . $workingTime->employee_id . " for times: " .
            $workingTime->date . " => " . $workingTime->start_time . " - " . $workingTime->end_time);

        // Session flash
        session()->flash('message', 'New working time has been added.');

        // Redirect to the business owner employee page
        return redirect('/admin/roster/' . $monthYear);
	}

    /**
     * View edit booking page
     */
    public function edit($monthYear, $employeeID, $workingTimeID)
    {
        // Find working time by ID
        $workingTime = WorkingTime::find($workingTimeID);

        $business = BusinessOwner::first();

        return view('admin.edit_working_time', compact(['workingTime', 'business']));
    }

    /**
     * Update a working time by ID
     * Sent by PUT/PATCH request
     */
    public function update(Request $request, $id)
    {
        // Custom error messages
        $messages = [
            'employee_id.exists' => 'The :attribute does not exist.',
            'date.unique' => 'The employee can only have one working time per day.',
            'date.date_format' => 'The :attribute field must be in the correct date format.',
        ];

        // Validation rules
        $rules = [
            // Employee ID is required and must exist in employees table
            'employee_id' => 'required|exists:employees,id',

            // Start time is required
            'start_time' => 'required|before:end_time',

            // End time is required and must be AFTER the start time (they can't be the same either)
            'end_time' => 'required|after:start_time',

            // Date must be unique where employee ID is unique
            'date' => 'required|date_format:Y-m-d',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'employee_id' => 'employee',
        ];

        // Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Find working time
        $workingTime = WorkingTime::find($id);

        // Unassign employee that was previously working on a booking
        Booking::where('start_time', '>=', $workingTime->start_time)
            ->where('end_time', '<=', $workingTime->end_time)
            ->delete();

        // Save data
        $workingTime->employee_id = $request->employee_id;
        $workingTime->start_time = toTime($request->start_time);
        $workingTime->end_time = toTime($request->end_time);
        $workingTime->date = toDate($request->date);
        $workingTime->save();

        // Session flash
        session()->flash('message', 'Edited working time has been successful.');

        // Redirect to the business owner employee page
        return redirect('/admin/roster/' . Carbon::parse($request->date)->format('m-Y'));
    }
}
