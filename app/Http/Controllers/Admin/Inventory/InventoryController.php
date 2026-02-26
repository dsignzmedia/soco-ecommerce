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
        $filters = $request->only(['school_id', 'category', 'status', 'q', 'product_type']);

        $products = ProductMapping::with(['school', 'variants'])
            ->when($filters['school_id'] ?? null, fn($q, $school) => $q->whereIn('school_id', (array)$school))
            ->when($filters['product_type'] ?? null, fn($q, $type) => $q->whereIn('product_type', (array)$type))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->whereIn('category', (array)$category))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->whereIn('status', (array)$status))
            ->when($filters['q'] ?? null, fn($q, $term) => $q->where('product_name', 'like', '%'.$term.'%'))
            ->orderBy('product_name')
            ->paginate(15)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        
        // Get distinct product types from DB
        $dbTypes = ProductMapping::select('product_type')
            ->whereNotNull('product_type')
            ->distinct()
            ->orderBy('product_type')
            ->pluck('product_type')
            ->toArray();
            
        // Ensure standard types are present
        $productTypes = array_unique(array_merge($dbTypes, ['back_to_school', 'merchandised']));
        sort($productTypes);

        return view('inventoryadmin.inventory.index', compact('products', 'schools', 'categories', 'filters', 'productTypes'));
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

    public function reports(Request $request)
    {
        $filters = $request->only(['school_id', 'grade_id', 'category', 'stock_status', 'date_from', 'date_to', 'q']);

        // Build base query with filters
        $baseQuery = ProductMapping::with(['school', 'grade'])
            ->when($filters['school_id'] ?? null, fn($q, $school) => $q->where('school_id', $school))
            ->when($filters['grade_id'] ?? null, fn($q, $grade) => $q->where('grade_id', $grade))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
            ->when($filters['q'] ?? null, fn($q, $term) => $q->where('product_name', 'like', '%'.$term.'%'))
            ->when($filters['date_from'] ?? null, fn($q, $from) => $q->whereDate('updated_at', '>=', $from))
            ->when($filters['date_to'] ?? null, fn($q, $to) => $q->whereDate('updated_at', '<=', $to));

        // Apply stock status filter
        if (isset($filters['stock_status'])) {
            match($filters['stock_status']) {
                'in_stock' => $baseQuery->where('inventory_stock', '>', 0),
                'low_stock' => $baseQuery->whereColumn('inventory_stock', '<=', 'low_stock_threshold')->where('inventory_stock', '>', 0),
                'out_of_stock' => $baseQuery->where('inventory_stock', '<=', 0),
                'critical' => $baseQuery->whereRaw('inventory_stock <= (low_stock_threshold / 2)')->where('inventory_stock', '>', 0),
                default => null
            };
        }

        // Handle Export Request
        if ($request->has('export')) {
            $exportProducts = (clone $baseQuery)->get();
            $filename = 'inventory_report_' . date('Y-m-d_H-i');

            if ($request->export === 'csv') {
                $headers = [
                    "Content-type" => "text/csv",
                    "Content-Disposition" => "attachment; filename=$filename.csv",
                    "Pragma" => "no-cache",
                    "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
                    "Expires" => "0"
                ];

                $columns = ['Product Name', 'School', 'Grade', 'Category', 'Stock', 'Threshold', 'Last Updated', 'Status'];

                $callback = function() use ($exportProducts, $columns) {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, $columns);
                    
                    foreach ($exportProducts as $product) {
                        $status = 'In Stock';
                        if ($product->inventory_stock <= 0) $status = 'Out of Stock';
                        elseif ($product->inventory_stock <= $product->low_stock_threshold) $status = 'Low Stock';

                        fputcsv($file, [
                            $product->product_name,
                            $product->school?->name ?? '—',
                            $product->grade?->name ?? '—',
                            $product->category ?? '—',
                            $product->inventory_stock,
                            $product->low_stock_threshold,
                            $product->updated_at->format('d M Y'),
                            $status
                        ]);
                    }
                    fclose($file);
                };
                return response()->stream($callback, 200, $headers);
            }

            if ($request->export === 'pdf') {
                $pdf = \PDF::loadView('inventoryadmin.reports.pdf', compact('exportProducts', 'filters'));
                return $pdf->download("$filename.pdf");
            }
        }

        // Calculate comprehensive metrics
        $totalStock = (clone $baseQuery)->sum('inventory_stock');
        $totalProducts = (clone $baseQuery)->count();
        $totalValue = 0; // Price column not available in product_mappings
        $avgStock = $totalProducts > 0 ? round($totalStock / $totalProducts, 2) : 0;

        // Stock status counts
        $inStockCount = (clone $baseQuery)->where('inventory_stock', '>', 0)->count();
        $lowStock = (clone $baseQuery)->with('school')->whereColumn('inventory_stock', '<=', 'low_stock_threshold')->where('inventory_stock', '>', 0)->get();
        $outOfStock = (clone $baseQuery)->with('school')->where('inventory_stock', '<=', 0)->get();
        $criticalStock = (clone $baseQuery)->whereRaw('inventory_stock <= (low_stock_threshold / 2)')->where('inventory_stock', '>', 0)->count();

        // Stock by School
        $stockBySchool = (clone $baseQuery)
            ->selectRaw('school_id, SUM(inventory_stock) as total, COUNT(*) as product_count')
            ->groupBy('school_id')
            ->with('school')
            ->get();

        // Stock by Category
        $stockByCategory = (clone $baseQuery)
            ->selectRaw('category, SUM(inventory_stock) as total, COUNT(*) as product_count')
            ->whereNotNull('category')
            ->groupBy('category')
            ->get();

        // Stock by Grade
        $stockByGrade = (clone $baseQuery)
            ->selectRaw('grade_id, SUM(inventory_stock) as total, COUNT(*) as product_count')
            ->groupBy('grade_id')
            ->with('grade')
            ->get();

        // Stock Aging Analysis (products not updated in 30+ days)
        $stockAging = (clone $baseQuery)
            ->select('product_name', 'inventory_stock', 'updated_at', 'category', 'school_id')
            ->with('school')
            ->whereDate('updated_at', '<=', now()->subDays(30))
            ->orderBy('updated_at', 'asc')
            ->limit(20)
            ->get()
            ->map(function($product) {
                return [
                    'name' => $product->product_name,
                    'stock' => $product->inventory_stock,
                    'days_old' => $product->updated_at ? floor($product->updated_at->diffInDays(now())) : 0,
                    'category' => $product->category,
                    'school' => $product->school?->name ?? 'Unknown'
                ];
            });

        // Detailed product list with pagination
        $products = (clone $baseQuery)
            ->select('id', 'product_name', 'school_id', 'grade_id', 'category', 'inventory_stock', 'low_stock_threshold', 'updated_at', 'featured_image')
            ->with('variants')
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        // Get filter dropdown data
        $schools = School::orderBy('name')->get();
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');
        $grades = \App\Models\Admin\Master\Grade::orderBy('name')->get();

        return view('inventoryadmin.reports.index', compact(
            'filters',
            'totalStock',
            'totalProducts',
            'totalValue',
            'avgStock',
            'inStockCount',
            'lowStock',
            'outOfStock',
            'criticalStock',
            'stockBySchool',
            'stockByCategory',
            'stockByGrade',
            'stockAging',
            'products',
            'schools',
            'categories',
            'grades'
        ));
    }
}
