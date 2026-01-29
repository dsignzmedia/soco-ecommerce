<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Order;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\ReturnExchangeRequest;
use App\Models\Admin\Master\School;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['school_id', 'grade', 'category', 'date_from', 'date_to', 'product_name', 'status']);

        // Build base query with filters
        $baseOrderQuery = $this->buildOrderQuery($filters);
        $baseProductQuery = $this->buildProductQuery($filters);

        // Calculate all report data
        $reportData = [
            'orders' => $this->getOrdersReport($baseOrderQuery),
            'revenue' => $this->getRevenueReport($baseOrderQuery, $filters),
            'product_performance' => $this->getProductPerformanceReport($baseOrderQuery),
            'stock' => $this->getStockReport($baseProductQuery),
            'shipping_cost' => $this->getShippingCostReport($baseOrderQuery),
            'tax' => $this->getTaxReport($baseOrderQuery),
            'school_wise' => $this->getSchoolWiseReport($baseOrderQuery),
            'grade_wise' => $this->getGradeWiseReport($baseOrderQuery),
            'category_wise' => $this->getCategoryWiseReport($baseOrderQuery),
            'return_exchange' => $this->getReturnExchangeReport($filters),
        ];

        $reportTypes = [
            ['label' => 'Orders', 'key' => 'orders', 'description' => 'Order counts, status, fulfilment SLAs'],
            ['label' => 'Revenue', 'key' => 'revenue', 'description' => 'Gross vs net revenue, tax, shipping'],
            ['label' => 'Product Performance', 'key' => 'product_performance', 'description' => 'Best sellers, velocity, returns'],
            ['label' => 'Stock', 'key' => 'stock', 'description' => 'In stock/out of stock/aging snapshot'],
            ['label' => 'Shipping Cost', 'key' => 'shipping_cost', 'description' => 'Average cost per order, per zone'],
            ['label' => 'Tax', 'key' => 'tax', 'description' => 'Tax collected per period/tax profile'],
            ['label' => 'School-wise', 'key' => 'school_wise', 'description' => 'Orders and revenue by school'],
            ['label' => 'Grade wise', 'key' => 'grade_wise', 'description' => 'Demand by grade segments'],
            ['label' => 'Category-wise', 'key' => 'category_wise', 'description' => 'Revenue & units by category'],
            ['label' => 'Return/Exchange', 'key' => 'return_exchange', 'description' => 'Return rates, reasons, processing time'],
        ];

        // Get filtered orders for snapshot (with pagination support)
        $orders = (clone $baseOrderQuery)->with('school')->latest()->paginate(20);
        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.reports.index', compact('reportTypes', 'reportData', 'filters', 'orders', 'schools', 'categories'));
    }

    protected function buildOrderQuery(array $filters)
    {
        return Order::query()
            // Exclude exchange orders from revenue calculations (they have zero payment)
            ->where(function($q) {
                $q->whereNull('return_exchange_status')
                  ->orWhere('return_exchange_status', '!=', 'exchange_created');
            })
            ->when($filters['school_id'] ?? null, fn ($q, $school) => $q->where('school_id', $school))
            ->when($filters['grade'] ?? null, fn ($q, $grade) => $q->where('grade', $grade))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('order_status', $status))
            ->when($filters['product_name'] ?? null, fn ($q, $product) => $q->where('item_name', 'like', '%' . $product . '%'))
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('order_date', '<=', $to));
    }

    protected function buildProductQuery(array $filters)
    {
        return ProductMapping::query()
            ->when($filters['school_id'] ?? null, fn ($q, $school) => $q->where('school_id', $school))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category));
    }

    protected function getOrdersReport($query)
    {
        $total = (clone $query)->count();
        $byStatus = (clone $query)
            ->select('order_status', DB::raw('count(*) as count'))
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        $avgValue = (clone $query)->avg('total_amount') ?? 0;

        // Calculate average fulfilment SLA (time from order_date to delivery)
        $slaOrders = (clone $query)
            ->whereNotNull('order_date')
            ->where('order_status', 'delivered')
            ->get();

        $avgSla = 0;
        if ($slaOrders->count() > 0) {
            $totalDays = $slaOrders->sum(function ($order) {
                return $order->updated_at ? $order->order_date->diffInDays($order->updated_at) : 0;
            });
            $avgSla = round($totalDays / $slaOrders->count(), 1);
        }

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'avg_value' => round($avgValue, 2),
            'fulfilment_sla' => $avgSla,
        ];
    }

    protected function getRevenueReport($query, array $filters)
    {
        $gross = (clone $query)->sum('total_amount') ?? 0;
        $tax = (clone $query)->sum('tax_amount') ?? 0;
        $shipping = (clone $query)->sum('shipping_cost') ?? 0;

        // Calculate net revenue (gross - refunds)
        $orderIds = (clone $query)->pluck('id');
        $refunds = Payment::whereIn('order_id', $orderIds)
            ->where('payment_for', 'refund')
            ->where('payment_status', 'refunded')
            ->sum('amount_paid') ?? 0;

        $net = max(0, $gross - $refunds);

        // Revenue by date range (if dates provided)
        $revenueByDate = [];
        if (isset($filters['date_from']) || isset($filters['date_to'])) {
            $revenueByDate = (clone $query)
                ->select(DB::raw('DATE(order_date) as date'), DB::raw('SUM(total_amount) as revenue'))
                ->groupBy('date')
                ->orderBy('date')
                ->pluck('revenue', 'date')
                ->toArray();
        }

        return [
            'gross' => round($gross, 2),
            'net' => round($net, 2),
            'tax' => round($tax, 2),
            'shipping' => round($shipping, 2),
            'refunds' => round($refunds, 2),
            'by_date' => $revenueByDate,
        ];
    }

    protected function getProductPerformanceReport($query)
    {
        $bestSellers = (clone $query)
            ->select('item_name', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(total_amount) as total_revenue'), DB::raw('COUNT(*) as order_count'))
            ->groupBy('item_name')
            ->orderByDesc('total_revenue')
            ->take(10)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->item_name,
                    'quantity' => $item->total_qty,
                    'revenue' => round($item->total_revenue, 2),
                    'orders' => $item->order_count,
                ];
            });

        // Calculate return rate per product
        $productReturns = ReturnExchangeRequest::with('order')
            ->whereHas('order', function ($q) use ($query) {
                $orderIds = (clone $query)->pluck('id');
                $q->whereIn('id', $orderIds);
            })
            ->select('order_id', DB::raw('SUM(requested_quantity) as returned_qty'))
            ->groupBy('order_id')
            ->get();

        $totalSold = (clone $query)->sum('quantity') ?? 0;
        $totalReturned = $productReturns->sum('returned_qty') ?? 0;
        $returnRate = $totalSold > 0 ? round(($totalReturned / $totalSold) * 100, 2) : 0;

        // Products with zero sales
        $zeroSales = ProductMapping::whereNotIn('product_name', (clone $query)->distinct()->pluck('item_name'))
            ->count();

        return [
            'best_sellers' => $bestSellers,
            'return_rate' => $returnRate,
            'total_sold' => $totalSold,
            'total_returned' => $totalReturned,
            'zero_sales_count' => $zeroSales,
        ];
    }

    protected function getStockReport($query)
    {
        $totalStock = (clone $query)->sum('inventory_stock') ?? 0;
        $inStock = (clone $query)->where('inventory_stock', '>', 0)->count();
        $outOfStock = (clone $query)->where('inventory_stock', '<=', 0)->count();
        $lowStock = (clone $query)
            ->whereColumn('inventory_stock', '<=', 'low_stock_threshold')
            ->where('inventory_stock', '>', 0)
            ->count();

        // Stock aging (products not updated in X days)
        $aging = (clone $query)
            ->select('product_name', 'inventory_stock', 'updated_at', 'category')
            ->get()
            ->map(function ($product) {
                $days = $product->updated_at ? $product->updated_at->diffInDays(now()) : 0;
                return [
                    'name' => $product->product_name,
                    'stock' => $product->inventory_stock,
                    'days' => $days,
                    'category' => $product->category,
                ];
            })
            ->sortByDesc('days')
            ->take(10)
            ->values();

        // Stock by school
        $bySchoolQuery = clone $query;
        $bySchool = $bySchoolQuery
            ->select('school_id', DB::raw('SUM(inventory_stock) as total'))
            ->groupBy('school_id')
            ->with('school')
            ->get()
            ->map(function ($item) {
                return [
                    'school' => $item->school?->name ?? 'Unknown',
                    'total' => $item->total,
                ];
            });

        // Stock by category
        $byCategoryQuery = clone $query;
        $byCategory = $byCategoryQuery
            ->select('category', DB::raw('SUM(inventory_stock) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category')
            ->toArray();

        return [
            'total_stock' => $totalStock,
            'in_stock_count' => $inStock,
            'out_of_stock_count' => $outOfStock,
            'low_stock_count' => $lowStock,
            'aging' => $aging,
            'by_school' => $bySchool,
            'by_category' => $byCategory,
        ];
    }

    protected function getShippingCostReport($query)
    {
        $total = (clone $query)->sum('shipping_cost') ?? 0;
        $count = (clone $query)->where('shipping_cost', '>', 0)->count();
        $avg = $count > 0 ? round($total / $count, 2) : 0;

        return [
            'total' => round($total, 2),
            'average' => $avg,
            'orders_with_shipping' => $count,
        ];
    }

    protected function getTaxReport($query)
    {
        $total = (clone $query)->sum('tax_amount') ?? 0;

        // Tax by category
        $byCategory = (clone $query)
            ->select('category', DB::raw('SUM(tax_amount) as total'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->pluck('total', 'category')
            ->toArray();

        return [
            'total' => round($total, 2),
            'by_category' => array_map(fn($v) => round($v, 2), $byCategory),
        ];
    }

    protected function getSchoolWiseReport($query)
    {
        $data = (clone $query)
            ->select('school_id', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as revenue'), DB::raw('AVG(total_amount) as avg_order_value'))
            ->groupBy('school_id')
            ->with('school')
            ->get()
            ->map(function ($item) {
                return [
                    'school' => $item->school?->name ?? 'Unknown',
                    'orders' => $item->order_count,
                    'revenue' => round($item->revenue, 2),
                    'avg_order_value' => round($item->avg_order_value, 2),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return [
            'data' => $data,
            'total_schools' => $data->count(),
        ];
    }

    protected function getGradeWiseReport($query)
    {
        $data = (clone $query)
            ->select('grade', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as revenue'), DB::raw('SUM(quantity) as total_quantity'))
            ->whereNotNull('grade')
            ->groupBy('grade')
            ->get()
            ->map(function ($item) {
                return [
                    'grade' => $item->grade,
                    'orders' => $item->order_count,
                    'revenue' => round($item->revenue, 2),
                    'quantity' => $item->total_quantity,
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return [
            'data' => $data,
            'total_grades' => $data->count(),
        ];
    }

    protected function getCategoryWiseReport($query)
    {
        $data = (clone $query)
            ->select('category', DB::raw('COUNT(*) as order_count'), DB::raw('SUM(total_amount) as revenue'), DB::raw('SUM(quantity) as total_quantity'), DB::raw('AVG(total_amount) as avg_price'))
            ->whereNotNull('category')
            ->groupBy('category')
            ->get()
            ->map(function ($item) {
                return [
                    'category' => $item->category,
                    'orders' => $item->order_count,
                    'revenue' => round($item->revenue, 2),
                    'quantity' => $item->total_quantity,
                    'avg_price' => round($item->avg_price, 2),
                ];
            })
            ->sortByDesc('revenue')
            ->values();

        return [
            'data' => $data,
            'total_categories' => $data->count(),
        ];
    }

    protected function getReturnExchangeReport(array $filters)
    {
        $returnQuery = ReturnExchangeRequest::query()
            ->when($filters['date_from'] ?? null, function ($q, $from) {
                $q->whereHas('order', fn($oq) => $oq->whereDate('order_date', '>=', $from));
            })
            ->when($filters['date_to'] ?? null, function ($q, $to) {
                $q->whereHas('order', fn($oq) => $oq->whereDate('order_date', '<=', $to));
            })
            ->when($filters['school_id'] ?? null, function ($q, $school) {
                $q->whereHas('order', fn($oq) => $oq->where('school_id', $school));
            });

        $total = $returnQuery->count();
        $byStatus = (clone $returnQuery)
            ->select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $byReason = (clone $returnQuery)
            ->select('reason', DB::raw('count(*) as count'))
            ->whereNotNull('reason')
            ->groupBy('reason')
            ->pluck('count', 'reason')
            ->toArray();

        $byType = (clone $returnQuery)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();

        // Calculate average processing time
        $completed = (clone $returnQuery)
            ->whereIn('status', ['completed', 'received_restocked', 'received_discarded'])
            ->with('order')
            ->get();

        $avgProcessingTime = 0;
        if ($completed->count() > 0) {
            $totalDays = $completed->sum(function ($request) {
                if ($request->order && $request->order->order_date && $request->updated_at) {
                    return $request->order->order_date->diffInDays($request->updated_at);
                }
                return 0;
            });
            $avgProcessingTime = round($totalDays / $completed->count(), 1);
        }

        // Calculate total refund amount
        $orderIds = (clone $returnQuery)->pluck('order_id');
        $totalRefunds = Payment::whereIn('order_id', $orderIds)
            ->where('payment_for', 'refund')
            ->where('payment_status', 'refunded')
            ->sum('amount_paid') ?? 0;

        // Calculate return rate
        $totalOrders = Order::when($filters['date_from'] ?? null, fn($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn($q, $to) => $q->whereDate('order_date', '<=', $to))
            ->when($filters['school_id'] ?? null, fn($q, $school) => $q->where('school_id', $school))
            ->count();

        $returnRate = $totalOrders > 0 ? round(($total / $totalOrders) * 100, 2) : 0;

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'by_reason' => $byReason,
            'by_type' => $byType,
            'avg_processing_time' => $avgProcessingTime,
            'total_refunds' => round($totalRefunds, 2),
            'return_rate' => $returnRate,
        ];
    }

    public function export(Request $request, string $type)
    {
        $type = strtolower($type);
        abort_unless(in_array($type, ['csv', 'excel', 'pdf'], true), 404);

        $filters = $request->only(['school_id', 'grade', 'category', 'date_from', 'date_to', 'product_name', 'status']);

        $orders = Order::with('school')
            ->when($filters['school_id'] ?? null, fn ($q, $school) => $q->where('school_id', $school))
            ->when($filters['grade'] ?? null, fn ($q, $grade) => $q->where('grade', $grade))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('order_status', $status))
            ->when($filters['product_name'] ?? null, fn ($q, $product) => $q->where('item_name', 'like', '%' . $product . '%'))
            ->when($filters['date_from'] ?? null, fn ($q, $from) => $q->whereDate('order_date', '>=', $from))
            ->when($filters['date_to'] ?? null, fn ($q, $to) => $q->whereDate('order_date', '<=', $to))
            ->get();

        try {
            return match ($type) {
                'csv' => $this->downloadDelimited($orders, ',', 'reports.csv', 'text/csv'),
                'excel' => $this->downloadDelimited($orders, "\t", 'reports.xls', 'application/vnd.ms-excel'),
                'pdf' => $this->downloadPdf($orders),
            };
        } catch (\Throwable $e) {
            return response("Export Error: " . $e->getMessage() . " in " . $e->getFile() . " line " . $e->getLine());
        }
    }

    protected function downloadDelimited(Collection $orders, string $delimiter, string $filename, string $contentType)
    {
        return response()->streamDownload(function () use ($orders, $delimiter) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Order ID', 'Date', 'School', 'Student', 'Grade', 'Category', 'Item', 'Qty', 'Amount', 'Tax', 'Shipping', 'Status'], $delimiter);
            foreach ($orders as $order) {
                fputcsv($handle, [
                    $order->order_number,
                    optional($order->order_date)->format('Y-m-d'),
                    optional($order->school)->name,
                    $order->student_name,
                    $order->grade,
                    $order->category,
                    $order->item_name,
                    $order->quantity,
                    $order->total_amount,
                    $order->tax_amount,
                    $order->shipping_cost,
                    $order->order_status,
                ], $delimiter);
            }
            fclose($handle);
        }, $filename, ['Content-Type' => $contentType]);
    }

    protected function downloadPdf(Collection $orders)
    {
        $pdf = Pdf::loadView('admin.reports.pdf', compact('orders'));
        return $pdf->download('reports.pdf');
    }
}

