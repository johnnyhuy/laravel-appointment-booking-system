<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\WorkingTime;
use Carbon\Carbon;

class WorkingTimeController extends Controller
{
    // Create a new working time
	public function create(Request $request)
	{
        // Request day and week
        $day = request('day');
        $week = request('week');

        // Set date as request
        $date = request('date');

        if (isset($day) and isset($week)) {
            // Create a date out of days and weeks
            $date = Carbon::now()
                ->addMonth()
                ->startOfMonth()
                ->startOfWeek()
                ->addDays($day)
                ->addWeeks($week)
                ->toDateString();

            // Put into request date field
            $request->merge(['date' => $date]);
        }

        $afterDate = Carbon::now()
            ->addMonth()
            ->startOfMonth()
            ->startOfWeek()
            ->subDay()
            ->toDateString();
        $beforeDate = Carbon::now()
            ->addMonth()
            ->endOfMonth()
            ->endOfMonth()
            ->addDay()
            ->toDateString();
        
		// Custom error messages
		$messages = [
			'employee_id.exists' => 'Employee does not exist.',
			'start_time.date_format' => 'The :attribute field must be in the correct time format.',
			'end_time.date_format' => 'The :attribute field must be in the correct time format.',
            'date.before' => 'The :attribute must be a date after within the weeks of next month.',
            'date.after' => 'The :attribute must be a date after within the weeks of next month.',
		];

		// Validation rules
		$rules = [
            // Employee ID is required and must exist in employees table
            'employee_id' => 'required|exists:employees,id',

            // Start time is required
            'start_time' => 'required|before:end_time|date_format:H:i',

            // End time is required and must be AFTER the start time (they can't be the same either)
            'end_time' => 'required|after:start_time|date_format:H:i',

        	// Must be a date after today, before a month from now
        	// Note: 'before last day of this month' means it is 'before the first day of next month' for some unknown reason
            'date' => 'required|after:' . $afterDate . '|before:' . $beforeDate,
        ];

		// Validate form
        $this->validate(request(), $rules, $messages);

        // Create a working time
        $workingTime = WorkingTime::create([
            'employee_id' => request('employee_id'),
            'start_time' => request('start_time'),
            'end_time' => request('end_time'),
            'date' => $date,
        ]);

        // Session flash
        session()->flash('message', 'New working time has been added.');

        // Redirect to the business owner employee page
        return redirect('/admin/roster');
	}
}
