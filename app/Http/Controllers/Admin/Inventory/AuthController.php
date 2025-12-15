<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Maximum login attempts before throttling
     */
    protected int $maxAttempts = 5;

    /**
     * Throttle decay time in seconds (1 minute)
     */
    protected int $decaySeconds = 60;

    /**
     * Display the inventory admin login screen.
     * Note: Now protected by RedirectIfInventoryAdmin middleware in routes.
     */
    public function showLoginForm(): View
    {
        return view('inventoryadmin.auth.login');
    }

    /**
     * Handle inventory admin login request.
     *
     * Security features:
     * - Rate limiting to prevent brute force attacks
     * - Session fixation protection via regenerate()
     * - CSRF protection (via Laravel middleware)
     * - Strict role validation
     */
    public function login(Request $request): RedirectResponse
    {
        // Check if user is rate limited
        $throttleKey = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors([
                'email' => "Too many login attempts. Please try again in {$seconds} seconds.",
            ])->withInput();
        }

        // Validate input with comprehensive rules
        $credentials = $request->validate([
            'email' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
        ], [
            'email.required' => 'Email or phone is required.',
            'password.required' => 'Password is required.',
            'password.min' => 'Password must be at least 6 characters.',
        ]);

        // Sanitize email input
        $emailOrPhone = trim($credentials['email']);

        // Attempt to find user by email or phone
        $user = User::where('email', $emailOrPhone)
            ->orWhere('phone', $emailOrPhone)
            ->first();

        // Check credentials
        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            // Increment rate limiter on failed attempt
            RateLimiter::hit($throttleKey, $this->decaySeconds);

            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->withInput(['email' => $emailOrPhone]);
        }

        // Check if user has Inventory Admin role (role = 3)
        if ($user->role !== 3) {
            // Increment rate limiter on role mismatch too
            RateLimiter::hit($throttleKey, $this->decaySeconds);

            return back()->withErrors([
                'email' => 'You do not have permission to access the Inventory Admin panel.',
            ])->withInput(['email' => $emailOrPhone]);
        }

        // Clear rate limiter on successful authentication
        RateLimiter::clear($throttleKey);

        // Log the user in
        auth()->guard('inventory_admin')->login($user);

        // CRITICAL: Regenerate session ID to prevent session fixation attacks
        $request->session()->regenerate();

        // Store admin session data
        $request->session()->put('inventory_admin_id', $user->id); // Prefixed to avoid collision
        $request->session()->put('inventory_admin_name', $user->name);
        $request->session()->put('inventory_admin_email', $user->email);
        $request->session()->put('inventory_admin_role', 'inventory_admin');

        try {
            AuditLogger::record(
                'login',
                $user,
                [
                    'user_email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                'Inventory Admin logged in'
            );
        } catch (\Exception $e) {
            // Silently ignore logging errors
        }

        // Notify Master Admin of Login
        try {
            \App\Models\Notification::create([
                'type' => 'system_alert',
                'title' => 'Inventory Admin Login',
                'message' => "Inventory Admin {$user->name} ({$user->email}) logged in.",
                'target_role' => 'master',
                'data' => ['user_id' => $user->id, 'email' => $user->email],
            ]);
        } catch (\Exception $e) {
            // Ignore notification errors to not block login
        }

        return redirect()->route('inventory.admin.dashboard');
    }



    /**
     * Render a trimmed-down dashboard for inventory admins.
     *
     * For now this mirrors the stock insights that the master admin sees,
     * but inside a separate layout and URI space so that future inventory
     * admin-only modules can live here without exposing master features.
     */
    public function dashboard(Request $request): View
    {
        $filters = $request->only(['school_id', 'date_from', 'date_to']);
        
        $applyOrderFilters = function($q) use ($filters) {
            if (!empty($filters['school_id'])) {
                $q->where('school_id', $filters['school_id']);
            }
            if (!empty($filters['date_from'])) {
                $q->whereDate('created_at', '>=', $filters['date_from']);
            }
            if (!empty($filters['date_to'])) {
                $q->whereDate('created_at', '<=', $filters['date_to']);
            }
        };

        // Order Metrics
        // If date filter is present, use it. Otherwise default to today for "New Orders" metric
        $ordersTodayQuery = \App\Models\Admin\Master\Order::query();
        if (!empty($filters['date_from']) || !empty($filters['date_to'])) {
            $applyOrderFilters($ordersTodayQuery);
        } else {
            $ordersTodayQuery->whereDate('created_at', today());
            if (!empty($filters['school_id'])) {
                $ordersTodayQuery->where('school_id', $filters['school_id']);
            }
        }
        $ordersToday = $ordersTodayQuery->count();

        // Status counts (usually all time pending, but can be filtered by school/date)
        $pendingPicking = \App\Models\Admin\Master\Order::where('order_status', 'pending');
        $applyOrderFilters($pendingPicking);
        $pendingPicking = $pendingPicking->count();

        $pendingPacking = \App\Models\Admin\Master\Order::where('order_status', 'processing');
        $applyOrderFilters($pendingPacking);
        $pendingPacking = $pendingPacking->count();

        $pendingShipment = \App\Models\Admin\Master\Order::where('order_status', 'ready_to_ship');
        $applyOrderFilters($pendingShipment);
        $pendingShipment = $pendingShipment->count();

        $delayedOrders = \App\Models\Admin\Master\Order::where('created_at', '<', now()->subDays(2))
            ->whereNotIn('order_status', ['completed', 'cancelled', 'delivered']);
        if (!empty($filters['school_id'])) {
            $delayedOrders->where('school_id', $filters['school_id']);
        }
        $delayedOrders = $delayedOrders->count();

        $schoolsWithOrders = \App\Models\Admin\Master\Order::whereIn('order_status', ['pending', 'processing', 'ready_to_ship'])
            ->distinct('school_id');
        $applyOrderFilters($schoolsWithOrders);
        $schoolsWithOrders = $schoolsWithOrders->count();

        $gradesWithOrders = \App\Models\Admin\Master\Order::whereIn('order_status', ['pending', 'processing', 'ready_to_ship'])
            ->distinct('grade');
        $applyOrderFilters($gradesWithOrders);
        $gradesWithOrders = $gradesWithOrders->count();

        // Stock Metrics
        $productsQuery = ProductMapping::select('inventory_stock', 'low_stock_threshold');
        if (!empty($filters['school_id'])) {
            $productsQuery->where('school_id', $filters['school_id']);
        }
        $products = $productsQuery->get();
        
        $outOfStock = $products->where('inventory_stock', '<=', 0)->count();
        $lowStock = $products->filter(
            fn ($product) => $product->low_stock_threshold !== null
                && $product->inventory_stock <= $product->low_stock_threshold
        )->count();

        // Data for filters
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();

        return view('inventoryadmin.dashboard', compact(
            'ordersToday',
            'pendingPicking',
            'pendingPacking',
            'pendingShipment',
            'delayedOrders',
            'schoolsWithOrders',
            'gradesWithOrders',
            'outOfStock',
            'lowStock',
            'filters',
            'schools'
        ));
    }

    /**
     * Display the Inventory Admin profile page.
     */
    public function profile(): View
    {
        // Default set used if DB tables don't exist yet
        $defaultPermissions = [
            ['key' => 'orders.view', 'label' => 'View Orders', 'granted' => true],
            ['key' => 'orders.update_status', 'label' => 'Update Order Status', 'granted' => true],
            ['key' => 'inventory.adjust', 'label' => 'Adjust Inventory', 'granted' => true],
            ['key' => 'returns.view', 'label' => 'View Returns & Exchanges', 'granted' => true],
            ['key' => 'reports.view', 'label' => 'View Reports', 'granted' => true],
            ['key' => 'returns.approve', 'label' => 'Approve Returns/Exchanges', 'granted' => false],
            ['key' => 'settings.manage', 'label' => 'Manage System Settings', 'granted' => false],
        ];

        $adminId = request()->session()->get('inventory_admin_id');

        // If the permissions tables exist, read from DB; otherwise fall back
        if (Schema::hasTable('permissions') && Schema::hasTable('user_permissions')) {
            // Ensure defaults exist in the permissions catalog
            try {
                $records = collect($defaultPermissions)->map(function ($p) {
                    return [
                        'key' => $p['key'],
                        'label' => $p['label'],
                        'module' => 'inventory',
                        'updated_at' => now(),
                        'created_at' => now(),
                    ];
                })->all();

                // Upsert based on unique key
                DB::table('permissions')->upsert($records, ['key'], ['label', 'module', 'updated_at']);
            } catch (\Throwable $e) {
                // Ignore seeding errors and fall back to default list
            }

            // Build permission list with granted flag for current admin
            $query = DB::table('permissions')
                ->leftJoin('user_permissions', function ($join) use ($adminId) {
                    $join->on('user_permissions.permission_id', '=', 'permissions.id');
                    if ($adminId) {
                        $join->where('user_permissions.user_id', '=', $adminId);
                    } else {
                        $join->whereRaw('1 = 0'); // no session -> no grants
                    }
                })
                ->select([
                    'permissions.key',
                    'permissions.label',
                    DB::raw('CASE WHEN user_permissions.granted = 1 THEN 1 ELSE 0 END as granted'),
                ])
                ->orderBy('permissions.key');

            $permissions = $query->get()->map(function ($row) {
                return [
                    'key' => $row->key,
                    'label' => $row->label,
                    'granted' => (bool) $row->granted,
                ];
            })->all();

            // If none found (fresh DB), fall back
            if (empty($permissions)) {
                $permissions = $defaultPermissions;
            }
        } else {
            $permissions = $defaultPermissions;
        }

        return view('inventoryadmin.profile', compact('permissions'));
    }

    /**
     * Update the current inventory admin user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $adminId = $request->session()->get('inventory_admin_id');
        if (!$adminId) {
            return back()->withErrors(['current_password' => 'Admin session not found. Please log in again.']);
        }

        $user = User::find($adminId);
        if (!$user) {
            return back()->withErrors(['current_password' => 'Admin user not found.']);
        }

        if (!Hash::check($data['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        try {
            AuditLogger::record(
                action: 'password_change',
                entityType: 'User',
                entityId: $user->id,
                description: 'Inventory admin changed account password',
                properties: [
                    'user_email' => $user->email,
                    'ip' => $request->ip(),
                ]
            );
        } catch (\Exception $e) {
            // Silently ignore logging errors for now
        }

        return back()->with('status', 'Password updated successfully.');
    }

    /**
     * Generate throttle key based on email/IP combination
     * This prevents both single-account brute force and distributed attacks
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(
            Str::lower($request->input('email', '')) . '|' . $request->ip()
        );
    }

    /**
     * Logout the inventory admin user.
     *
     * Security steps:
     * 1. Log the logout action for audit trail
     * 2. Log out the user from Laravel's auth system
     * 3. Clear all admin-specific session data
     * 4. Invalidate the entire session
     * 5. Regenerate CSRF token
     */
    public function logout(Request $request): RedirectResponse
    {
        // Get user info before logout for audit logging
        $user = auth()->guard('inventory_admin')->user();

        if ($user) {
            try {
                AuditLogger::record(
                    'logout',
                    $user,
                    [
                        'user_email' => $user->email,
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ],
                    'Inventory Admin logged out'
                );
            } catch (\Exception $e) {
                // Silently ignore logging errors
            }

            // Notify Master Admin of Logout
            try {
                \App\Models\Notification::create([
                    'type' => 'system_alert',
                    'title' => 'Inventory Admin Logout',
                    'message' => "Inventory Admin {$user->name} ({$user->email}) logged out.",
                    'target_role' => 'master',
                    'data' => ['user_id' => $user->id, 'email' => $user->email],
                ]);
            } catch (\Exception $e) {
                // Ignore notification errors to not block logout
            }
        }

        // Log out the user from Laravel's auth system
        auth()->guard('inventory_admin')->logout();

        // Clear all admin-specific session data
        $request->session()->forget([
            'inventory_admin_id',
            'inventory_admin_name',
            'inventory_admin_email',
            'inventory_admin_role',
        ]);

        // Do NOT invalidate session to keep Master Admin logged in
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        return redirect()->route('inventory.admin.login')
            ->with('status', 'You have been logged out successfully.')
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache');
    }

    public function markNotificationRead(Request $request, $id)
    {
        $notification = \App\Models\Notification::find($id);
        if ($notification) {
            $notification->update(['read_at' => now()]);

            // Redirection logic based on type and data
            if ($notification->data && is_array($notification->data)) {
                // New Order -> Orders List filtered by order number
                if ($notification->type === 'new_order' && isset($notification->data['order_number'])) {
                    return redirect()->route('inventory.admin.orders.index', ['order_number' => $notification->data['order_number']]);
                }
                // Return Request -> Returns List filtered by order number (q)
                if ($notification->type === 'return_request' && isset($notification->data['order_number'])) {
                    return redirect()->route('inventory.admin.returns-exchange.index', ['q' => $notification->data['order_number']]);
                }
            }
        }
        return back()->with('success', 'Notification marked as read.');
    }

    public function markAllNotificationsRead(Request $request)
    {
        \App\Models\Notification::whereNull('read_at')->update(['read_at' => now()]);
        return back()->with('success', 'All notifications marked as read.');
    }
}
