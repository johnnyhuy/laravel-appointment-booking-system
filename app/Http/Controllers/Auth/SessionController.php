<?php

namespace App\Http\Controllers\Auth;

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

class SessionController extends Controller
{
    public function __construct() {
        $this->middleware('guest:web_user', ['except' => 'logout']);
        $this->middleware('guest:web_admin', ['except' => 'logout']);
    }

    public function index()
    {
        return view('customer.login');
    }

    public function login()
    {
        Log::info("An attempt to login was made", request(['username', 'password']));

        // Sign in as customer
        if (Auth::guard('web_user')->attempt(request(['username', 'password']))) {
            //Log customer login success
            Log::info("Customer Login with username " . request('username') . " was successful");
            // Session flash
            session()->flash('message', 'Successfully logged in!');

            // Success, go to customer's booking page
            return redirect('/bookings');
        }
        // If sign in as a customer doesn't work, attempt business owner sign in
        elseif (Auth::guard('web_admin')->attempt(request(['username', 'password']))) {
            // Log business owner login success
            Log::info("Business Owner login with username " . request('username') . " was successful");
            // Session flash
            session()->flash('message', 'Business Owner login success.');

            // Success, go to business owner's admin page
            return redirect('/admin');
        }

        //Login failed (handle failed login)
        Log::notice("An attempt to login failed with username and password: " . request('username') . ", " . request('password'));

        // Session flash
        session()->flash('error', 'Error! Invalid credentials.');

        // Return to login screen
        return back();
    }

    public function logout()
    {
        //If logged in as a customer, log out from customer
        if (Auth::guard('web_user')->check())
        {
            Auth::guard('web_user')->logout();
        }
        //If already logged in as a business owner, log out from buisness owner
        elseif (Auth::guard('web_admin')->check())
        {
            Auth::guard('web_admin')->logout();
        }
        else {
            //If the user has a non-default guard or they weren't logged in
            Log::error("Unkown user attempted logout");
        }

        //Session flash
        session()->flash('message', 'Successfully logged out!');

        //Redirect to login page
        return redirect('/login');
    }
}
