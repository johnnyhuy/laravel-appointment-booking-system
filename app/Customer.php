<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Support\Facades\Validator;

use App\Booking;

class Customer extends Model implements Authenticatable
{
	use AuthenticableTrait;

	protected $guarded = [];

	/**
	 *
	 * Get bookings for customer
	 *
	 */
	public function bookings()
	{
		return $this->hasMany(Booking::class);
	}
}
