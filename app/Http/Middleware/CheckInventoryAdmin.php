<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * CheckInventoryAdmin Middleware
 * 
 * Protects Inventory Admin routes with comprehensive security:
 * 1. Verifies user is authenticated
 * 2. Validates user has Inventory Admin role (role = 3)
 * 3. Adds cache control headers to prevent back-button access after logout
 */
class CheckInventoryAdmin
{
    /**
     * Handle an incoming request.
     * Only allows users with role = 3 (Inventory Admin) to access.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated via Inventory Admin guard
        if (!auth('inventory_admin')->check()) {
            return redirect()->route('inventory.admin.login')
                ->with('error', 'Please login to access the Inventory Admin panel.');
        }

        // Verify user has Inventory Admin role (role = 3)
        if (auth('inventory_admin')->user()->role !== 3) {
            // Log out the unauthorized user from this guard
            auth('inventory_admin')->logout();
            
            return redirect()->route('inventory.admin.login')
                ->with('error', 'You do not have permission to access the Inventory Admin panel.');
        }

        // Process the request
        $response = $next($request);

        // Add cache control headers to prevent back-button access
        return $response
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
    }
}
