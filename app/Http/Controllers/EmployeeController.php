<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Employee;
use App\BusinessOwner;

class EmployeeController extends Controller
{
    public function __construct() {
        // Check auth, if not auth then redirect to login
        $this->middleware('auth:web_admin', [
            'only' => [
                'create',
                'index',
            ]
        ]);
    }

    // Create a new employee
    public function create(Request $request)
    {
    	// Validate form
        $this->validate($request, [
            'firstname' => 'required|min:2|max:32|regex:/^[A-z\-\.' . "\'" . ' ]+$/',
            'lastname' => 'required|min:2|max:32|regex:/^[A-z\-\.' . "\'" . ' ]+$/',
            'title' => 'required|min:2|max:32|regex:/^[A-z\-\.' . "\'" . ' ]+$/',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
        ]);

        // Create employee
        Employee::create([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'title' => $request->title,
            'phone' => $request->phone,
        ]);

        // Session flash
        session()->flash('message', 'New Employee Added');

        //Redirect to the business owner employee page
        return redirect('/admin/employees');
    }

    public function index()
    {
        return view('admin.employees', [
            'business' => BusinessOwner::first(),
            'employees' => Employee::all()->sortBy('firstname')->sortBy('lastname')
        ]);
    }
}
