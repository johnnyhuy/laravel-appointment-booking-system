<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;

class Activity extends Model
{
	protected $guarded = [];

	// Declare public accessors
	public $hour, $minute;

	public function __construct(array $attributes = array()) {
		// Run parent Eloquent model construct method before setup
		parent::__construct($attributes);

		// Set public accessors
		$this->hour = Carbon::parse($this->duration)->hour;
		$this->minute = Carbon::parse($this->duration)->minute;
	}

    /**
	 * Get bookings from activity
	 * 
	 * @return \App\Booking
	 */
	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}
}
