<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\BusinessOwner;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        // If user is auth
        if (Auth::guard($guard)->check()) {
            switch ($guard) {
                case 'web_user':
                    // Go to customer bookings
                    $redirectURL = '/bookings';
                    break;
                case 'web_admin':
                    // Check if business owner exists
                    if (BusinessOwner::first()) {
                        // Go to dashboard
                        $redirectURL = '/admin';
                    }
                    else {
                        // Else go to business owner registration
                        $redirectURL = '/admin/register';
                    }

                    break;
                
                default:
                    // All else fails, redirect to login
                    $redirectURL = '/login';
                    break;
            }
            
            return redirect($redirectURL);
        }

        return $next($request);
    }
}
