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

        $products = ProductMapping::query()
            ->when($filters['school_id'] ?? null, fn($q, $schoolId) => $q->where('school_id', $schoolId))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
            ->get();

        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        $ordersKpi = [
            'total_orders' => 412,
            'processing' => 28,
            'shipped' => 36,
            'delivered' => 312,
            'failed' => 12,
        ];

        $financialKpi = [
            'total_sales' => 186000,
            'total_tax' => 14600,
            'total_shipping' => 9200,
        ];

        $lowStockCount = $products->filter(function ($product) {
            if (is_null($product->low_stock_threshold)) {
                return false;
            }

            return $product->inventory_stock <= $product->low_stock_threshold;
        })->count();

        $stockKpi = [
            'in_stock' => $products->where('inventory_stock', '>', 0)->count(),
            'out_of_stock' => $products->where('inventory_stock', '<=', 0)->count(),
            'low_stock' => $lowStockCount,
            'returns' => 14,
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

        $ordersBySchoolData = $schools->map(fn($school) => [
            'label' => $school->name,
            'value' => rand(10, 60),
        ]);

        if ($ordersBySchoolData->isEmpty()) {
            $ordersBySchoolData = collect([
                ['label' => 'No schools yet', 'value' => 0],
            ]);
        }

        $ordersByCategoryData = $categories->map(fn($category) => [
            'label' => $category,
            'value' => rand(20, 80),
        ]);

        if ($ordersByCategoryData->isEmpty()) {
            $ordersByCategoryData = collect([
                ['label' => 'Uniform', 'value' => 45],
                ['label' => 'Sports', 'value' => 22],
            ]);
        }

        $charts = [
            'salesTrend' => [
                'title' => 'Sales over time',
                'labels' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                'series' => [52, 68, 71, 90, 110, 132, 97],
            ],
            'ordersBySchool' => [
                'title' => 'Orders by school',
                'data' => $ordersBySchoolData->toArray(),
            ],
            'ordersByCategory' => [
                'title' => 'Orders by category',
                'data' => $ordersByCategoryData->toArray(),
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

        $lowStockAlerts = ProductMapping::whereColumn('inventory_stock', '<=', 'low_stock_threshold')
            ->orderBy('inventory_stock')
            ->take(3)
            ->get();

        $alerts = [
            [
                'type' => 'Low stock',
                'items' => $lowStockAlerts->map(fn($mapping) => $mapping->product_name . ' (' . $mapping->inventory_stock . ' units left)'),
            ],
            [
                'type' => 'Delayed orders',
                'items' => collect(['#ORD-1045 stuck at hub', '#ORD-1061 pending dispatch']),
            ],
            [
                'type' => 'Failed payments',
                'items' => collect(['Parent S Kumar (₹1,499)', 'Parent R Devi (₹2,249)']),
            ],
        ];

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

