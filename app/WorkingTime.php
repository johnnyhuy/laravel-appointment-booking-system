<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\WorkingTime;

use Carbon\Carbon;

class WorkingTime extends Model
{
    protected $guarded = [];

    public static function getRoster() {
    	// Start of week in the month
    	$startDate = Carbon::now()
    		->addMonth()
    		->startOfMonth()
    		->startOfWeek()
    		// Subtract a day to capture the first day of week
    		->subDay();
    	// End of week in the month
    	$endDate = Carbon::now()
    		->addMonth()
    		->endOfMonth()
    		->endOfWeek()
    		// Add a day to capture last day of week
    		->addDay();

    	return WorkingTime::whereBetween('date', [$startDate, $endDate])
    		// Get eloquent model
    		->get()
    		// Sort by start time of working time
    		->sortBy('start_time');
    }

    /**
	 *
	 * Get employee from working time
	 *
	 */
	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}
}
