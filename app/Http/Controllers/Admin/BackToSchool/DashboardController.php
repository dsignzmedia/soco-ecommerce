<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\BackToSchool\Order;
use App\Models\BackToSchool\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Statistics restricted to Back-To-School domain
        $totalOrders = Order::count();
        $pendingOrders = Order::where('order_status', 'pending')->count();
        $totalProducts = Product::count();
        $activeProducts = Product::where('status', 'live')->count();
        $inactiveProducts = Product::where('status', 'draft')->count();
        $lowStockProducts = Product::whereColumn('inventory_stock', '<=', 'low_stock_threshold')->count();

        $recentOrders = Order::latest()->take(5)->get();

        return view('admin.back_to_school.dashboard', compact('totalOrders', 'pendingOrders', 'totalProducts', 'activeProducts', 'inactiveProducts', 'lowStockProducts', 'recentOrders'));
    }
}
