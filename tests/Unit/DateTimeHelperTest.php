<?php

namespace Tests\Unit;

use Tests\TestCase;

use Carbon\Carbon;

/**
 * Test all date time related functions
 * Standalone helper functions are included
 */
class DateTimeHelperTest extends TestCase
{
    /**
     * Ensure the helper time function is
     * printing the time string
     *
     * @return void
     */
    public function testHelperGetTimeString() {
        // Test the input time strings below
        $this->assertEquals('10:00:00', toTime('10:00:00'));
        $this->assertEquals('10:00:00', toTime('10:00'));

        // Not in AEST
        $this->assertEquals(Carbon::now()->toTimeString(), toTime('now'));

        // Shorthand time
        $this->assertEquals('06:00 PM', toTime('18:00', true));

        // Long 24 hour time
        $this->assertEquals('18:00', toTime('18:00', false));
    }

    /**
     * Ensure the helper time function is
     * printing the date string
     *
     * @return void
     */
    public function testHelperGetDateString() {
        // Test the input time strings below
        $this->assertEquals('2017-01-01', toDate('2017-01'));
        $this->assertEquals('2017-01-01', toDate('17-01-01'));
        $this->assertEquals('2017-01-01', toDate('17-01-01'));
        $this->assertEquals('2017-01-01', toDate('01/01/2017'));
        $this->assertEquals('2017-01-01', toDate('01/01/17'));

        // Not in AEST
        $this->assertEquals(Carbon::now()->toDateString(), toDate('2017'));
        $this->assertEquals(Carbon::now()->toDateString(), toDate('now'));

        // Use brackets
        $this->assertEquals('01/01/2017', toDate('2017-01-01', true));

        // Use dashes
        $this->assertEquals('01/01/2017', toDate('2017-01-01', false));
    }

    /**
     * Ensure the helper time function is
     * printing the date time string
     *
     * @return void
     */
    public function testHelperGetDateTimeString() {
        // Test the input time strings below
        $this->assertEquals('2017-01-01 00:00:00', toDateTime('2017-01'));
        $this->assertEquals('2017-01-01 00:00:00', toDateTime('17-01-01'));
        $this->assertEquals('2017-01-01 00:00:00', toDateTime('17-01-01'));
        $this->assertEquals('2017-01-01 00:00:00', toDateTime('01/01/2017'));
        $this->assertEquals('2017-01-01 00:00:00', toDateTime('01/01/17'));
        $this->assertEquals('2017-01-01 10:00:00', toDateTime('2017-01-01 10:00'));

        // Does not include AEST
        $this->assertEquals(Carbon::now()->toDateTimeString(), toDateTime('now'));
    }

    /**
     * Ensure the helper time function is
     * printing the month year string with
     * a specfic format
     *
     * @return void
     */
    public function testHelperGetMonthYear() {
        // Test the input time strings below
        $this->assertEquals('01-2017', toMonthYear('17-01-01'));
        $this->assertEquals('01-2017', toMonthYear('17-01-01'));
        $this->assertEquals('01-2017', toMonthYear('01/01/2017'));
        $this->assertEquals('01-2017', toMonthYear('01/01/17'));

        // Does not include AEST
        $this->assertEquals(Carbon::now()->format('m-Y'), toMonthYear('now'));
    }

    /**
     * Ensure the helper time function is
     * printing the hour minute string
     *
     * @return void
     */
    public function testHelperGetHourMinuteString() {
        // Get short 12 hour time
        $shortTime = toTime('now', true);
        $this->assertEquals(Carbon::now()->format('h:i A'), $shortTime);

        // Get long 24 hour time
        $longTime = toTime('now', false);
        $this->assertEquals(Carbon::now()->format('H:i'), $longTime);

        // Get 10:00:00 in hour minute string
        // Both time string and helper function time should be the same
        $this->assertEquals('10:00', toTime('10:00:00', false));
    }

    /**
     * Test the current time is in AEST
     *
     * @return void
     */
    public function testHelperGetTimeNow() {
        $this->assertEquals(Carbon::now('AEST')->toTimeString(), getTimeNow());
    }

    /**
     * Test the current date is in AEST
     *
     * @return void
     */
    public function testHelperGetDateNow() {
        $this->assertEquals(Carbon::now('AEST')->toDateString(), getDateNow());
    }

    /**
     * Test the current date time is in AEST
     *
     * @return void
     */
    public function testHelperGetDateTimeNow() {
        $this->assertEquals(Carbon::now('AEST')->toDateTimeString(), getDateTimeNow());
    }
}