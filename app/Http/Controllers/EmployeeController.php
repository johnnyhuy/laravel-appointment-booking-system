<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Employee;
use App\Booking;
use App\BusinessOwner;

class EmployeeController extends Controller
{
    public function __construct() {
        // Check auth, if not auth then redirect to login
        $this->middleware('auth:web_admin', [
            'only' => [
                'create',
                'index',
                'assign',
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

    public function assign($employee_id = null)
    {
        if ($employee_id) {
            // Return the assign employees page
            return view('admin.assign_employees', [
                'business' => BusinessOwner::first(),
                'employees' => Employee::all()->sortBy('lastname')->sortBy('firstname')->sortBy('title'),
                'selectedEmployee' => Employee::find($employee_id),
                'bookings' => Booking::getWorkableBookingsForEmployee($employee_id,30),
                'unassignBookings' => Booking::all()->where('employee_id', null)
            ]);
        }
        else {
            $employee = Employee::first();

            if ($employee) {
                return redirect('/admin/employees/assign/' . $employee->id);
            }
            else {
                return view('shared.error_page', [
                    'business' => BusinessOwner::first(),
                    'message' => 'No employees found',
                    'subMessage' => 'There seems to be no employees on the business. Create one <a href="/admin/employees">here</a>'
                ]);
            }
        }
    }
}
