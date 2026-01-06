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
        $filters = $request->only(['school_id', 'category', 'status', 'q', 'product_type']);

        $products = ProductMapping::with(['school', 'variants'])
            ->when($filters['product_type'] ?? null, fn($q, $type) => $q->where('product_type', $type))
            ->when($filters['school_id'] ?? null, fn($q, $school) => $q->where('school_id', $school))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, fn($q, $term) => $q->where('product_name', 'like', '%'.$term.'%'))
            ->orderBy('product_name')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        
        // Get distinct product types
        $dbTypes = ProductMapping::select('product_type')
            ->whereNotNull('product_type')
            ->distinct()
            ->orderBy('product_type')
            ->pluck('product_type')
            ->toArray();
            
        // Ensure standard types are present
        $productTypes = array_unique(array_merge($dbTypes, ['back_to_school', 'merchandised']));
        sort($productTypes);

        return view('admin.inventory.list', compact('products', 'schools', 'categories', 'filters', 'productTypes'));
    }

    public function adjust(ProductMapping $product): View
    {
        $product->load('variants');
        $recentAdjustments = $product->inventoryAdjustments()->latest()->take(5)->get();
        $variantId = request()->get('variant_id');

        return view('admin.inventory.adjust', compact('product', 'recentAdjustments', 'variantId'));
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

            // Store old stock for audit
            $oldStock = $variant->stock;
            $productTotalBefore = $product->inventory_stock;
            
            // Update ONLY this variant's stock (doesn't affect other variants)
            $variant->update(['stock' => $validated['stock']]);
            
            // Recalculate and update product total stock from ALL variants
            // This ensures the total is always accurate
            $product->updateTotalStock();
            $productTotalAfter = $product->fresh()->inventory_stock;

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
                'Variant stock updated via inventory list'
            );

            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Variant '{$variant->option}' stock updated successfully.",
                    'variant_stock' => $validated['stock'],
                    'product_total_stock' => $productTotalAfter,
                ]);
            }

            // Regular redirect for non-AJAX requests
            return redirect()->route('master.admin.inventory.list')
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
            return redirect()->route('master.admin.inventory.list')
                ->with('error', 'Error updating stock: ' . $e->getMessage());
        }
    }

    public function reports(Request $request): View
    {
        $filters = $request->only(['school_id', 'category']);

        // Base query callback for reusable filtering
        $applyFilters = function ($query) use ($filters) {
            $query->when($filters['school_id'] ?? null, fn($q, $id) => $q->where('school_id', $id))
                  ->when($filters['category'] ?? null, fn($q, $cat) => $q->where('category', $cat));
        };

        // 1. Low Stock
        $lowStock = ProductMapping::whereColumn('inventory_stock', '<=', 'low_stock_threshold')
            ->tap($applyFilters)
            ->get();

        // 2. Out of Stock
        $outOfStock = ProductMapping::where('inventory_stock', '<=', 0)
            ->tap($applyFilters)
            ->get();

        // 3. Stock by School
        $stockBySchool = ProductMapping::selectRaw('school_id, SUM(inventory_stock) as total')
            ->tap($applyFilters)
            ->groupBy('school_id')
            ->with('school')
            ->get();

        // 4. Stock by Category
        $stockByCategory = ProductMapping::selectRaw('category, SUM(inventory_stock) as total')
            ->tap($applyFilters)
            ->groupBy('category')
            ->get();

        // 5. Aging Buckets
        $agingBuckets = [
            '0-30 days' => 0,
            '31-60 days' => 0,
            '61-90 days' => 0,
            '90+ days' => 0,
        ];

        ProductMapping::select('inventory_stock', 'updated_at')
            ->tap($applyFilters)
            ->each(function ($product) use (&$agingBuckets) {
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

        // 6. Movements
        // Movements are on InventoryAdjustment model. We need to filter based on related product.
        $movements = InventoryAdjustment::with('product')
            ->whereHas('product', function ($q) use ($filters) {
                $q->when($filters['school_id'] ?? null, fn($sq, $id) => $sq->where('school_id', $id))
                  ->when($filters['category'] ?? null, fn($sq, $cat) => $sq->where('category', $cat));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Data for Filter Dropdowns
        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.inventory.reports', compact('lowStock', 'outOfStock', 'stockBySchool', 'stockByCategory', 'agingBuckets', 'movements', 'schools', 'categories', 'filters'));
    }
}

