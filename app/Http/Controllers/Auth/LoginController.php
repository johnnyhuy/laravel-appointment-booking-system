<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\BusinessOwnerController;

class LoginController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    public function index()
    {
        //If already logged in as a user, redirect to user's page
        if(Auth::guard('web_user')->Check()) 
        {
            return redirect('/bookings');
        }
        //If already logged in as a business owner, redirect to admin page
        else if(Auth::guard('web_admin')->Check()) 
        {
            return redirect('/admin');
        }
        //If not logged in, show the login page
        else 
        {
            return view('customer.login');
        }
        
    }

    public function login()
    {
      
        // Sign in as customer
        if(CustomerController::login()) {
            // Session flash
            session()->flash('message', 'Successfully logged in!');

            // Success, go to customer's booking page
            return redirect('/bookings');
        }
        //If sign in as a customer doesn't work, attempt business owner sign in
        else if(BusinessOwnerController::login()) {
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
        if(Auth::guard('web_user')->Check()) 
        {
            Auth::guard('web_user')->logout();
        }
        //If already logged in as a business owner, log out from buisness owner
        else if(Auth::guard('web_admin')->Check()) 
        {
            Auth::guard('web_admin')->logout();
        }

        //Session flash
        session()->flash('message', 'Successfully logged out!');

        //Redirect to login page
        return redirect('/');
    }

   
}
