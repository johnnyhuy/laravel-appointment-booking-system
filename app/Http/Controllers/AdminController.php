<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Activity;
use App\BusinessOwner;
use App\Booking;
use App\Employee;
use App\WorkingTime;

class AdminController extends Controller
{
    // Create a business model called within the class
	protected $business;

    public function __construct()
    {
        // Get the first business owner
        // Assuming there is one business owner in the business
        $this->business = BusinessOwner::first();

        $this->middleware('auth:web_admin');
    }

    // Redirects 
    public function index()
    {
        return view('admin.index', ['business' => $this->business]);
    }

    public function summary()
    {
        return view('admin.summary', ['business' => $this->business, 'latest' => Booking::allLatest('+7 days')]);
    }

    public function employees()
    {
        return view('admin.employees', ['business' => $this->business, 'employees' => Employee::all()->sortBy('firstname')->sortBy('lastname')]);
    }

    public function history()
    {
        return view('admin.history', ['business' => $this->business, 'history' => Booking::allHistory()]);
    }

    public function roster()
    {
        return view('admin.roster', ['business' => $this->business, 'roster' => WorkingTime::getRoster()]);
    }

    public function activity()
    {
        return view('admin.activity', ['business' => $this->business, 'activities' => Activity::all()->sortBy('name')->sortBy('description')]);
    }

    public function booking()
    {
        return view('admin.booking', ['business' => $this->business, 'bookings' => Booking::allLatest()]);
    }
}
