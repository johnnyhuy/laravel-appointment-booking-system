<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class WorkingHours extends Model
{
	public function scopeGetWorkingHoursThisMonth($query) {
		return false;
	}
}
