<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\WorkingHours;
use Carbon\Carbon;

class WorkingHoursController extends Controller
{
	//Create a new working hours object
	public function create()
	{
		$nextMonth = Carbon::now()->addMonth();
		// Validate form
        $this->validate(request(), [
        	//Must be a date after today, before a month from now and mustn't already have a working hour object for that day
            'date' => 'required|after:today|before:' . date("d-m-Y", strtotime($nextMonth)) . '|unique:working_hours,day',
            //Start time is required
            'start_time' => 'required',
            //End time is required and must be AFTER the start time (they can't be the same either)
            'end_time' => 'required|after:start_time',
        ]);

        //Create the working hours object
        $wh = new WorkingHours;
        $wh->day = request('date');
        $wh->start_time = request('start_time');
        $wh->end_time = request('end_time');
        $wh->save();

        // Session flash
        session()->flash('message', 'New Working Hours Added');

        //Redirect to the business owner employee page
        return redirect('/admin/roster');
	}
}
