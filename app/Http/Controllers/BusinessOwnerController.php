<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\BusinessOwner;
use App\Booking;

class BusinessOwnerController extends Controller
{
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

    //Register's a business owner
    public function create()
    {
    	// Validate form
        $this->validate(request(), [
            'businessname' => 'required|max:255|regex:[\w+]',
            'fullname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'username' => 'required|min:6|max:24|alpha_num',
            'password' => 'required|min:6|max:32|confirmed',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
            'address' => 'required|min:6|max:32',
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
        session()->flash('message', 'Business Owner registration success');

        //Login as the business owner
        auth()->login($businessOwner);

        //Redirect to the business owner admin page
        return redirect('/login');
    }
}
