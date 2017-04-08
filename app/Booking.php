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
			// Get eloquent model
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
		$startDay = Carbon::now()->addDay();
		$booking = Booking::where('date', '>', $startDay);

		if (isset($max)) {
			$max = Carbon::parse($max);
			$booking = Booking::whereBetween('date', [$startDay->toDateString(), $max->toDateString()]);
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
}
