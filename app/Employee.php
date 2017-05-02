<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Availability;

use Carbon\Carbon;

class Employee extends Model
{
	protected $guarded = [];

	/**
	 * Get the available times of an employee at a given date
	 *
	 * @param  string $date
	 * @return Array
	 */
	public function availableTimes($date) {
		Log::info("Called availableTimes() on" . $date . " from employee ID " . $this->id);

		// Get working time
		$workingTime = $this->workingTimes->where('date', $date)->first();

		// Get employee bookings
        $bookings = $this->bookings->where('date', $date)->sortBy('start_time');

        // Index the available time array
        $i = 0;

        // Set available time to working time
        $avaTimes[$i]['start_time'] = $workingTime->start_time;
        $avaTimes[$i]['end_time'] = $workingTime->end_time;

        foreach ($bookings as $booking) {
            if ($avaTimes[$i]['end_time'] != $avaTimes[$i]['start_time']) {
                // Switch times
                $avaTimes[$i]['end_time'] = $booking->start_time;

                // If avail and booking start time are the same, go back
                if ($avaTimes[$i]['start_time'] == $booking->start_time) {
                    $i--;
                }

                // Go to next index
                $i++;

                // IF booking and working end time is the same, go back
                if ($booking->end_time != $workingTime->end_time) {
                    // Switch times
                    $avaTimes[$i]['start_time'] = $booking->end_time;

                    // Default set end time as working time end time
                    $avaTimes[$i]['end_time'] = $workingTime->end_time;
                }
                else {
                    $i--;
                }
            }
        }

        return $avaTimes;
	}

	/* depreciated
	public function availability($day)
	{
		if ($day == 1) $day = "SUNDAY";
		if ($day == 2) $day = "MONDAY";
		if ($day == 3) $day = "TUESDAY";
		if ($day == 4) $day = "WEDNESDAY";
		if ($day == 5) $day = "THURSDAY";
		if ($day == 6) $day = "FRIDAY";
		if ($day == 7) $day = "SATURDAY";

		// Format of the time (HH:MM)
		$format = "H:i";
		// Gets the availability of the employee from the database
		$availability = Availability::where('employee_id', $this->id)
						->where('day', $day)
						->get();

		// If no availability found for given day
		if (count($availability) == 0) {
			// Return Not available string
			return 'Not Available';
		}

		// If availability is found, show start and end time
		return Carbon::parse($availability[0]->start_time)->format('h:i A') . " - " .
				Carbon::parse($availability[0]->end_time)->format('h:i A');
	}
	*/

	/**
	 * Get working times of an employee
	 *
	 * @return Relationship
	 */
	public function workingTimes()
	{
		return $this->hasMany(WorkingTime::class);
	}

	/**
	 * Get bookings of an employee
	 *
	 * @return Relationship
	 */
	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}
}
