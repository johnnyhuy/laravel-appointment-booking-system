<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Booking extends Model
{
	protected $guarded = [];

	/**
	 *
	 * Calculate the duration of the booking
	 *
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
	 *
	 * Show all history of bookings
	 *
	 */
	public static function allHistory() {
		// Return past bookings eloquent model
		return Booking::where('date', '<', Carbon::now()->subDay())	
			->get()	
			// Sort by start time using an eloquent collection function
			->sortByDESC('date');
	}

	/**
	 *
	 * Show all latest of bookings
	 *
	 */
	public static function allLatest($max = null) {
		$startDay = Carbon::now()->startOfDay();
		$booking = Booking::where('date', '>=', $startDay);

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
	 *
	 * Get customer from bookings
	 *
	 */
	public function customer()
	{
		return $this->belongsTo(Customer::class);
	}

	/**
	 *
	 * Get activity from bookings
	 *
	 */
	public function activity()
	{
		return $this->belongsTo(Activity::class);
	}
}
