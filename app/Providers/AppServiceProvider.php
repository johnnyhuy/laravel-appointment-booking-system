<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Laravel\Dusk\DuskServiceProvider;

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

        Validator::extend('is_employee_not_working', function ($attribute, $value, $parameters, $validator) {
            $date = $parameters[0];
            $startTime = $parameters[1];
            $endTime = $parameters[2];
            $bookings = App\Booking::where('employee_id', $value)
                ->where('date', '=', $date);

            foreach ($bookings as $booking) {
                // When a booking is between an employee working
                if ($booking->start_time < $startTime and $booking->end_time > $endTime) {
                    return false;
                }

                if ($booking->start_time < $startTime and $booking->end_time < $endTime) {
                    return false;
                }
            }

            return $count === 0;
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
