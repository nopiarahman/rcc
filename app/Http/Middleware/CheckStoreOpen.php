<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebSetting;

class CheckStoreOpen
{
    /**
     * Routes that should be accessible even when store is closed
     *
     * @var array
     */
    protected $except = [
        'login',
        'login.*',
        'logout',
        'password.*',
        'register',
        'verification.*',
        'maintenance',
        'admin.*',
        'dashboard.*',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip middleware for excepted routes
        if ($this->inExceptArray($request)) {
            return $next($request);
        }

        $settings = WebSetting::first();
        
        // If store is not closed, continue with the request
        if (!$settings || !$settings->is_temporarily_closed) {
            return $next($request);
        }

        // Allow access for admin users
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        // If we're already on the maintenance page, proceed to avoid redirect loop
        if ($request->is('maintenance')) {
            return $next($request);
        }

        // Redirect to maintenance page for all other cases
        return redirect()->route('maintenance');
    }

    /**
     * Determine if the request has a URI that should be accessible.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function inExceptArray($request)
    {
        foreach ($this->except as $except) {
            if ($except !== '/') {
                $except = trim($except, '/');
            }

            if ($request->fullUrlIs($except) || $request->is($except)) {
                return true;
            }
        }

        return false;
    }
}
