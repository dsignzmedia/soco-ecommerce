<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureMerchandiseAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check specific guard
        if (auth()->guard('merch_admin')->check() && auth()->guard('merch_admin')->user()->role === \App\Models\User::ROLE_MERCHANDISE_ADMIN) {
            $response = $next($request);

            // Add cache control headers to prevent back-button access after logout
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            
            return $response;
        }

        abort(403, 'Unauthorized. Access restricted to Merchandise Administrators.');
    }
}
