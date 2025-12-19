<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\Order;
use App\Models\Merchandise\Product;
use App\Models\Merchandise\PrintJob;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistics restricted to Merchandise domain
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $totalProducts = Product::count();
        $pendingPrintJobs = PrintJob::where('status', 'pending')->count();
        $lowStockProducts = Product::where('inventory_stock', '<=', 5)->count();
        $activeProducts = Product::where('status', 'live')->count();
        $inactiveProducts = Product::where('status', 'draft')->count();

        // Orders that are currently active (not delivered or cancelled)
        $ordersInProgress = Order::whereNotIn('order_status', ['delivered', 'cancelled'])->latest()->take(10)->get();

        return view('admin.merchandise.dashboard', compact('totalOrders', 'pendingOrders', 'totalProducts', 'pendingPrintJobs', 'lowStockProducts', 'activeProducts', 'inactiveProducts', 'ordersInProgress'));
    }
}
