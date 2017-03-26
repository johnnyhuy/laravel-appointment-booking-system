<?php

namespace App\Http\Controllers;

use App\BusinessOwner;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class BusinessOwnerController extends Controller
{
	use RegistersUsers;

    //Returns the guard object for a business owner authentication
    protected static function guard()
    {
        return Auth::guard('web_admin');
    }

    //Returns true if form information is correct to log into business owner account
    public static function login() 
    {
        //Attempt to login as the business owner
        return BusinessOwnerController::guard()->attempt(request(['username', 'password']));
    }

    //Redirect to appropriate page
    public function index()
    {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
    	if (BusinessOwner::first() && $this->guard()->check())
        {
    		return view('admin.index');
    	}
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
    	elseif (BusinessOwner::first()) 
        {
    		return redirect('/login');
    	}
        //If no business owner exists, show the business owner registration page
    	else {
    		return view('admin.register');
    	}
    }

    //Register's a business owner
    public function create()
    {
    	// Validate form
        $this->validate(request(), [
            'businessname' => 'required|max:255|regex:[\w+]',
            'fullname' => 'required|max:255|regex:[\w+]',
            'username' => 'required|min:6|regex:[\w*\d*]',
            'password' => 'required|min:6|confirmed|regex:[\w+d+]',
            'address' => 'required|regex:[\d{1,5}\s\w{1,30}\s(\b\w*\b){1,4}\w*\s*\,*\s*\w{1,30}\s*\,*\s*\d{0,4}]',
            'phone' => 'required|min:8|max:11|regex:[\d+]',
        ]);

    	// Create customer
        $businessOwner = BusinessOwner::create([
            'business_name' => request('businessname'),
            'owner_name' => request('fullname'),
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'address' => request('address'),
            'phone' => request('phone'),
        ]);

        // Session flash
        session()->flash('message', 'Business Owner registration success.');

        //Login as the business owner
        auth()->login($businessOwner);

        //Redirect to the business owner admin page
        return redirect('/admin');
    }
}
