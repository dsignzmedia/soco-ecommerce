<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\Order;
use App\Models\Merchandise\Product;
use App\Models\Merchandise\PrintJob;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'start_date', 'end_date']);

        // Base queries - Merchandise only
        $ordersQuery = Order::query();
        $productsQuery = Product::query();

        // Apply Filters to Orders
        if (!empty($filters['category'])) {
            $ordersQuery->where('category', $filters['category']);
        }
        
        // Date Range Filtering
        if (!empty($filters['start_date'])) {
            $ordersQuery->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $ordersQuery->whereDate('created_at', '<=', $filters['end_date']);
        }

        // Apply Filters to Products (for stock KPIs)
        if (!empty($filters['category'])) {
            $productsQuery->where('category', $filters['category']);
        }

        // --- KPI Cards ---
        $ordersKpi = [
            'total_orders' => (clone $ordersQuery)->count(),
            'order_placed' => (clone $ordersQuery)->where('order_status', 'order_placed')->count(),
            'processing' => (clone $ordersQuery)->where('order_status', 'processing')->count(),
            'packed' => (clone $ordersQuery)->where('order_status', 'packed')->count(),
            'shipped' => (clone $ordersQuery)->where('order_status', 'shipped')->count(),
            'delivered' => (clone $ordersQuery)->where('order_status', 'delivered')->count(),
        ];

        // Calculate refunds from Payment table for merchandise
        $refundsQuery = \App\Models\Payment::where('payment_for', 'refund')
            ->where('payment_status', 'refunded')
            ->where('product_type', 'merchandised');
        
        // Apply date filters to refunds if set
        if (!empty($filters['start_date'])) {
            $refundsQuery->whereDate('created_at', '>=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $refundsQuery->whereDate('created_at', '<=', $filters['end_date']);
        }
        
        // Apply category filter to refunds (through order relationship)
        if (!empty($filters['category'])) {
            $refundsQuery->whereHas('order', function($q) use ($filters) {
                $q->where('category', $filters['category']);
            });
        }
        
        $totalRefunds = $refundsQuery->sum('amount_paid') ?? 0;
        
        $financialKpi = [
            'total_sales' => (clone $ordersQuery)->sum('total_amount'),
            'total_refunds' => $totalRefunds,
            'net_revenue' => ((clone $ordersQuery)->sum('total_amount') ?? 0) - $totalRefunds,
        ];

        // Stock KPIs
        $stockKpi = [
            'in_stock' => (clone $productsQuery)->where('inventory_stock', '>', 0)->count(),
            'out_of_stock' => (clone $productsQuery)->where('inventory_stock', '<=', 0)->count(),
            'low_stock' => (clone $productsQuery)->whereColumn('inventory_stock', '<=', 'low_stock_threshold')->count(),
            'active_products' => (clone $productsQuery)->where('status', 'live')->count(),
            'pending_print_jobs' => PrintJob::where('status', 'pending')->count(),
        ];

        $kpis = [
            ['label' => 'Total Orders', 'value' => $ordersKpi['total_orders']],
            ['label' => 'Order Placed', 'value' => $ordersKpi['order_placed']],
            ['label' => 'Processing', 'value' => $ordersKpi['processing']],
            ['label' => 'Packed', 'value' => $ordersKpi['packed']],
            ['label' => 'Shipped', 'value' => $ordersKpi['shipped']],
            ['label' => 'Delivered', 'value' => $ordersKpi['delivered']],
            ['label' => 'Total Sales', 'prefix' => '₹', 'value' => number_format($financialKpi['total_sales'])],
            ['label' => 'Total Refunds', 'prefix' => '₹', 'value' => number_format($financialKpi['total_refunds']), 'color' => 'kpi-red'],
            ['label' => 'Net Revenue', 'prefix' => '₹', 'value' => number_format($financialKpi['net_revenue']), 'color' => 'kpi-green'],
            ['label' => 'In-stock SKUs', 'value' => $stockKpi['in_stock']],
            ['label' => 'Out-of-stock SKUs', 'value' => $stockKpi['out_of_stock']],
            ['label' => 'Low Stock Products', 'value' => $stockKpi['low_stock']],
            ['label' => 'Active Products', 'value' => $stockKpi['active_products']],
            ['label' => 'Pending Print Jobs', 'value' => $stockKpi['pending_print_jobs']],
        ];

        // --- Charts ---
        $startDate = !empty($filters['start_date']) ? \Carbon\Carbon::parse($filters['start_date']) : now()->subDays(6);
        $endDate = !empty($filters['end_date']) ? \Carbon\Carbon::parse($filters['end_date']) : now();

        $salesData = (clone $ordersQuery)
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as total')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total', 'date');

        $chartLabels = [];
        $chartSeries = [];

        $period = \Carbon\CarbonPeriod::create($startDate, $endDate);
        foreach ($period as $date) {
            $formattedDate = $date->format('Y-m-d');
            $chartLabels[] = $date->format('M d');
            $chartSeries[] = $salesData->get($formattedDate) ?? 0;
        }

        // Orders by Category
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
                'labels' => $chartLabels,
                'series' => $chartSeries,
            ],
            'ordersByCategory' => [
                'data' => $ordersByCategory->isEmpty() ? [['label' => 'No orders', 'value' => 0]] : $ordersByCategory->toArray(),
            ],
        ];

        // --- Alerts ---
        $lowStockAlerts = Product::whereColumn('inventory_stock', '<=', 'low_stock_threshold')
            ->orderBy('inventory_stock')
            ->take(5)
            ->get();

        $delayedOrders = Order::where('order_status', 'processing')
            ->where('created_at', '<', now()->subDays(3))
            ->take(5)
            ->get();

        $pendingPrintJobs = PrintJob::where('status', 'pending')->take(5)->get();

        $alerts = [
            [
                'type' => 'Low stock',
                'items' => $lowStockAlerts->map(fn($p) => $p->product_name . ' (' . $p->inventory_stock . ' left)'),
            ],
            [
                'type' => 'Delayed orders',
                'items' => $delayedOrders->map(fn($o) => '#' . $o->order_number . ' (' . $o->created_at->diffForHumans() . ')'),
            ],
            [
                'type' => 'Pending print jobs',
                'items' => $pendingPrintJobs->map(fn($job) => 'Print Job #' . $job->id . ' - ' . ($job->product_name ?? 'N/A')),
            ],
        ];

        // Data for Filters
        $categories = Product::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.merchandise.dashboard', [
            'kpis' => $kpis,
            'charts' => $charts,
            'alerts' => $alerts,
            'filters' => $filters,
            'categories' => $categories,
        ]);
    }
}
