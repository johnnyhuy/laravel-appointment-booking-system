<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon as Time;

class CustomerController extends Controller
{
    public function __construct() {
        // Check if guest then stay, else redirect
        $this->middleware('guest:web_admin');
        $this->middleware('guest:web_user');
    }

	// Opens the customer registration page
	public function register()
	{
		return view('customer.register');
	}

	// Registers a new customer account
	public function create()
	{
        Log::info("An attempt was made to register a customer account", request()->all());

		// Validation rules
		$rules = [
            'firstname' => "required|min:2|max:32|regex:/^[A-z\']+$/",
            'lastname' => "required|min:2|max:32|regex:/^[A-z\']+$/",
            'username' => 'required|min:4|max:24|alpha_num|unique:customers,username|unique:business_owners,username',
            'password' => 'required|min:6|max:32|confirmed',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
            'address' => 'required|min:6|max:32',
        ];

		// Validate form
        $this->validate(request(), $rules);

        // Create customer
        $customer = Customer::create([
            'firstname' => ucfirst(request('firstname')),
            'lastname' => ucfirst(request('lastname')),
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'address' => request('address'),
            'phone' => request('phone'),
        ]);

        Log::notice("A customer account with user_id and username: " . $customer->id . ', '. $customer->username . " was created");

        // Session flash
        session()->flash('message', 'Thank you for registering! You can now Login!');

       	return redirect('/login');
	}
}
