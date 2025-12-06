<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfMasterAdmin Middleware
 * 
 * Redirects already authenticated Master Admins away from guest pages (like login).
 * This is the inverse of CheckMasterAdmin - it protects "guest-only" routes.
 * 
 * Use case: User is already logged in and tries to access /MasterAdmin/login
 * Expected behavior: Redirect them to dashboard instead of showing login page
 */
class RedirectIfMasterAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated AND has Master Admin role
        if (auth()->check() && auth()->user()->role === 2) {
            return redirect()->route('master.admin.dashboard')
                ->with('info', 'You are already logged in.');
        }

        return $next($request);
    }
}
