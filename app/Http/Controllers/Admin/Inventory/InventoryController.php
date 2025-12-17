<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use App\Models\Admin\Master\InventoryAdjustment;
use App\Support\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
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

        return view('inventoryadmin.inventory.index', compact('products', 'schools', 'categories', 'filters'));
    }

    public function adjust(ProductMapping $product): View
    {
        $recentAdjustments = $product->inventoryAdjustments()->latest()->take(5)->get();

        return view('inventoryadmin.inventory.adjust', compact('product', 'recentAdjustments'));
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

        // Audit Log
        try {
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
                'Stock adjusted via Inventory Admin'
            );
        } catch (\Exception $e) {
            // Silently fail logging
        }

        return redirect()->route('inventory.admin.inventory.index')->with('status', 'Stock adjusted successfully.');
    }

    public function reports(): View
    {
        $lowStock = ProductMapping::with('school')->whereColumn('inventory_stock', '<=', 'low_stock_threshold')->get();
        $outOfStock = ProductMapping::with('school')->where('inventory_stock', '<=', 0)->get();

        $stockBySchool = ProductMapping::selectRaw('school_id, SUM(inventory_stock) as total')
            ->groupBy('school_id')
            ->with('school')
            ->get();

        $stockByGrade = ProductMapping::selectRaw('grade_id, SUM(inventory_stock) as total')
            ->groupBy('grade_id')
            ->with('grade')
            ->get();

        return view('inventoryadmin.reports.index', compact('lowStock', 'outOfStock', 'stockBySchool', 'stockByGrade'));
    }
}
