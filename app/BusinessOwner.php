<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class BusinessOwner extends Model implements Authenticatable
{
    use AuthenticableTrait;

    protected $guard = 'web_admin';

	protected $guarded = [];
}
