<?php

namespace Tests\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Customer;
use App\Employee;
use App\BusinessOwner;
use App\Booking;
use App\WorkingTime;

use Carbon\Carbon;

class BusinessOwnerTest extends TestCase
{
	use DatabaseTransactions;
}