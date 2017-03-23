<?php

namespace App\Http\Controllers\Auth;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    public function create()
    {
        // dd(request()->all());

        // Validate form
        $this->validate(request(), [
            'firstname' => 'required|max:255|regex:[\w+]',
            'lastname' => 'required|max:255|regex:[\w+]',
            'username' => 'required|min:6|regex:[\w*\d*]',
            'password' => 'required|min:6|confirmed|regex:[\w+d+]',
            'address' => 'required|regex:[\d{1,5}\s\w{1,30}\s(\b\w*\b){1,4}\w*\s*\,*\s*\w{1,30}\s*\,*\s*\d{0,4}]',
            'phone' => 'required|min:4|max:22|regex:[\d+]',
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
        session()->flash('message', 'Customer ' . request('firstname') . ' ' . request('lastname') . 'has been registered!');

        // Sign in
        auth()->login($customer);

       return redirect('/bookings');
    }

    public function index()
    {
        return view('register.index');
    }
}

