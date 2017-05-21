<?php

use Carbon\Carbon as Time;

/**
 * Date/Time helper functions
 */

/**
 * Parse time alias from Carbon
 *
 * @param  string $string
 * @return Carbon
 */
function parseDateTime($string)
{
    // Avoid thrown exception
    try {
        return Time::parse($string);
    }
    catch (Exception $e) {
        return $string;
    }
}

/**
 * Print the month and year given the date time string
 * e.g. 01-2017
 *
 * @param  string $stringDateTime
 * @return string
 */
function toMonthYear($stringDateTime)
{
    // Avoid thrown exception
    try {
        return Time::parse($stringDateTime)->format('m-Y');
    }
    catch (Exception $e) {
        return $stringDateTime;
    }
}


/**
 * Print the date time string
 *
 * @param  string $stringDateTime
 * @return string
 */
function toDateTime($stringDateTime)
{
    // Avoid thrown exception
    try {
        return Time::parse($stringDateTime)->toDateTimeString();
    }
    catch (Exception $e) {
        return $stringDateTime;
    }
}

/**
 * Print the time string
 *
 * @param  string $stringDateTime
 * @return string
 */
function toTime($stringDateTime, $hourMinute = null)
{
    // Avoid thrown exception
    try {
        $time = Time::parse($stringDateTime);
    }
    catch (Exception $e) {
        return $stringDateTime;
    }

    // If the last param is set
    if (isset($hourMinute)) {
        if ($hourMinute) {
            // Short 12 hour format
            return $time->format('h:i A');
        }
        else {
            // Long 24 hour format
            return $time->format('H:i');
        }
    }

    // Otherwise deafault to normal format
    return $time->toTimeString();
}

/**
 * Print the date string
 *
 * @param  string $stringDateTime
 * @return string
 */
function toDate($stringDateTime, $slash = null)
{
    // Avoid thrown exception
    try {
        $time = Time::parse($stringDateTime);
    }
    catch (Exception $e) {
        return $stringDateTime;
    }


    // If the last param is set
    if (isset($slash)) {
        if ($slash) {
            // Short 12 hour format
            return $time->format('d/m/Y');
        }
        else {
            // Long 24 hour format
            return $time->format('d-m-Y');
        }
    }

    return $time->toDateString();
}

/**
 * Get current time now in AEST
 *
 * @return string
 */
function getTimeNow()
{
    return Time::now('AEST')->toTimeString();
}

/**
 * Get current date now in AEST
 *
 * @return string
 */
function getDateNow()
{
    return Time::now('AEST')->toDateString();
}

/**
 * Get current date time now in AEST
 *
 * @return string
 */
function getDateTimeNow()
{
    return Time::now('AEST')->toDateTimeString();
}

/**
 * Get current date time alias
 *
 * @return string
 */
function getNow()
{
    return getDateTimeNow();
}

/**
 * Get month year now
 *
 * @return string
 */
function getMonthYearNow()
{
    return toMonthYear(getNow());
}

/**
 * Convert month year to carbon
 *
 * @param  string $string
 *
 * @return Carbon
 */
function monthYearToDate($string)
{
    // Get the month and year from url
    $date = explode('-', $string);
    $month = $date[0];
    $year = $date[1];

    // If input is invalid
    if (!is_numeric($month) or !is_numeric($year)) {
        return $string;
    }

    return Time::createFromDate($year, $month);
}

/**
 * Return a list of Carbon dates
 *
 * @param  string $string
 *
 * @return Carbon[]
 */
function getMonthList($string)
{
    // List of months
    // 6 months ahead and behind
    $monthList = [];

    // Get months previous
    for ($months = 6; $months > 0; $months--) {
        $monthList[] = monthYearToDate($string)->subMonths($months);
    }

    // Get months now and ahead
    for ($months = 0; $months < 6; $months++) {
        $monthList[] = monthYearToDate($string)->addMonths($months);
    }

    return $monthList;
}

/**
 * Return an array of all days within the week
 *
 * @return  array  $days
 */
function getDaysOfWeek($upper = false)
{
    // List of days in first uppercase
    $days = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday'
    ];

    // If day is uppercase
    if ($upper) {
        foreach ($days as $key => $day) {
            $days[$key] = strtoupper($day);
        }
    }

    return $days;
}

/**
 * Return a day according by number
 * starting from Monday
 *
 * @param  integer $number
 *
 * @return string
 */
function getDayOfWeek($number = 0, $upper = false)
{
    $days = getDaysOfWeek();

    // Check if number is in between 1 - 7
    // Otherwise default to 1
    $number = $number > 0 && $number < 8 && is_numeric($number) ? $number : 1;

    // If day is uppercase
    if ($upper) {
        foreach ($days as $key => $day) {
            $days[$key] = strtoupper($day);
        }
    }

    // Get the acutal index
    return $days[$number - 1];
}