<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class Customer extends Model implements Authenticatable
{
	use AuthenticableTrait;

	protected $guarded = [];

	public function scopeGetBookings($query, $id) {
		return false;
	}
}
