<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Availability;

use Carbon\Carbon;

class Employee extends Model
{
	protected $guarded = [];
	
	public function availability() 
	{
		// Format of the time (HH:MM)
		$format = "H:i";
		// Gets the availability of the employee from the database
		$availability = Availability::where('employee_id', $this->id)->get();

		// If no availability found for given day
		if (count($availability) == 0) {
			// Return Not available string
			return 'Not Available';
		}

		// If availability is found, show start and end time
		return Carbon::parse($availability->start_time)->format('h:i A') . " - " . 
				Carbon::parse($availability->end_time)->format('h:i A');
	}

	/**
	 *
	 * Get working times from employee
	 *
	 */
	public function workingTimes()
	{
		return $this->hasMany(WorkingTime::class);
	}
}
