<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
	protected $guarded = [];
	
    /**
	 *
	 * Get bookings from activity
	 *
	 */
	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}
}
