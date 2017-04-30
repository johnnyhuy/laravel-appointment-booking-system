<?php

namespace Tests\Browser;

use Tests\DuskTestCase;

use App\BusinessOwner;
use App\Booking;

use Carbon\Carbon;

class BookingHistoryTest extends DuskTestCase
{
    /**
     * Test whether the history page exists
     *
     * @return void
     */
    public function testSummaryPageExists()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')
                ->assertPathIs('/admin/history');
        });
    }

    /**
     * Test whether the history page gives no history of bookings found message
     *
     * @return void
     */
    public function testNoBookingsDisplayed()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();

        $this->browse(function ($browser) use ($bo) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')
                ->assertSee('No history of bookings found');
        });
    }

    /**
     * Test whether the correct information is displayed in the summary field
     *
     * @return void
     */
    public function testSingleEntryDisplayed()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Create a booking for yesterday
        $booking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->subDay(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')
                // Check for the booking ID
                ->assertSee($booking->id)
                // Check for full name on page
                ->assertSee($booking->customer->firstname . " " . $booking->customer->lastname)
                // Check for start time in the proper format
                ->assertSee(Carbon::parse($booking->start_time)->format('H:i'))
                // Check for the end time in the proper format
                ->assertSee(Carbon::parse($booking->end_time)->format('H:i'))
                // Check for the date in the proper format
                ->assertSee(Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether future entries are not stored in history
     *
     * @return void
     */
    public function testFutureEntriesNotDisplayed()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Create a booking for yesterday
        $booking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->addDay(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')
                // Check for full name on page
                ->assertDontSee($booking->customer->firstname . " " . $booking->customer->lastname)
                // Check for start time in the proper format
                ->assertDontSee(Carbon::parse($booking->start_time)->format('H:i'))
                // Check for the end time in the proper format
                ->assertDontSee(Carbon::parse($booking->end_time)->format('H:i'))
                // Check for the date in the proper format
                ->assertDontSee(Carbon::parse($booking->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether multiple entries are displayed at the same time
     *
     * @return void
     */
    public function testMultipleEntriesDisplayed()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Create a booking for yesterday
        $booking1 = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->subDay(),
        ]);
        // Create a booking for a month ago
        $booking2 = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->subMonth(),
        ]);

        $this->browse(function ($browser) use ($bo, $booking1, $booking2) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')

                // Check for first booking
                // Check for the booking ID
                ->assertSee($booking1->id)
                // Check for full name on page
                ->assertSee($booking1->customer->firstname . " " . $booking1->customer->lastname)
                // Check for start time in the proper format
                ->assertSee(Carbon::parse($booking1->start_time)->format('H:i'))
                // Check for the end time in the proper format
                ->assertSee(Carbon::parse($booking1->end_time)->format('H:i'))
                // Check for the date in the proper format
                ->assertSee(Carbon::parse($booking1->date)->format('d/m/y'))

                // Check for second booking
                // Check for the booking ID
                ->assertSee($booking2->id)
                // Check for full name on page
                ->assertSee($booking2->customer->firstname . " " . $booking2->customer->lastname)
                // Check for start time in the proper format
                ->assertSee(Carbon::parse($booking2->start_time)->format('H:i'))
                // Check for the end time in the proper format
                ->assertSee(Carbon::parse($booking2->end_time)->format('H:i'))
                // Check for the date in the proper format
                ->assertSee(Carbon::parse($booking2->date)->format('d/m/y'));
        });
    }

    /**
     * Test whether a booking for the current day isn't displayed
     *
     * @return void
     */
    public function testCurrentDayEntryNotDisplayed()
    {
        // Creates business owner
        $bo = factory(BusinessOwner::class)->create();
        // Create a booking for today
        $booking = factory(Booking::class)->create([
            'date' => Carbon::now('Australia/Melbourne')->toDateString()
        ]);

        $this->browse(function ($browser) use ($bo, $booking) {
            // Login as Business Owner
            $browser->loginAs($bo, 'web_admin')
                // Go to summary page (default directory of /admin)
                ->visit('/admin/history')
                // Check for full name on page
                ->assertDontSee($booking->customer->firstname . " " . $booking->customer->lastname)
                // Check for start time in the proper format
                ->assertDontSee(Carbon::parse($booking->start_time)->format('H:i'))
                // Check for the end time in the proper format
                ->assertDontSee(Carbon::parse($booking->end_time)->format('H:i'))
                // Check for the date in the proper format
                ->assertDontSee(Carbon::parse($booking->date)->format('d/m/y'));
        });
    }
}
