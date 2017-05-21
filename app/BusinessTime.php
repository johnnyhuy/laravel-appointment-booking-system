<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

use App\WorkingTime;

use Carbon\Carbon as Time;

class BusinessTime extends Model
{
    protected $guarded = [];

    // Disable timestamps
    public $timestamps = false;

    /**
     * Removes all future working times.
     *
     * @return void
     */
    public function deleteAllFutureWorkingTimes()
    {
        // Count the amount of working times removed
        $wTimeCount = 0;

        // Delete remaining working times after today on a day of week
        foreach (WorkingTime::where('date', '>=', getDateNow())->get() as $wTime) {
            if (strtoupper(Time::parse($wTime->date)->format('l')) == $this->day) {
                $wTime->delete();
                $wTimeCount++;
            }
        }

        Log::notice("Deleted " . $wTimeCount . " previous working time(s)");
    }

    /**
     * Removes all future bookings.
     *
     * @return void
     */
    public function deleteAllFutureBookings()
    {
        // Count the amount of bookings removed
        $bookingCount = 0;

        // Delete remaining booking after today on a day of week
        foreach (Booking::where('date', '>=', getDateNow())->get() as $booking) {
            if (strtoupper(Time::parse($booking->date)->format('l')) == $this->day) {
                $booking->delete();
                $bookingCount++;
            }
        }

        Log::notice("Deleted " . $bookingCount . " previous booking(s)");
    }
}
