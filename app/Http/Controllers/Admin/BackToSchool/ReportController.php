<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\ProductMapping;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'status']);

        // Stats Logic
        // Strict scope: explicit type only
        $query = Order::query()->where(function($q) {
            $q->where('product_type', 'back_to_school');
        });

        $totalOrders = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        
        // Product-wise Sales
        $productSales = Order::selectRaw('item_name, SUM(quantity) as total_qty, SUM(total_amount) as total_revenue')
            ->where(function($q) {
                $q->where('product_type', 'back_to_school');
            })
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // Recent Orders for display
        $orders = (clone $query)->latest()->take(10)->get();

        return view('admin.back_to_school.reports.index', compact('totalOrders', 'totalRevenue', 'productSales', 'orders', 'filters'));
    }
}
