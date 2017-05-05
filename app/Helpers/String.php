<?php

/**
 * Get first character of string
 * followed by a period
 *
 * @param  string $string
 * @return string
 */
function firstChar($string, $period)
{
    $string = substr($string, 0, 1);

    if ($period) {
        $string .= '.';
    }

    return $string;
}