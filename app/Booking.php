<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Activity;

use Carbon\Carbon;

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
		$duration = Carbon::parse($pDuration);

		// Set start time
		$startTime = Carbon::parse($pStartTime);

	    // Calculate end time
	    return Carbon::createFromTime($startTime->hour, $startTime->minute)
	    	->addHours($duration->hour)
	    	->addMinutes($duration->minute)
	    	->format('H:i');
	}

	/**
	 * Calculate the duration of the booking
	 *
	 * @return string
	 */
	public function duration($toTimeString = false)
	{
		// Set start and end time
		$startTime = Carbon::parse($this->attributes['start_time']);
		$endTime = Carbon::parse($this->attributes['end_time']);

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
		return Booking::where('date', '<', Carbon::now()->toDateString())
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
		$booking = Booking::where('date', '>=', Carbon::now()->toDateString());

		if (isset($max)) {
			$max = Carbon::now()->addDays($max);
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
