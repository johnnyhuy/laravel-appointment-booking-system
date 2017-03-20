<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
	public function scopeGetBookings($query, $id) {
		return false;
	}
}
