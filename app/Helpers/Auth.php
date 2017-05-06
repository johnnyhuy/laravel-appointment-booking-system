<?php

use Illuminate\Support\Facades\Auth;

/**
 * Check if user is an admin (Business Owner)
 *
 * @param  string $string
 * @return Carbon
 */
function isAdmin()
{
    if (Auth::guard('web_admin')->check()) {
        return true;
    }
    else {
        return false;
    }
}

/**
 * Check if user is an user (Customer)
 *
 * @param  string $string
 * @return Carbon
 */
function isUser()
{
    if (Auth::guard('web_user')->check()) {
        return true;
    }
    else {
        return false;
    }
}