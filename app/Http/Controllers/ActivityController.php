<?php

namespace App\Http\Controllers;

use App\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
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

        // Create customer
        $activity = Activity::create([
            'name' => $request->name,
            'description' => $request->description,
            'duration' => $request->duration,
        ]);

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
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Activity $activity)
    {
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

        dd($activity);

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

        //Redirect to the business owner admin page
        return redirect('/admin/activity');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Activity  $activity
     * @return \Illuminate\Http\Response
     */
    public function destroy(Activity $activity)
    {
        //
    }
}
