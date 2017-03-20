<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Availability;

class Employee extends Model
{
    public function scopeGetEmployee($query, $id) {
		return false;
	}
	
	public function scopeGetEmployeeAvailability($query, $id) {
		return [false];
	}
}
