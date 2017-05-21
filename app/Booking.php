<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Activity;
use App\WorkingTime;

use Carbon\Carbon as Time;

class Booking extends Model
{
	protected $guarded = [];

	/**
	 * Calculate end time of a booking given its activity duration
	 *
	 * @return string
	 */
	public static function calcEndTime($pDuration, $pStartTime) {
		// Set duration
		$duration = Time::parse($pDuration);

		try {
			// Set start time
			$startTime = Time::parse($pStartTime);
		}
		catch (\Exception $e) {
			return false;
		}

	    // Calculate end time
	    return Time::createFromTime($startTime->hour, $startTime->minute)
	    	->addHours($duration->hour)
	    	->addMinutes($duration->minute)
	    	->toTimeString();
	}

	/**
	 * Calculate the duration of the booking
	 *
	 * @return string
	 */
	public function duration($toTimeString = false)
	{
		// Set start and end time
		$startTime = Time::parse($this->attributes['start_time']);
		$endTime = Time::parse($this->attributes['end_time']);

		// Get difference in time
		$duration = $startTime->diffInSeconds($endTime);

		// Convert to time string
		if ($toTimeString) {
			$duration = gmdate('G:i', $duration);
		}

		// Return duration
		return $duration;
	}

	/**
	 * Show all history of bookings
	 *
	 * @return App\Booking
	 */
	public static function allHistory() {
		// Return past bookings eloquent model
		return Booking::where('date', '<', Time::now('Australia/Melbourne')->toDateString())
			->get()
			// Sort by start time using an eloquent collection function
			->sortByDESC('date');
	}

	/**
	 * Show all latest of bookings
	 *
	 * @return App\Booking
	 */
	public static function allLatest($max = null) {
		$booking = Booking::where('date', '>=', Time::now('Australia/Melbourne')->toDateString());

		if (isset($max)) {
			$max = Time::now('Australia/Melbourne')->addDays($max);
			$booking->where('date', '<=', $max);
		}

		// Return latest bookings eloquent model
		return $booking
			// Get eloquent model
			->get()
			// Sort by start time using an eloquent collection function
			->sortBy('date');
	}

	/**
	 * Show all bookings a given employee is available to work
	 *
	 * @return App\Booking[]
	 */
	public static function getWorkableBookingsForEmployee($employeeID, $ndays)
	{
		// Get all bookings from the next 30 days
		$bookings = Booking::allLatest($ndays);

		// Get all working times for the employee for next 30 days
		$workingTimes = WorkingTime::getWorkingTmesForEmployee($employeeID, $ndays)->get();

		// Final bookings
		$finalBookings = [];

		// Iterate through each booking
		foreach ($bookings as $booking) {
			// Iterate through each working time
			foreach ($workingTimes as $workingTime) {
				// If the employee is working during the entirety of this booking
				// And the booking is on the same day the employee is working
				if ($workingTime->start_time <= $booking->start_time &&
					$workingTime->end_time >= $booking->end_time &&
					$workingTime->date == $booking->date) {

					// Push booking to list of final bookings
					array_push($finalBookings, $booking);
				}
			}
		}

		// Return bookings
		return $finalBookings;
	}

	/**
	 * Get employee from bookings
	 *
	 * @return \App\Employee
	 */
	public function employee()
	{
		return $this->belongsTo(Employee::class);
	}

	/**
	 * Get customer from bookings
	 *
	 * @return \App\Customer
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	/**
	 * Get activity from bookings
	 *
	 * @return \App\Activity
	 */
	public function activity()
	{
		return $this->belongsTo(Activity::class);
	}
}
