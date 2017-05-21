<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\Booking;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon as Time;

class WorkingTime extends Model
{
    protected $guarded = [];

    /**
     * Get the roster
     *
     * @return WorkingTime
     */
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
     * Get the working times of an employee for a given amount of days
     *
     * @return WorkingTime
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
	 * Get employee from working time
     *
	 * @return Employee
	 */
	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

    /**
     * Removes all future bookings.
     *
     * @return void
     */
    public function deleteBookings()
    {
        // Count the amount of bookings removed
        $bookingCount = 0;

        // Delete remaining booking after today on a day of week
        foreach (Booking::where('date', $this->date)->where('employee_id', $this->employee_id)->get() as $booking) {
            $booking->delete();
            $bookingCount++;
        }

        Log::notice("Deleted " . $bookingCount . " previous booking(s)");
    }
}
