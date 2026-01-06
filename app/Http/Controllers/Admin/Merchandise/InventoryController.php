<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\Product;
use App\Models\Admin\Master\ProductMapping;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['category', 'status', 'q', 'product_type']);

        $query = Product::query()
            ->when($filters['product_type'] ?? null, fn($q, $type) => $q->where('product_type', $type))
            ->when($filters['category'] ?? null, fn($q, $category) => $q->where('category', $category))
            ->when($filters['status'] ?? null, fn($q, $status) => $q->where('status', $status))
            ->when($filters['q'] ?? null, fn($q, $term) => $q->where('product_name', 'like', '%'.$term.'%'));

        $products = $query->with('variants')->orderBy('product_name')->paginate(20)->withQueryString();

        $categories = ProductMapping::select('category')
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category');
        
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

        return view('admin.merchandise.inventory.index', compact('products', 'categories', 'filters', 'productTypes'));
    }

    public function update(Request $request, ProductMapping $product)
    {
        // Ensure we are only updating a merchandised product
        if ($product->product_type !== 'merchandised') {
             abort(403, 'This action is restricted to Merchandise products.');
        }

        $validated = $request->validate([
            'inventory_stock' => 'required|integer|min:0',
        ]);

        $product->update(['inventory_stock' => $validated['inventory_stock']]);

        return back()->with('success', 'Stock updated successfully.');
    }

    public function updateVariantStock(Request $request, ProductMapping $product)
    {
        try {
            // Ensure we are only updating a merchandised product
            if ($product->product_type !== 'merchandised') {
                abort(403, 'This action is restricted to Merchandise products.');
            }

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

            // Handle AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => "Variant '{$variant->option}' stock updated successfully.",
                    'variant_stock' => $validated['stock'],
                    'product_total_stock' => $productTotalAfter,
                ]);
            }

            return back()->with('success', "Variant '{$variant->option}' stock updated successfully. Total stock: {$productTotalAfter}");
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
            return back()->with('error', 'Error updating stock: ' . $e->getMessage());
        }
    }
}
