<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessTime extends Model
{
    protected $guarded = [];

    // Disable timestamps
    public $timestamps = false;
}
