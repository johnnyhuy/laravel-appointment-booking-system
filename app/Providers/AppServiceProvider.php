<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Dusk\DuskServiceProvider;

use App\Activity;
use App\Booking;
use App\WorkingTime;

use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
		// Additional code to fix php artisan migrate error for (unique key too long on certain systems)
        Schema::defaultStringLength(191);

        // Create a validator to check if an employee is free when adding a booking
        Validator::extend('is_on_booking', function ($attribute, $value, $parameters, $validator) {
            // Get request data
            $request = $validator->getData();

            // If request data is not provided then return false
            if (!isset($request['date']) or !isset($request['start_time']) or !isset($request['activity_id'])) {
                return false;
            }

            // Find activity or return false
            try {
                $activity = Activity::findOrFail($request['activity_id']);
            }
            catch (ModelNotFoundException $e) {
                return false;
            }

            // Set time
            $reqStartTime = $request['start_time'];
            $reqEndTime = Booking::calcEndTime($activity->duration, $reqStartTime);

            // Get bookings of the date
            $bookings = Booking::where($attribute, $value)
                ->where('date', $request['date'])
                ->get();

            // Loop through booking results
            foreach ($bookings as $booking) {
                // Booking start and end time
                $bookStartTime = $booking->start_time;
                $bookEndTime = $booking->end_time;

                // If times are conflicting with any exiting booking
                // Then break and return false
                if ($reqStartTime >= $bookStartTime && $reqEndTime <= $bookEndTime ||
                    $reqStartTime < $bookStartTime && $reqEndTime > $bookStartTime ||
                    $reqStartTime < $bookEndTime && $reqEndTime > $bookEndTime) {
                    return false;
                }
            }

            return true;
        });

        // Create a validator to check if an employee is free when adding a booking
        Validator::extend('is_employee_working', function ($attribute, $value, $parameters, $validator) {
            // Get request data
            $request = $validator->getData();

            // If request data is not provided then return false
            if (!isset($request['date']) or !isset($request['start_time']) or !isset($request['activity_id'])) {
                return false;
            }

            // Find activity or return false
            try {
                $activity = Activity::findOrFail($request['activity_id']);
            }
            catch (ModelNotFoundException $e) {
                return false;
            }

            // Set time
            $pStartTime = toTime($request['start_time']);
            $pEndTime = Booking::calcEndTime($activity->duration, $pStartTime);

            // Get bookings of the date
            $workingTime = WorkingTime::where($attribute, $value)
                ->where('date', $request['date'])
                ->first();

            // If there doesnt exist a working time, then return false
            if ($workingTime == null) {
                return false;
            }

            // Working time alias
            $wStartTime = $workingTime->start_time;
            $wEndTime = $workingTime->end_time;


            // Check if booking is in between employee working time
            if ($pStartTime >= $wStartTime and $pEndTime <= $wEndTime) {
                return true;
            }

            // If anything unexpected happens, return false
            return false;
        });

        // Check if the calculated end time of a booking is valid
        Validator::extend('is_end_time_valid', function ($attribute, $value, $parameters, $validator) {
            // Get request data
            $request = $validator->getData();

            // If request data is not provided then return false
            if (!isset($request['start_time'])) {
                return false;
            }

            // Alias
            $startTime = $request['start_time'];

            // Find activity or return false
            try {
                $activity = Activity::findOrFail($value);
            }
            catch (ModelNotFoundException $e) {
                return false;
            }

            // If end time is before start time
            // Then return false
            if (Booking::calcEndTime($activity->duration, $startTime) > $startTime) {
                return true;
            }

            return false;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->environment('local', 'testing')) {
            $this->app->register(DuskServiceProvider::class);
        }
    }
}
