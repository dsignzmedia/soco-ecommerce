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
        // Check if user is authenticated via Laravel's auth system
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Please login to access the School Dashboard.');
        }

        // Verify user has School role
        if (!auth()->user()->isSchool()) {
            // Log out the unauthorized user (unless we want to just redirect them to their own dashboard)
            // But if they are trying to access school routes and are not school, they shouldn't be here.

            // If they are a parent, maybe redirect to parent dashboard?
            // But for strict security similar to admin, usually we deny access.
            // For now, let's redirect to their appropriate dashboard if logged in, or logout.

            if (auth()->user()->isParent()) {
                return redirect()->route('frontend.parent.dashboard');
            }

            // For safety/default:
             return redirect()->route('login')
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
