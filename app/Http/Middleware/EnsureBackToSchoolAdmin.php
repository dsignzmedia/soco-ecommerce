<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureBackToSchoolAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check specific guard
        if (auth()->guard('bts_admin')->check() && auth()->guard('bts_admin')->user()->role === \App\Models\User::ROLE_BACK_TO_SCHOOL_ADMIN) {
            // Also allow if user is accidentally retrieved via default guard but has permissions, but preferred strict.
            // For now, strict:
            $response = $next($request);

            // Add cache control headers to prevent back-button access after logout
            $response->headers->set('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', 'Sat, 01 Jan 2000 00:00:00 GMT');
            
            return $response;
        }

        abort(403, 'Unauthorized. Access restricted to Back-To-School Administrators.');
    }
}
