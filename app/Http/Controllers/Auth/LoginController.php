<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('guest', ['except' => 'logout']);
    // }

    public function index()
    {
        return view('login.index');
    }

    public function create()
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
        session()->flash('message', 'Successfully logged in!');

        // Success
        return redirect('/bookings');
    }

    public function destroy()
    {
        Auth::logout();

        return redirect('/');
    }
}
