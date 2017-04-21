<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\WorkingTime;

use Carbon\Carbon;

class WorkingTime extends Model
{
    protected $guarded = [];

    public static function getRoster()
    {
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
     * Get the date from month and year string
     * Usage for /admin/roster/10-2017
     * Where '10-2017' is the selected string
     * 
     * @return Carbon\Carbon
     */
    public static function getDate($monthYear)
    {
        // Get the month and year from url
        $date = explode('-', $monthYear);
        $month = $date[0];
        $year = $date[1];

        // If input is invalid
        if (!is_numeric($month) or !is_numeric($year)) {
            throw new NotFoundHttpException;
        }

        return Carbon::createFromDate($year, $month);
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
