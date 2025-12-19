<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureParent
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->isParent() || $user->role === \App\Models\User::ROLE_GUEST) {
            return $next($request);
        }

        // Redirect based on other roles
        if ($user->isBackToSchoolAdmin()) {
            return redirect()->route('admin.back_to_school.dashboard');
        }

        if ($user->isMerchandiseAdmin()) {
             return redirect()->route('admin.merchandise.dashboard');
        }

        if ($user->isMasterAdmin()) {
            return redirect()->route('master.admin.dashboard');
        }

        if ($user->isInventoryAdmin()) {
            return redirect()->route('inventory.admin.dashboard');
        }

        if ($user->isSchool()) {
            return redirect()->route('frontend.school.dashboard');
        }

        // Default fallback or error
        return redirect()->route('login')->with('error', 'Unauthorized access.');
    }
}
