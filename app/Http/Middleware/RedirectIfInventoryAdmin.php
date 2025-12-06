<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * RedirectIfInventoryAdmin Middleware
 * 
 * Redirects already authenticated Inventory Admins away from guest pages (like login).
 */
class RedirectIfInventoryAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        // If user is authenticated AND has Inventory Admin role
        if (auth()->check() && auth()->user()->role === 3) {
            return redirect()->route('inventory.admin.dashboard')
                ->with('info', 'You are already logged in.');
        }

        return $next($request);
    }
}
