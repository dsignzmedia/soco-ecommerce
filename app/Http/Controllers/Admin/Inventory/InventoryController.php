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

        $products = ProductMapping::with(['school', 'variants'])
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

    public function updateVariantStock(Request $request, ProductMapping $product)
    {
        try {
            $validated = $request->validate([
                'variant_id' => ['required', 'exists:product_variants,id'],
                'stock' => ['required', 'integer', 'min:0'],
            ]);

            // Verify the variant belongs to this product (security check)
            $variant = \App\Models\ProductVariant::where('id', $validated['variant_id'])
                ->where('product_mapping_id', $product->id)
                ->firstOrFail();

            // Store old stock
            $oldStock = $variant->stock;
            $productTotalBefore = $product->inventory_stock;
            
            // Update ONLY this variant's stock
            $variant->update(['stock' => $validated['stock']]);
            
            // Recalculate and update product total stock from ALL variants
            $product->updateTotalStock();
            $productTotalAfter = $product->fresh()->inventory_stock;

            // Audit Log
            try {
                AuditLogger::record(
                    'variant_stock_update',
                    $product,
                    [
                        'product' => $product->product_name,
                        'variant_id' => $variant->id,
                        'variant_option' => $variant->option,
                        'stock_before' => $oldStock,
                        'stock_after' => $validated['stock'],
                        'product_total_before' => $productTotalBefore,
                        'product_total_after' => $productTotalAfter,
                    ],
                    'Variant stock updated via Inventory Admin'
                );
            } catch (\Exception $e) {
                // Silently fail logging
            }

            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Variant '{$variant->option}' stock updated successfully.",
                    'variant_stock' => $validated['stock'],
                    'product_total_stock' => $productTotalAfter,
                ]);
            }

            return redirect()->route('inventory.admin.inventory.index')
                ->with('status', "Variant '{$variant->option}' stock updated successfully. Total stock: {$productTotalAfter}");
        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => $e->errors(),
                ], 422);
            }
            throw $e;
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error updating stock: ' . $e->getMessage(),
                ], 500);
            }
            return redirect()->route('inventory.admin.inventory.index')
                ->with('error', 'Error updating stock: ' . $e->getMessage());
        }
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
