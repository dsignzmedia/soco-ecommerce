<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

/**
 * CheckSchool Middleware
 * 
 * Protects School Dashboard routes with comprehensive security:
 * 1. Verifies user is authenticated
 * 2. Validates user has School role
 * 3. Adds cache control headers to prevent back-button access after logout
 * 4. Forces fresh page loads to prevent showing stale authenticated content
 */
class CheckSchool
{
    /**
     * Handle an incoming request.
     * Only allows users with role = ROLE_SCHOOL to access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via School guard
        if (!auth('school')->check()) {
            return redirect()->route('frontend.school.login')
                ->with('error', 'Please login to access the School Dashboard.');
        }

        // Verify user has School role
        if (!auth('school')->user()->isSchool()) {
            // Log out from school guard
            auth('school')->logout();
            
            // If they are a parent, redirect to parent dashboard
            if (auth('web')->check() && auth('web')->user()->isParent()) {
                return redirect()->route('frontend.parent.dashboard');
            }
            
            return redirect()->route('frontend.school.login')
                ->with('error', 'You do not have permission to access the School Dashboard.');
        }

        // Process the request
        $response = $next($request);

        // Add comprehensive cache control headers
        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
