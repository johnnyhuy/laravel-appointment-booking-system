<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Booking extends Model
{
	protected $guarded = [];

	public function duration()
	{
		$startTime = Carbon::parse($this->attributes['booking_start_time']);
		$endTime = Carbon::parse($this->attributes['booking_end_time']);
		
		return $startTime->diffInSeconds($endTime);
	}
}
