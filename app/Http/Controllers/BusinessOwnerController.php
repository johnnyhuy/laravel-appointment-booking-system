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
    public function __construct() {
        // Check if guest then stay, else redirect
        $this->middleware('guest:web_admin');
        $this->middleware('guest:web_user');
    }
    
    public function register() {
        // Redirect to /admin if business exists
        if (BusinessOwner::first() and Auth::guard('web_admin')) {
            return redirect('/admin');
        }

        return view('admin.register');
    }

    //Register's a business owner
    public function create()
    {
        // Validation error messages
        $messages = [
            'businessname.regex' => 'The :attribute is invalid, do not use special characters except "." and "-".',
            'firstname.regex' => 'The :attribute is invalid, field cannot contain special characters or numbers.',
            'lastname.regex' => 'The :attribute is invalid, field cannot contain special characters or numbers.',
            'phone.regex' => 'The :attribute is invalid, field cannot contain special characters or numbers.',
        ];

        // Validation rules
        $rules = [
            'businessname' => 'required|min:2|max:32|regex:/^[A-z0-9\-\.\s ]+$/',
            'firstname' => "required|min:2|max:32|regex:/^[A-z" . "\'" . '\-' . " ]+$/",
            'lastname' => "required|min:2|max:32|regex:/^[A-z" . "\'" . '\-' . " ]+$/",
            'username' => 'required|min:6|max:24|alpha_num|unique:customers,username',
            'password' => 'required|min:6|max:32|confirmed',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
            'address' => 'required|min:6|max:32',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'businessname' => 'business name',
            'firstname' => 'first name',
            'lastname' => 'last name',
        ];

    	// Validate form
        $this->validate(request(), $rules, $messages, $attributes);

    	// Create customer
        $businessOwner = BusinessOwner::create([
            'business_name' => request('businessname'),
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'username' => request('username'),
            'password' => bcrypt(request('password')),
            'address' => request('address'),
            'phone' => request('phone'),
        ]);

        // Session flash
        session()->flash('message', 'Business Owner registration success.');

        //Redirect to the business owner admin page
        return redirect('/login');
    }
}
