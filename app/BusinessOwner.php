<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;
use Illuminate\Support\Facades\Validator;

class BusinessOwner extends Model implements Authenticatable
{
    use AuthenticableTrait;

	protected $guarded = [];
}
