<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['date_from', 'date_to', 'status']);

        // Strict scope for Merchandise Orders
        $query = Order::query()->where('product_type', 'merchandised');

        $totalOrders = (clone $query)->count();
        $totalRevenue = (clone $query)->sum('total_amount');
        
        // Product-wise Sales
        $productSales = Order::selectRaw('item_name, SUM(quantity) as total_qty, SUM(total_amount) as total_revenue')
            ->where('product_type', 'merchandised')
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get();

        // Recent Orders for display
        $orders = (clone $query)->latest()->take(10)->get();

        return view('admin.merchandise.reports.index', compact('totalOrders', 'totalRevenue', 'productSales', 'orders', 'filters'));
    }
}
