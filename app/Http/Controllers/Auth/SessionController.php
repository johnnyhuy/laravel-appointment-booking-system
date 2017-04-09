<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BusinessOwnerController;

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
        // Sign in as customer
        if (Auth::guard('web_user')->attempt(request(['username', 'password']))) {
            // Session flash
            session()->flash('message', 'Successfully logged in!');

            // Success, go to customer's booking page
            return redirect('/bookings');
        }
        // If sign in as a customer doesn't work, attempt business owner sign in
        elseif (Auth::guard('web_admin')->attempt(request(['username', 'password']))) {
             // Session flash
            session()->flash('message', 'Business Owner login success.');

            // Success, go to business owner's admin page
            return redirect('/admin');
        }

        //Login failed (handle failed login)

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

        //Session flash
        session()->flash('message', 'Successfully logged out!');

        //Redirect to login page
        return redirect('/login');
    }

   
}
