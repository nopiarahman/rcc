<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\WebSetting;

class CheckStoreOpen
{
    /**
     * Nama route yang tetap bisa diakses walau toko tutup.
     *
     * @var array<int, string>
     */
    protected $except = [
        'login',
        'login.*',
        'logout',
        'password.*',
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
        if ($request->routeIs($this->except)) {
            return $next($request);
        }

        // Admin (semua akun di tabel users) tetap bisa melihat tampilan toko saat tutup
        if (Auth::guard('web')->check()) {
            return $next($request);
        }

        $reason = WebSetting::current()->closedReason();

        if ($reason === null) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Toko sedang tutup.'], 403);
        }

        return redirect()->route('maintenance', ['reason' => $reason]);
    }
}
