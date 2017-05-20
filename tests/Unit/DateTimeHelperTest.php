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
        $this->assertEquals('01-01-2017', toDate('2017-01-01', false));
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

    /**
     * Return an array of all days within a week
     *
     * @return void
     */
    public function testHelperGetDaysOfWeekArray() {
        $this->assertEquals('Monday', getDaysOfWeek()[0]);
        $this->assertEquals('Tuesday', getDaysOfWeek()[1]);
        $this->assertEquals('Wednesday', getDaysOfWeek()[2]);
        $this->assertEquals('Thursday', getDaysOfWeek()[3]);
        $this->assertEquals('Friday', getDaysOfWeek()[4]);
        $this->assertEquals('Saturday', getDaysOfWeek()[5]);
        $this->assertEquals('Sunday', getDaysOfWeek()[6]);
    }

    /**
     * Return an array of all days within a week
     * all in uppercase
     *
     * @return void
     */
    public function testHelperGetDaysOfWeekIsUppercase() {
        $this->assertEquals('MONDAY', getDaysOfWeek(true)[0]);
        $this->assertEquals('TUESDAY', getDaysOfWeek(true)[1]);
        $this->assertEquals('WEDNESDAY', getDaysOfWeek(true)[2]);
        $this->assertEquals('THURSDAY', getDaysOfWeek(true)[3]);
        $this->assertEquals('FRIDAY', getDaysOfWeek(true)[4]);
        $this->assertEquals('SATURDAY', getDaysOfWeek(true)[5]);
        $this->assertEquals('SUNDAY', getDaysOfWeek(true)[6]);
    }

    /**
     * All error parameters get the first day of week
     * Includes no parameter
     *
     * @return void
     */
    public function testHelperGetFirstDayOfWeekIfError() {
        $this->assertEquals('Monday', getDayOfWeek());
        $this->assertEquals('Monday', getDayOfWeek(null));
        $this->assertEquals('Monday', getDayOfWeek('first'));
        $this->assertEquals('Monday', getDayOfWeek('@#!@%$'));
        $this->assertEquals('Monday', getDayOfWeek(111));
    }

    /**
     * Make day of week uppercase
     *
     * @return void
     */
    public function testHelperGetFirstDayOfWeekIsUppercase() {
        $this->assertEquals('MONDAY', getDayOfWeek(1, true));
        $this->assertEquals('MONDAY', getDayOfWeek(null, true));
        $this->assertEquals('MONDAY', getDayOfWeek(-1, true));
        $this->assertEquals('MONDAY', getDayOfWeek(8, true));
    }

    /**
     * Get day 4 of week, which is Friday
     *
     * @return void
     */
    public function testHelperGetDayFourOfWeek() {
        $this->assertEquals('Thursday', getDayOfWeek(4));
        $this->assertNotEquals('Thursday', getDayOfWeek('four'));
    }
}