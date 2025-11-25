<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPasswordChange
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah user sudah login
        if (Auth::check()) {
            
            // Cek apakah user WAJIB ganti password
            if (Auth::user()->must_change_password) {
                
                // PENGECUALIAN (PENTING!):
                // Jika user sedang mengakses rute 'change-password' atau logout, BIARKAN LEWAT.
                // Jika tidak dikecualikan, akan terjadi error "Too many redirects" (Infinite Loop).
                if ($request->routeIs('password.change.*') || $request->routeIs('logout')) {
                    return $next($request);
                }

                // Jika user mencoba akses halaman lain (misal dashboard), TENDANG ke form ganti password
                return redirect()->route('password.change.form');
            }
        }

        return $next($request);
    }
}