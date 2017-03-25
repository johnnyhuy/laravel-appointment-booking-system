<?php

namespace App\Http\Controllers\Auth;

use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class RegisterController extends Controller
{
    public function __construct()
    {
        // $this->middleware('guest');
    }

    public function create()
    {
        // dd(request()->all());

        // Validate form
        $this->validate(request(), [
            'firstname' => 'required|max:255|alpha',
            'lastname' => 'required|max:255|alpha',
            'username' => 'required|min:6|alpha_num|unique:customers,username',
            'password' => 'required|min:6|confirmed',
            'phone' => 'required|min:4|max:22|numeric',
            'address' => 'required|min:8|max:15',
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
        session()->flash('message', 'Customer ' . request('firstname') . ' ' . request('lastname') . ' has been registered!');

        // Sign in
        auth()->login($customer);

       return redirect('/bookings');
    }

    public function index()
    {
        return view('register.index');
    }
}

