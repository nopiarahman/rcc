<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebSetting;
use Carbon\Carbon;

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

        // Skip if we're already on the maintenance page
        if ($request->is('maintenance') || $request->routeIs('maintenance')) {
            return $next($request);
        }

        $settings = WebSetting::first();
        
        // Always allow access for admin users
        if (Auth::check() && Auth::user()->is_admin) {
            return $next($request);
        }

        $shouldRedirect = false;
        $redirectReason = '';

        // Check if store is temporarily closed
        if ($settings && $settings->is_temporarily_closed) {
            $shouldRedirect = true;
            $redirectReason = 'temporarily_closed';
        } 
        // Check store hours if they are set
        elseif ($settings && $settings->opening_time && $settings->closing_time) {
            if (!$this->isStoreOpen($settings)) {
                $shouldRedirect = true;
                $redirectReason = 'outside_business_hours';
            }
        }

        // Only redirect if not already on the maintenance page
        if ($shouldRedirect && !$request->is('maintenance')) {
            return redirect()->route('maintenance', ['reason' => $redirectReason]);
        }

        return $next($request);
    }

    /**
     * Determine if the request has a URI that should be accessible.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    /**
     * Check if store is open based on business hours
     *
     * @param  WebSetting  $settings
     * @return bool
     */
    protected function isStoreOpen($settings)
    {
        if (!$settings || !$settings->opening_time || !$settings->closing_time) {
            return true; // If no hours set, assume store is always open
        }

        $now = Carbon::now();
        $openingTime = Carbon::parse($settings->opening_time);
        $closingTime = Carbon::parse($settings->closing_time);

        // Handle overnight hours (e.g., 18:00 - 02:00)
        if ($closingTime->lessThan($openingTime)) {
            return $now->greaterThanOrEqualTo($openingTime) || $now->lessThan($closingTime);
        }

        return $now->between($openingTime, $closingTime);
    }

    /**
     * Redirect to maintenance page if not already there
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    protected function redirectToMaintenance($request, $next)
    {
        // If we're already on the maintenance page, return the response directly
        if ($request->is('maintenance') || $request->routeIs('maintenance')) {
            return $next($request);
        }

        // If this is an AJAX request, return a JSON response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Store is currently closed.'], 403);
        }

        // Only redirect if we're not already going to the maintenance page
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
