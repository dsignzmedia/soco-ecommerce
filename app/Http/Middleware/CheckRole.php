<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  int|string  ...$roles  Allowed role(s) as integers or constants
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();
        
        // Convert roles to integers
        $allowedRoles = array_map('intval', $roles);
        
        // Check if user's role is in the allowed roles
        if (!in_array($user->role, $allowedRoles)) {
            // Redirect based on user's actual role
            return match($user->role) {
                \App\Models\User::ROLE_PARENT => redirect()->route('frontend.parent.dashboard')
                    ->with('error', 'You do not have permission to access this page.'),
                \App\Models\User::ROLE_SCHOOL => redirect()->route('frontend.school.dashboard')
                    ->with('error', 'You do not have permission to access this page.'),
                \App\Models\User::ROLE_MASTER_ADMIN => redirect()->route('master.admin.dashboard')
                    ->with('error', 'You do not have permission to access this page.'),
                \App\Models\User::ROLE_INVENTORY_ADMIN => redirect()->route('master.admin.inventory.dashboard')
                    ->with('error', 'You do not have permission to access this page.'),
                default => redirect()->route('login')->with('error', 'Unauthorized access.'),
            };
        }

        return $next($request);
    }
}
