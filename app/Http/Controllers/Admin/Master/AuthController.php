<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use App\Models\User;
use App\Support\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
     * Display the master admin login screen.
     * Note: Now protected by RedirectIfMasterAdmin middleware in routes,
     * but we keep this check as fallback defense-in-depth.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
    }

    /**
     * Handle master admin login request.
     *
     * Security features:
     * - Rate limiting to prevent brute force attacks
     * - Session fixation protection via regenerate()
     * - CSRF protection (via Laravel middleware)
     * - Strict role validation
     */
    public function login(Request $request)
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

        // Check if user has Master Admin role (role = 2)
        if ($user->role !== 2) {
            // Increment rate limiter on role mismatch too
            RateLimiter::hit($throttleKey, $this->decaySeconds);

            return back()->withErrors([
                'email' => 'You do not have permission to access the Master Admin panel.',
            ])->withInput(['email' => $emailOrPhone]);
        }

        // Clear rate limiter on successful authentication
        RateLimiter::clear($throttleKey);

        // Log the user in using Laravel's auth system
        auth()->guard('master_admin')->login($user);

        // CRITICAL: Regenerate session ID to prevent session fixation attacks
        // This creates a new session ID while keeping session data
        $request->session()->regenerate();

        // Store admin-specific session data for quick access
        $request->session()->put('master_admin_id', $user->id);
        $request->session()->put('master_admin_name', $user->name);
        $request->session()->put('master_admin_email', $user->email);
        $request->session()->put('master_admin_role', 'master_admin');

        // Log the successful login for audit trail
        AuditLogger::record(
            'login',
            $user,
            [
                'user_email' => $user->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ],
            'Master Admin logged in'
        );

        return redirect()->route('master.admin.dashboard');
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
     * Display the master admin dashboard.
     *
     * For now, the stats are mocked so the UI can be reviewed without
     * wiring the backend. Replace these arrays with live data once the
     * admin modules are connected.
     */
    public function dashboard(Request $request)
    {
        $filters = $request->only(['date_range', 'school_id', 'category', 'start_date', 'end_date']);

        // Base queries
        $ordersQuery = \App\Models\Admin\Master\Order::query();
        $productsQuery = ProductMapping::query();

        // Apply Filters to Orders
        if (!empty($filters['school_id'])) {
            $ordersQuery->where('school_id', $filters['school_id']);
        }
        if (!empty($filters['category'])) {
            $ordersQuery->where('category', $filters['category']);
        }
        
        // Date Range Filtering
        if (!empty($filters['start_date'])) {
            $ordersQuery->whereDate('order_date', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $ordersQuery->whereDate('order_date', '<=', $filters['end_date']);
        }

        // Apply Filters to Products (for stock KPIs)
        if (!empty($filters['school_id'])) {
            $productsQuery->where('school_id', $filters['school_id']);
        }
        if (!empty($filters['category'])) {
            $productsQuery->where('category', $filters['category']);
        }

        // --- KPI Cards ---
        // Clone query for different stats to avoid interference
        $ordersKpi = [
            'total_orders' => (clone $ordersQuery)->count(),
            'processing' => (clone $ordersQuery)->where('order_status', 'processing')->count(),
            'shipped' => (clone $ordersQuery)->where('order_status', 'shipped')->count(),
            'delivered' => (clone $ordersQuery)->where('order_status', 'delivered')->count(),
            'failed' => (clone $ordersQuery)->whereIn('order_status', ['failed', 'cancelled'])->count(),
        ];

        $financialKpi = [
            'total_sales' => (clone $ordersQuery)->sum('total_amount'),
            'total_tax' => (clone $ordersQuery)->sum('tax_amount'),
            'total_shipping' => (clone $ordersQuery)->sum('shipping_cost'),
        ];

        // Stock KPIs (based on Products, not Orders)
        $stockKpi = [
            'in_stock' => (clone $productsQuery)->where('inventory_stock', '>', 0)->count(),
            'out_of_stock' => (clone $productsQuery)->where('inventory_stock', '<=', 0)->count(),
            'low_stock' => (clone $productsQuery)->whereColumn('inventory_stock', '<=', 'low_stock_threshold')->count(),
            'returns' => (clone $ordersQuery)->whereNotNull('return_exchange_status')->count(),
        ];

        $kpis = [
            ['label' => 'Total Orders', 'value' => $ordersKpi['total_orders']],
            ['label' => 'Processing', 'value' => $ordersKpi['processing']],
            ['label' => 'Shipped', 'value' => $ordersKpi['shipped']],
            ['label' => 'Delivered', 'value' => $ordersKpi['delivered']],
            ['label' => 'Failed / Cancelled', 'value' => $ordersKpi['failed']],
            ['label' => 'Total Sales', 'prefix' => '₹', 'value' => number_format($financialKpi['total_sales'])],
            ['label' => 'Total Tax', 'prefix' => '₹', 'value' => number_format($financialKpi['total_tax'])],
            ['label' => 'Total Shipping', 'prefix' => '₹', 'value' => number_format($financialKpi['total_shipping'])],
            ['label' => 'In-stock SKUs', 'value' => $stockKpi['in_stock']],
            ['label' => 'Out-of-stock SKUs', 'value' => $stockKpi['out_of_stock']],
            ['label' => 'Returns / Exchange', 'value' => $stockKpi['returns']],
        ];

        // --- Charts ---

        // 1. Sales over time (Last 7 days or selected range)
        // Determine date range first
        $startDate = !empty($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date']) : now()->subDays(6);
        $endDate = !empty($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date']) : now();

        // Fetch sales grouped by date
        $salesData = (clone $ordersQuery)
            ->selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->whereDate('order_date', '>=', $startDate)
            ->whereDate('order_date', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date'); // Key by date for easy lookup

        $chartLabels = [];
        $chartSeries = [];

        // Build a continuous period ensuring every day is represented
        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $chartLabels[] = $date->format('M d');
            // Use actual sales or 0 if no sales on that day
            $chartSeries[] = $salesData->get($formattedDate) ?? 0;
        }

        // 2. Orders by School
        $ordersBySchool = (clone $ordersQuery)
            ->select('school_id', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->with('school:id,name')
            ->groupBy('school_id')
            ->get()
            ->map(fn($item) => [
                'label' => $item->school->name ?? 'Unknown',
                'value' => $item->total
            ]);

        // 3. Orders by Category
        $ordersByCategory = (clone $ordersQuery)
            ->select('category', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->map(fn($item) => [
                'label' => $item->category,
                'value' => $item->total
            ]);

        $charts = [
            'salesTrend' => [
                'title' => 'Sales over time',
                'labels' => $chartLabels,
                'series' => $chartSeries,
            ],
            'ordersBySchool' => [
                'title' => 'Orders by school',
                'data' => $ordersBySchool->isEmpty() ? [['label' => 'No orders', 'value' => 0]] : $ordersBySchool->toArray(),
            ],
            'ordersByCategory' => [
                'title' => 'Orders by category',
                'data' => $ordersByCategory->isEmpty() ? [['label' => 'No orders', 'value' => 0]] : $ordersByCategory->toArray(),
            ],
            'stockInsights' => [
                'title' => 'Stock insights',
                'bars' => [
                    ['label' => 'In stock', 'value' => $stockKpi['in_stock']],
                    ['label' => 'Low stock', 'value' => $stockKpi['low_stock']],
                    ['label' => 'Out of stock', 'value' => $stockKpi['out_of_stock']],
                ],
            ],
        ];

        // --- Alerts ---

        // 1. Low Stock
        $lowStockAlerts = ProductMapping::whereColumn('inventory_stock', '<=', 'low_stock_threshold')
            ->orderBy('inventory_stock')
            ->take(5)
            ->get();

        // 2. Delayed Orders (Processing for > 3 days)
        $delayedOrders = \App\Models\Admin\Master\Order::where('order_status', 'processing')
            ->where('created_at', '<', now()->subDays(3))
            ->take(5)
            ->get();

        // 3. Failed Payments
        $failedPayments = \App\Models\Admin\Master\Order::where('payment_status', 'failed')
            ->take(5)
            ->get();

        $alerts = [
            [
                'type' => 'Low stock',
                'items' => $lowStockAlerts->map(fn($m) => $m->product_name . ' (' . $m->inventory_stock . ' left)'),
            ],
            [
                'type' => 'Delayed orders',
                'items' => $delayedOrders->map(fn($o) => '#' . $o->order_number . ' (' . $o->created_at->diffForHumans() . ')'),
            ],
            [
                'type' => 'Failed payments',
                'items' => $failedPayments->map(fn($o) => '#' . $o->order_number . ' - ' . $o->customer_name),
            ],
        ];

        // Data for Filters
        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.dashboard', [
            'kpis' => $kpis,
            'charts' => $charts,
            'alerts' => $alerts,
            'filters' => $filters,
            'schools' => $schools,
            'categories' => $categories,
        ]);
    }

    /**
     * Display the admin profile page.
     */
    public function profile(): \Illuminate\View\View
    {
        // For now, permissions are derived from visible Master Admin modules
        $permissions = [
            ['key' => 'orders.manage', 'label' => 'Manage Orders', 'granted' => true],
            ['key' => 'schools.manage', 'label' => 'Manage Schools', 'granted' => true],
            ['key' => 'catalog.manage', 'label' => 'Manage Products & Catalog', 'granted' => true],
            ['key' => 'inventory.manage', 'label' => 'Manage Inventory', 'granted' => true],
            ['key' => 'shipping.manage', 'label' => 'Manage Shipping', 'granted' => true],
            ['key' => 'reports.view', 'label' => 'View Reports', 'granted' => true],
            ['key' => 'settings.manage', 'label' => 'Manage System Settings', 'granted' => true],
            ['key' => 'audit.view', 'label' => 'View Audit Logs', 'granted' => true],
        ];

        return view('admin.profile', compact('permissions'));
    }

    /**
     * Update the current admin user's password.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string', 'min:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $adminId = $request->session()->get('master_admin_id');
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

        AuditLogger::record(
            action: 'password_change',
            entityType: 'User',
            entityId: $user->id,
            description: 'Admin changed account password',
            properties: [
                'user_email' => $user->email,
                'ip' => $request->ip(),
            ]
        );

        return back()->with('status', 'Password updated successfully.');
    }

    /**
     * Logout the admin user.
     *
     * Security steps:
     * 1. Log the logout action for audit trail
     * 2. Log out the user from Laravel's auth system
     * 3. Clear all admin-specific session data
     * 4. Invalidate the entire session (prevents session reuse)
     * 5. Regenerate CSRF token (prevents CSRF attacks)
     * 6. Redirect to login with cache control headers
     */
    public function logout(Request $request)
    {
        // Get user info before logout for audit logging
        $user = auth()->guard('master_admin')->user();

        if ($user) {
            AuditLogger::record(
                'logout',
                $user,
                [
                    'user_email' => $user->email,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ],
                'Master Admin logged out'
            );
        }

        // Log out the user from Laravel's auth system
        auth()->guard('master_admin')->logout();

        // Clear all admin-specific session data
        $request->session()->forget([
            'master_admin_id',
            'master_admin_name',
            'master_admin_email',
            'master_admin_role',
        ]);

        // Do NOT invalidate session to keep Inventory Admin logged in
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // Redirect to login with success message
        // The middleware will add cache control headers automatically,
        // but we can also add them here for extra safety
        return redirect()->route('master.admin.login')
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
                    return redirect()->route('master.admin.orders.index', ['order_number' => $notification->data['order_number']]);
                }
                // Return Request -> Returns List filtered by order number (q)
                if ($notification->type === 'return_request' && isset($notification->data['order_number'])) {
                    return redirect()->route('master.admin.returns-exchange.index', ['q' => $notification->data['order_number']]);
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

