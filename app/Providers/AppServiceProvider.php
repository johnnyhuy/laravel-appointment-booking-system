<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
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
		//Additional code to fix php artisan migrate error for (unique key too long on certain systems)
        Schema::defaultStringLength(191);

        // Create a validator to check if an employee is free when adding a booking
        Validator::extend('is_employee_on_booking', function ($attribute, $value, $parameters, $validator) {
            // Parameters are the date and time of booking
            $pDate = $parameters[0];
            $pStartTime = $parameters[1];
            $pEndTime = $parameters[2];

            // Get bookings of the date
            $bookings = Booking::where('employee_id', $value)
                ->where('date', '=', $pDate)
                ->get();
            
            // Is employee free
            $free = true;

            // Loop through booking results
            foreach ($bookings as $booking) {
                // Booking start and end time
                $bStartTime = $booking->start_time;
                $bEndTime = $booking->end_time;

                // If times are conflicting with any exiting booking
                // Then break and return false
                if (!($pStartTime < $bStartTime and $pEndTime <= $bStartTime or $pStartTime >= $bEndTime and $pEndTime > $bEndTime)) {
                    $free = false;
                    break;
                }
            }

            // Return condition
            return $free;
        });

        // Create a validator to check if an employee is free when adding a booking
        Validator::extend('is_employee_working', function ($attribute, $value, $parameters, $validator) {
            // Parameters are the date and time of booking
            $pDate = $parameters[0];
            $pStartTime = $parameters[1];
            $pEndTime = $parameters[2];

            // Get bookings of the date
            $workingTime = WorkingTime::where('employee_id', $value)
                ->where('date', $pDate)
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
            // Alias
            $activityID = $value;
            $startTime = $parameters[0];

            // If end time is before start time
            // Then return false
            if (Booking::calculateEndTime($activityID, $startTime) < $startTime) {
                return false;
            }
            else {
                return true;
            }
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
