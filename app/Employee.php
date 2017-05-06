<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Carbon\Carbon as Time;

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
		// Get working time
		$workingTime = $this->workingTimes->where('date', $date)->first();

        // Get employee bookings
        $bookings = $this->bookings->where('date', $date)->sortBy('start_time');

        if (!$workingTime || !$bookings) {
            return null;
        }

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
                    array_pop($avaTimes);
                }

                // IF booking and working end time is the same, go back
                if ($booking->end_time != $workingTime->end_time) {
                    $i++;

                    // Switch times
                    $avaTimes[$i]['start_time'] = $booking->end_time;

                    // Default set end time as working time end time
                    $avaTimes[$i]['end_time'] = $workingTime->end_time;
                }
            }
        }

        return $avaTimes;
	}

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
