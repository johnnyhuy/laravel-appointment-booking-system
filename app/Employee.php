<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Availability;

class Employee extends Model
{
	public static function getEmployeeAvailability($employeeID, $day) 
	{
		//Format of the time (HH:MM)
		$format = "H:i";
		//Gets the availability of the employee from the database
		$availability = DB::table('availabilities')->where('id', $employeeID)->where('day', $day)->get();
		//If no availability found for given day
		if(!count($availability)) {
			//Return Not available string
			return 'Not Available';
		}
		//If availability found then return a string to show availablity in format (HH:MM - HH:MM)
		return date($format, strtotime($availability[0]->start_time)) . " - " . 
				date($format, strtotime($availability[0]->end_time));
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
