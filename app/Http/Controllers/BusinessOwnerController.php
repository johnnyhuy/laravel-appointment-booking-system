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

    public function index()
    {
    	if (BusinessOwner::first() /*&& Auth::check()*/) {
    		return view('admin.index');
    	}
    	elseif (BusinessOwner::first()) {
    		return redirect('/admin/login');
    	}
    	else {
    		return redirect('/admin/register');
    	}
    }
    /**/

    protected function guard()
    {
    	return Auth::guard('web_admin');
    }

    public function showLoginForm()
    {
    	return view('admin.login');
    }

    public function showRegisterForm()
    {
    	return view('admin.register');
    }

    public function login()
    {
    	// Sign in
        if (! Auth::attempt(request(['username', 'password']))) 
        {
            // Session flash
            session()->flash('error', 'Error! Invalid credentials.');

            // Failed to login
            return back();
        }

    	// Session flash
    	session()->flash('message', 'Business Owner login success.');

    	// Success
        return redirect('/');
    }

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

        auth()->login($businessOwner);

        return redirect('/admin');
    }
}
