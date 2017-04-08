<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    // Create a new employee
    public function create()
    {
    	// Validate form
        $this->validate(request(), [
            'firstname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'lastname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'title' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
        ]);

        // Create employee
        Employee::create([
            'firstname' => request('firstname'),
            'lastname' => request('lastname'),
            'title' => request('title'),
            'phone' => request('phone'),
        ]);

        // Session flash
        session()->flash('message', 'New Employee Added');

        //Redirect to the business owner employee page
        return redirect('/admin/employees');
    }
}
