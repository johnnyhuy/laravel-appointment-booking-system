<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\BusinessOwner;
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
        
        return view('admin.roster', [
            'business' => BusinessOwner::first(), 
            'roster' => WorkingTime::all(),
            'date' => WorkingTime::getDate($monthYear),
            'months' => $monthList,
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
		// Custom error messages
		$messages = [
			'employee_id.exists' => 'The :attribute does not exist.',
			'start_time.date_format' => 'The :attribute field must be in the correct time format.',
			'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'date.unique' => 'The employee can only have one working time per day.',
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
            'date' => 'required|unique:working_times,date,NULL,id,employee_id,' . $request->employee_id,
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
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'date' => $request->date,
        ]);

        // Session flash
        session()->flash('message', 'New working time has been added.');

        // Redirect to the business owner employee page
        return redirect('/admin/roster/' . $monthYear);
	}
}
