<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WorkingHours extends Model
{
	public static function getThisMonthsWorkingHours() 
	{
		//Get the working hours that are after the current date
		$workingHours = DB::table('working_hours')->where('day', '>=', Carbon::now());
		//Reduce the working hours to the ones within the next month
		$workingHours = $workingHours->where('day', '<=', Carbon::now()->addMonth());
		//Return the working hours, ordered from most recent to furthest away
		return $workingHours->orderby('day', 'asc')->get();
	}
}
