<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Activity;
use App\Booking;
use App\BusinessOwner;
use App\Customer;
use App\Employee;
use App\WorkingTime;

use Carbon\Carbon;

class ActivityController extends Controller
{
    public function __construct() {
        // Business Owner auth
        $this->middleware('auth:web_admin');
    }

    public function index()
    {
        return view('admin.activity', [
            'business' => BusinessOwner::first(),
            'activities' => Activity::all()->sortBy('name')->sortBy('description')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::info("An attempt was made to create a new Activity", $request->all());

        // Validation error messages
        $messages = [
            'name.regex' => 'The :attribute is invalid, do not use special characters.',
            'duration.date_format' => 'The :attribute field must be in the correct time format (e.g. 4:00 or 16:30).',
            'duration.after' => 'The :attribute field cannot be zero.',
        ];

        // Validation rules
        $rules = [
            'name' => 'required|unique:activities,name|min:2|max:32|regex:/^[A-z0-9\s]+$/',
            'description' => 'max:64',
            'duration' => 'required|date_format:H:i|after:00:00',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'name' => 'activity name',
        ];

        // Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Create customer
        $activity = Activity::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);

        Log::notice("A new activity was created");

        // Session flash
        session()->flash('message', 'Activity has successfully been created.');

        //Redirect to the business owner admin page
        return redirect('/admin/activity');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function edit(Activity $activity)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Find activity by ID
        $activity = Activity::find($id);

        // Validation error messages
        $messages = [
            'name.alpha_num' => 'The :attribute is invalid, do not use special characters except "." and "-".',
            'duration.date_format' => 'The :attribute field must be in the correct time format (e.g. 4:00 or 16:30).',
        ];

        // Validation rules
        $rules = [
            'name' => 'required|min:2|max:32|alpha_num',
            'description' => 'min:2|max:64',
            'duration' => 'required|date_format:H:i',
        ];

        // Attributes replace the field name with a more readable name
        $attributes = [
            'name' => 'activity name',
        ];

        // Validate form
        $this->validate($request, $rules, $messages, $attributes);

        // Set variables once validated
        $activity->name = $request->name;
        $activity->description = $request->description;
        $activity->duration = $request->duration;

        // Save activity
        $activity->save();

        // Session flash
        session()->flash('message', 'Activity has successfully been edited.');

        // Redirect to activity page
        return redirect('/admin/activity');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Find activity
        $activity = Activity::find($id);

        //Check the activity exists
        if(!isset($activity)) {
            //If it doesn't exist, log an error
            Log::error("Activity with id " . $id . ", could not be destroyed as it doesn't exist");
        }

        // Delete activity
        $activity->delete();

        // Session flash
        session()->flash('message', 'Activity has successfully been removed.');

        // Redirect to activity page
        return redirect('/admin/activity');
    }
}
