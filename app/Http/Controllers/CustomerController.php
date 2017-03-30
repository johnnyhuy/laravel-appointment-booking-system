<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Customer;

class CustomerController extends Controller
{
	protected static function guard()
    {
        return Auth::guard('web_user');
    }

	//Redirects user to approiate page
	public function index() 
	{
		//If user already logged in
		if(Auth::guard('web_user')->Check()) 
		{
			//Redirect to the bookings page, as user is already logged in
			return view('bookings.index');
		}
		//If the user is not logged in
		else
		{
			//Go to the login page
			return redirect('/login');
		}
	}

	// Opens the customer registration page
	public function register() 
	{
		return view('customer.register');
	}

    //Attempts to login using post data from a form, returns true if successful
    public static function login() 
    {
    	//Uses 'username' and 'password' data from form to attempt to log in
		return CustomerController::guard()->attempt(request(['username', 'password']));
	}

	//Registers a new customer account
	public function create() 
	{
		// Validate form
        $this->validate(request(), [
            'firstname' => "required|min:2|max:32|regex:/^[A-z\']+$/",
            'lastname' => "required|min:2|max:32|regex:/^[A-z\']+$/",
            'username' => 'required|min:6|max:24|alpha_num|unique:customers,username|unique:business_owners,username',
            'password' => 'required|min:6|max:32|confirmed',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
            'address' => 'required|min:6|max:32',
        ]);

        // Create customer
        $customer = Customer::create([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'address' => request('address'),
            'phone' => request('phone'),
        ]);

        // Session flash
        session()->flash('message', 'Thank you for registering! You can now Login!');

       	return redirect('/login');
	}
}
