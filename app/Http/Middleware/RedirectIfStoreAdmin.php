<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class RedirectIfStoreAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check specific Store Admin guards first
        if (Auth::guard('bts_admin')->check() && Auth::guard('bts_admin')->user()->role === User::ROLE_BACK_TO_SCHOOL_ADMIN) {
             return redirect()->route('admin.back_to_school.dashboard');
        }

        if (Auth::guard('merch_admin')->check() && Auth::guard('merch_admin')->user()->role === User::ROLE_MERCHANDISE_ADMIN) {
             return redirect()->route('admin.merchandise.dashboard');
        }

        // Keep web guard check just in case, but usually irrelevant for store admins now
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->role === User::ROLE_BACK_TO_SCHOOL_ADMIN) {
                // If logged in via web (legacy), logout and redirect to login to force correct guard
                Auth::logout();
                return redirect()->route('store.admin.login');
            } elseif ($user->role === User::ROLE_MERCHANDISE_ADMIN) {
                 Auth::logout();
                 return redirect()->route('store.admin.login');
            }
            return redirect()->route('frontend.parent.dashboard');
        }

        return $next($request);
    }
}
