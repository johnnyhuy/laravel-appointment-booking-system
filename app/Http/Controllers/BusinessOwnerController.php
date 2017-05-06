<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\RegistersUsers;
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

class BusinessOwnerController extends Controller
{
    public function __construct() {
        // Business Owner auth
        $this->middleware('auth:web_admin', [
            'only' => [
                'index',
            ]
        ]);

        // Guest view
        // Allow guests to register business
        $this->middleware('guest:web_user', [
            'only' => [
                'create',
                'register',
            ]
        ]);
    }

    /**
     * Show business information
     * E.g. Business name, owner full name
     */
    public function index() {
        return view('admin.index', ['business' => BusinessOwner::first()]);
    }

     /**
     * Show business registration form
     * Registers the business once
     */
    public function register() {
        // Redirect to /admin if business exists
        if (BusinessOwner::first() and Auth::guard('web_admin')) {
            Log::notice('Tried to visit business owner registration form, redirected to /admin since business already exists');
            return redirect('/admin');
        }

        return view('admin.register');
    }

     /**
     * Send receives a POST request
     * Creates Business Owner
     * Includes all business information
     */
    public function create(Request $request)
    {
        //Check a business owner doesn't already exist
        if (count(BusinessOwner::all()) > 1) {
            //Log a critical failure if an attempt is made to register more than 1 business
            Log::critical("More than one business was attempted to be registered", $request->all());
            return 0;
        }

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
        $this->validate($request, $rules, $messages, $attributes);

    	// Create customer
        $businessOwner = BusinessOwner::create([
            'business_name' => $request->businessname,
            'firstname' => ucfirst($request->firstname),
            'lastname' => ucfirst($request->lastname),
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'address' => $request->address,
            'phone' => $request->phone,
        ]);

        // Log business owner creation
        Log::notice("A Business Owner was registered with username " . $businessOwner->username);

        // Session flash
        session()->flash('message', 'Business Owner registration success.');

        //Redirect to the business owner admin page
        return redirect('/login');
    }
}
