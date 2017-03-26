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
	//Redirects user to approiate page
	public function index() 
	{
		//If user already logged in
		if(Auth::guard('web')->Check()) 
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

	//Opens the customer registration page
	public function register() 
	{
		return view('register.index');
	}

	//Returns the guard object for customer registration
	protected static function guard()
    {
    	return Auth::guard('web');
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
            'firstname' => 'required|max:255|regex:[\w+]',
            'lastname' => 'required|max:255|regex:[\w+]',
            'username' => 'required|min:6|regex:[\w*\d*]',
            'password' => 'required|min:6|confirmed|regex:[\w+d+]',
            'address' => 'required|regex:[\d{1,5}\s\w{1,30}\s(\b\w*\b){1,4}\w*\s*\,*\s*\w{1,30}\s*\,*\s*\d{0,4}]',
            'phone' => 'required|min:10|max:11|regex:[\d+]',
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

       	return redirect('/');
	}
}
