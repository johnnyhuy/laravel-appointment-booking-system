<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\BusinessOwner;
use App\Booking;
use App\WorkingTime;

class AdminController extends Controller
{
	protected $business;

    public function __construct()
    {
        $this->business = BusinessOwner::first();
    }

	//Returns the guard object for a business owner authentication
    protected static function guard()
    {
        return Auth::guard('web_admin');
    }

     // Redirects 
    public function index()
    {
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.index', ['business' => $this->business]);
        }
          //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return view('admin.register', ['business' => $this->business]);
        }
    }

    public function summary() {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.summary', ['business' => $this->business, 'latest' => Booking::allLatest('+7 days')]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return view('admin.register', ['business' => $this->business]);
        }
    }

    public function employees() 
    {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.employees', ['business' => $this->business]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }

    public function history() 
    {
        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            // Pass business and booking history data
            return view('admin.history', ['business' => $this->business, 'history' => Booking::allHistory()]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }

    public function roster() 
    {
        // Get first record of business owner
        $business = BusinessOwner::first();

        //If a business owner exists, and you are logged in as the business owner,
        //show the business owner page
        if (BusinessOwner::first() && $this->guard()->check()) 
        {
            return view('admin.roster', ['business' => $this->business, 'roster' => WorkingTime::getRoster()]);
        }
        //If a business owner exists, but you are not loggined in as the bussiness
        //owner, then redirect to the login page
        elseif (BusinessOwner::first()) 
        {
            return redirect('/login');
        }
        //If a user is logged in, they should not be able to access this page
        elseif (Auth::guard('web_user')->check()) 
        {
            return redirect('/');
        }
        //If no business owner exists, show the business owner registration page
        else 
        {
            return redirect('/admin');
        }
    }
}
