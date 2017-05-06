<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\WorkingTime;

use Carbon\Carbon as Time;

class WorkingTime extends Model
{
    protected $guarded = [];

    public static function getRoster()
    {
    	// Start of week in the month
    	$startDate = Time::now('Australia/Melbourne')
    		->addMonth()
    		->startOfMonth()
    		->startOfWeek()
    		// Subtract a day to capture the first day of week
    		->subDay();
    	// End of week in the month
    	$endDate = Time::now('Australia/Melbourne')
    		->addMonth()
    		->endOfMonth()
    		->endOfWeek()
    		// Add a day to capture last day of week
    		->addDay();

    	return WorkingTime::whereBetween('date', [$startDate, $endDate])
    		// Get eloquent model
    		->get()
            ->sortBy('end_time')
    		->sortBy('start_time');
    }

    /**
     *
     * Get the working times of an employee for a given amount of days
     *
     */
    public static function getWorkingTmesForEmployee($employeeID, $days)
    {
        //Get all working times for a particular employee
        $workingTimes = WorkingTime::where('employee_id', $employeeID);

        //Get working times from today onwards
        $workingTimes = $workingTimes->where('date', '>=', Time::now('Australia/Melbourne')->toDateString());

        //Final day of working times
        $max = Time::now('Australia/Melbourne')->addDays($days);

        //Restrict working times to amount of days
        $WorkingTimes = $workingTimes->where('date', '<', $max);

        //Return the working times for the employee
        return $workingTimes;
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
