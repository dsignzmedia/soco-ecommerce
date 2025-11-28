<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\InventoryAdjustment;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function dashboard(): View
    {
        $products = ProductMapping::with('school')->get();

        $totalStock = $products->sum('inventory_stock');
        $inStock = $products->where('inventory_stock', '>', 0)->count();
        $outOfStock = $products->where('inventory_stock', '<=', 0)->count();
        $lowStock = $products->filter(fn ($product) => $product->low_stock_threshold !== null && $product->inventory_stock <= $product->low_stock_threshold)->count();

        $aging = $products->map(function ($product) {
            $days = optional($product->updated_at)->diffInDays(now()) ?? 0;
            return [
                'product' => $product->product_name,
                'days' => $days,
                'category' => $product->category,
                'stock' => $product->inventory_stock,
            ];
        })->sortByDesc('days')->take(8);

        return view('admin.inventory.dashboard', compact('totalStock', 'inStock', 'outOfStock', 'lowStock', 'aging'));
    }

    public function index(Request $request): View
    {
        $filters = $request->only(['school_id', 'category', 'status', 'q']);

        $products = ProductMapping::with('school')
            ->when($filters['school_id'] ?? null, fn($q, $school) => $q->where('school_id', $school))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, fn($q, $term) => $q->where('product_name', 'like', '%'.$term.'%'))
            ->orderBy('product_name')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.inventory.list', compact('products', 'schools', 'categories', 'filters'));
    }

    public function adjust(ProductMapping $product): View
    {
        $recentAdjustments = $product->inventoryAdjustments()->latest()->take(5)->get();

        return view('admin.inventory.adjust', compact('product', 'recentAdjustments'));
    }

    public function applyAdjustment(Request $request, ProductMapping $product): RedirectResponse
    {
        $data = $request->validate([
            'quantity_change' => ['required', 'integer', 'not_in:0'],
            'reason' => ['required', 'in:purchase,return,damage,correction'],
            'comment' => ['nullable', 'string', 'max:500'],
        ]);

        $before = $product->inventory_stock;
        $after = $before + $data['quantity_change'];
        $product->update(['inventory_stock' => $after]);

        InventoryAdjustment::create([
            'product_mapping_id' => $product->id,
            'quantity_change' => $data['quantity_change'],
            'reason' => $data['reason'],
            'comment' => $data['comment'],
            'stock_before' => $before,
            'stock_after' => $after,
        ]);

        AuditLogger::record(
            'stock_adjustment',
            $product,
            [
                'product' => $product->product_name,
                'quantity_change' => $data['quantity_change'],
                'reason' => $data['reason'],
                'comment' => $data['comment'],
                'before' => $before,
                'after' => $after,
            ],
            'Stock adjusted via inventory admin panel'
        );

        return redirect()->route('master.admin.inventory.list')->with('status', 'Stock adjusted successfully.');
    }

    public function reports(): View
    {
        $lowStock = ProductMapping::whereColumn('inventory_stock', '<=', 'low_stock_threshold')->get();
        $outOfStock = ProductMapping::where('inventory_stock', '<=', 0)->get();

        $stockBySchool = ProductMapping::selectRaw('school_id, SUM(inventory_stock) as total')
            ->groupBy('school_id')
            ->with('school')
            ->get();

        $stockByCategory = ProductMapping::selectRaw('category, SUM(inventory_stock) as total')
            ->groupBy('category')
            ->get();

        $agingBuckets = [
            '0-30 days' => 0,
            '31-60 days' => 0,
            '61-90 days' => 0,
            '90+ days' => 0,
        ];

        ProductMapping::select('inventory_stock', 'updated_at')->each(function ($product) use (&$agingBuckets) {
            $days = optional($product->updated_at)->diffInDays(now()) ?? 0;
            if ($days <= 30) {
                $agingBuckets['0-30 days'] += $product->inventory_stock;
            } elseif ($days <= 60) {
                $agingBuckets['31-60 days'] += $product->inventory_stock;
            } elseif ($days <= 90) {
                $agingBuckets['61-90 days'] += $product->inventory_stock;
            } else {
                $agingBuckets['90+ days'] += $product->inventory_stock;
            }
        });

        $movements = InventoryAdjustment::with('product')->latest()->paginate(20);

        return view('admin.inventory.reports', compact('lowStock', 'outOfStock', 'stockBySchool', 'stockByCategory', 'agingBuckets', 'movements'));
    }
}

