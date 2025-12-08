<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckMasterAdmin Middleware
 *
 * Protects Master Admin routes with comprehensive security:
 * 1. Verifies user is authenticated
 * 2. Validates user has Master Admin role (role = 2)
 * 3. Adds cache control headers to prevent back-button access after logout
 * 4. Forces fresh page loads to prevent showing stale authenticated content
 */
class CheckMasterAdmin
{
    /**
     * Handle an incoming request.
     * Only allows users with role = 2 (Master Admin) to access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Laravel's auth system
        if (!auth()->check()) {
            // Clear any stale session data
            $request->session()->flush();

            return redirect()->route('master.admin.login')
                ->with('error', 'Please login to access the Master Admin panel.');
        }

        // Verify user has Master Admin role (role = 2)
        // Using strict comparison to prevent type coercion attacks
        if (auth()->user()->role !== 2) {
            // Log out the unauthorized user
            auth()->logout();

            // Completely invalidate and regenerate session for security
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('master.admin.login')
                ->with('error', 'You do not have permission to access the Master Admin panel.');
        }

        // Process the request
        $response = $next($request);

        // Add comprehensive cache control headers
        // These headers ensure:
        // - Browser doesn't cache the page (no-store, no-cache)
        // - Proxy servers don't cache (must-revalidate, private)
        // - Page expires immediately (max-age=0, Expires in past)
        // - Back button always fetches fresh content from server
        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
