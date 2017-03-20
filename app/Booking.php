<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
	public function scopeGetAvailableBookings($query) {
		return false;
	}

	public function scopeGetBookingsSummary($query) {
		return [false];
	}
}
