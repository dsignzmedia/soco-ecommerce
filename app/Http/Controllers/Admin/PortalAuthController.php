<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PortalAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('admin.auth.portal_login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Attempt authentication using the default 'web' user provider manually first to check credentials and role
        // We cannot use Auth::attempt() directly on a guard because we don't know the role yet.
        // So we lookup the user independently.
        $user = User::where('email', $request->email)->first();

        if ($user && \Illuminate\Support\Facades\Hash::check($request->password, $user->password)) {
            
            // Redirect based on role and login to specific guard
            if ($user->role === User::ROLE_BACK_TO_SCHOOL_ADMIN) {
                Auth::guard('bts_admin')->login($user, $request->filled('remember'));
                $request->session()->regenerate();
                return redirect()->intended(route('admin.back_to_school.dashboard'));
            } 
            elseif ($user->role === User::ROLE_MERCHANDISE_ADMIN) {
                Auth::guard('merch_admin')->login($user, $request->filled('remember'));
                $request->session()->regenerate();
                return redirect()->intended(route('admin.merchandise.dashboard'));
            }
            
            // Invalid role for this portal
            return back()->withErrors([
                'email' => 'You do not have administrative access.',
            ]);
        }

        throw ValidationException::withMessages([
            'email' => __('auth.failed'),
        ]);
    }

    public function logout(Request $request)
    {
        $guard = $request->input('guard');

        // Targeted Logout
        if ($guard === 'bts_admin' && Auth::guard('bts_admin')->check()) {
            Auth::guard('bts_admin')->logout();
            if(!Auth::guard('merch_admin')->check()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect()->route('store.admin.login');
        }

        if ($guard === 'merch_admin' && Auth::guard('merch_admin')->check()) {
            Auth::guard('merch_admin')->logout();
            if(!Auth::guard('bts_admin')->check()) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect()->route('store.admin.login');
        }

        // Fallback: If no guard specified, try to interpret or logout all store admins
        if (Auth::guard('bts_admin')->check()) {
             Auth::guard('bts_admin')->logout();
        }
        if (Auth::guard('merch_admin')->check()) {
             Auth::guard('merch_admin')->logout();
        }
        
        // Final invalidation
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('store.admin.login');
    }
}
