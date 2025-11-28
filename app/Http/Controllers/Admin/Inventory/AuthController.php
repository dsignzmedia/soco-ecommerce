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
use Illuminate\View\View;

class AuthController extends Controller
{
    /**
     * Display the inventory admin login screen.
     */
    public function showLoginForm(): View
    {
        return view('inventoryadmin.auth.login');
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
        // Order Metrics
        $ordersToday = \App\Models\Admin\Master\Order::whereDate('created_at', today())->count();
        $pendingPicking = \App\Models\Admin\Master\Order::where('order_status', 'pending')->count();
        $pendingPacking = \App\Models\Admin\Master\Order::where('order_status', 'processing')->count();
        $pendingShipment = \App\Models\Admin\Master\Order::where('order_status', 'ready_to_ship')->count();
        
        $delayedOrders = \App\Models\Admin\Master\Order::where('created_at', '<', now()->subDays(2))
            ->whereNotIn('order_status', ['completed', 'cancelled', 'delivered'])
            ->count();

        $schoolsWithOrders = \App\Models\Admin\Master\Order::whereIn('order_status', ['pending', 'processing', 'ready_to_ship'])
            ->distinct('school_id')
            ->count();
            
        $gradesWithOrders = \App\Models\Admin\Master\Order::whereIn('order_status', ['pending', 'processing', 'ready_to_ship'])
            ->distinct('grade')
            ->count();

        // Stock Metrics
        $products = ProductMapping::select('inventory_stock', 'low_stock_threshold')->get();
        $outOfStock = $products->where('inventory_stock', '<=', 0)->count();
        $lowStock = $products->filter(
            fn ($product) => $product->low_stock_threshold !== null
                && $product->inventory_stock <= $product->low_stock_threshold
        )->count();

        return view('inventoryadmin.dashboard', compact(
            'ordersToday',
            'pendingPicking',
            'pendingPacking',
            'pendingShipment',
            'delayedOrders',
            'schoolsWithOrders',
            'gradesWithOrders',
            'outOfStock',
            'lowStock'
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

        $adminId = request()->session()->get('admin_id');

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
     * Logout the inventory admin user.
     */
    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('admin_id');
        $request->session()->forget('admin_name');
        $request->session()->forget('admin_email');

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('inventory.admin.login')->with('status', 'You have been logged out successfully.');
    }
}

