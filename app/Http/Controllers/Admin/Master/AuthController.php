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

class AuthController extends Controller
{
    /**
     * Display the master admin login screen.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');
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
        $filters = $request->only(['date_range', 'school_id', 'category']);

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
        if (!empty($filters['date_range'])) {
            $dates = explode(' - ', $filters['date_range']);
            if (count($dates) == 2) {
                $ordersQuery->whereBetween('order_date', [
                    \Carbon\Carbon::parse($dates[0])->startOfDay(),
                    \Carbon\Carbon::parse($dates[1])->endOfDay()
                ]);
            }
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
        // Group by date
        $salesData = (clone $ordersQuery)
            ->selectRaw('DATE(order_date) as date, SUM(total_amount) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->get();
            
        $chartLabels = $salesData->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))->toArray();
        $chartSeries = $salesData->pluck('total')->toArray();
        
        // If empty, show last 7 days empty
        if (empty($chartLabels)) {
            $period = \Carbon\CarbonPeriod::create(now()->subDays(6), now());
            foreach ($period as $date) {
                $chartLabels[] = $date->format('M d');
                $chartSeries[] = 0;
            }
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

        $adminId = $request->session()->get('admin_id');
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
     */
    public function logout(Request $request)
    {
        // Clear any admin session data
        $request->session()->forget('admin_id');
        $request->session()->forget('admin_name');
        $request->session()->forget('admin_email');

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('master.admin.login')->with('status', 'You have been logged out successfully.');
    }
}

