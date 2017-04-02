<?php

namespace App\Http\Controllers;

use App\Employee;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    //Register a new employee
    public function create()
    {
    	// Validate form
        $this->validate(request(), [
            'firstname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'lastname' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'title' => 'required|max:255|regex:/^[A-z\-\. ]+$/',
            'phone' => 'required|min:10|max:24|regex:/^[0-9\-\+\.\s\(\)x]+$/',
        ]);

        $employee = new Employee;
        $employee->firstname = request('firstname');
        $employee->lastname = request('lastname');
        $employee->title = request('title');
        $employee->phone = request('phone');
        $employee->save();

        // Session flash
        session()->flash('message', 'New Employee Added');

        //Redirect to the business owner employee page
        return redirect('/admin/employees');
    }
}
