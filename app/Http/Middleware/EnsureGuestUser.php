<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Role 4: Guest User
        // This middleware is mainly to ensure they don't access School/Parent/Admin specific areas
        // But if applied to a route group, it ensures only Guest Users can access it (if that's the intent)
        // OR, it can be used to inject global scopes if applied globally (but we are aliasing allowed)
        
        // For now, we just check if they are authenticated and are role 4
        if ($request->user() && $request->user()->role === 4) {
             return $next($request);
        }

        // If not role 4, we might want to allow others or deny. 
        // Based on the plan, this is "EnsureGuestUser", so strictly Role 4.
        // However, usually "Guest" implies public. 
        // The user said "Role 4 -> Guest User... Must login/register only at checkout".
        // So this is likely for "Logged in as Guest Customer".
        
         if ($request->user() && $request->user()->role === 4) {
            return $next($request);
        }

        // If not logged in, they are also guests in Laravel sense, but here we mean "Registered Guest Role".
        // We will abort 403 if they are logged in but NOT role 4.
        // If they are NOT logged in, auth middleware handles it before this.
        
        abort(403, 'Unauthorized. Access restricted to Guest Customers.');
    }
}
