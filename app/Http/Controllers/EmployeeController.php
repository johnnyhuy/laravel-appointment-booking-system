<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        Log::info("An attempt was made to create a new employee", $request->all());

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

        Log::notice("A new employee was created with name: " . $request['firstname'] . " " . $request['lastname']);

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

    public function assignEmployees($employee_id = null) 
    {
        if(!isset($employee_id)) {
            if(Employee::first() !== null) {
                //Set the first employee to default
                $employee_id = Employee::first()->id;
            }else {
                $employee_id = 0;
            }   
        }
        
        //Return the assign employees page
        return view('admin.assign_employees', [
            'business' => BusinessOwner::first(),
            'employee_id' => $employee_id
        ]);
    }
}
