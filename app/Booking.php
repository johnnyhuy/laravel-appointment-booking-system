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
	public function duration()
	{
		$startTime = Carbon::parse($this->attributes['booking_start_time']);
		$endTime = Carbon::parse($this->attributes['booking_end_time']);
		
		return $startTime->diffInSeconds($endTime);
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
