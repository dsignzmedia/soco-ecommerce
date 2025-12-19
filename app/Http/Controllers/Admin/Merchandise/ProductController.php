<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Merchandise\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['school']);

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Apply shared filters
        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('stock_status')) {
             if ($request->stock_status === 'in_stock') {
                $query->where('inventory_stock', '>', 0);
            } else {
                $query->where('inventory_stock', '<=', 0);
            }
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mappings = $query->paginate(15)->withQueryString();
        
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        // Fetch distinct values for filters
        $grades = Product::whereNotNull('grade')->distinct()->pluck('grade', 'grade');
        $categories = Product::whereNotNull('category')->distinct()->pluck('category', 'category');
        $categories = Product::whereNotNull('category')->distinct()->pluck('category', 'category');
        $filters = $request->all();

        return view('admin.merchandise.products.index', compact('mappings', 'schools', 'grades', 'categories', 'filters'));
    }

    public function export(Request $request)
    {
        $type = $request->get('type', 'csv');
        $query = Product::with(['school']);

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->get();

        if ($type === 'csv') {
            $filename = 'products_merch_export_' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => "attachment; filename=\"$filename\"",
            ];
            
            $callback = function () use ($products) {
                $file = fopen('php://output', 'w');
                fputcsv($file, ['ID', 'Product Name', 'School', 'Grade', 'Category', 'Price', 'Sale Price', 'Stock', 'Status']);
                
                foreach ($products as $product) {
                    fputcsv($file, [
                        $product->id,
                        $product->product_name,
                        $product->school ? $product->school->name : 'N/A',
                        $product->grade,
                        $product->category,
                        $product->price_regular,
                        $product->price_sale,
                        $product->inventory_stock,
                        $product->status
                    ]);
                }
                fclose($file);
            };
            
            return response()->stream($callback, 200, $headers);
        }

        return redirect()->back()->with('error', 'Export type not supported yet.');
    }

    public function create(): View
    {
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        // Provide typical defaults for Merch products
        $grades = ['Pre-KG' => 'Pre-KG', 'LKG' => 'LKG', 'UKG' => 'UKG', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'];
        $categories = ['T-Shirts' => 'T-Shirts', 'Hoodies' => 'Hoodies', 'Caps' => 'Caps', 'Mugs' => 'Mugs', 'Accessories' => 'Accessories'];
        $productTypes = ['merchandised' => 'Merchandise'];

        $product = new Product();
        $product->gender = 'unisex';

        return view('admin.merchandise.products.form', [
            'product' => $product, 
            'mode' => 'create',
            'schools' => $schools,
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|string',
            'grade' => 'nullable|string',
            'school_id' => 'nullable|exists:schools,id',
            'gender' => 'nullable|string',
            'price_regular' => 'required|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'status' => 'required|in:live,draft',
            'description' => 'nullable|string',
        ]);

        $data['product_type'] = 'merchandised';
        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';
        // Merch might not have School ID, or it is null (Public/Custom)

        $product = Product::create($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id): View
    {
        $product = Product::findOrFail($id);
        $schools = \App\Models\Admin\Master\School::orderBy('name')->get();
        $grades = ['Pre-KG' => 'Pre-KG', 'LKG' => 'LKG', 'UKG' => 'UKG', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12'];
        $categories = ['T-Shirts' => 'T-Shirts', 'Hoodies' => 'Hoodies', 'Caps' => 'Caps', 'Mugs' => 'Mugs', 'Accessories' => 'Accessories'];
        $productTypes = ['merchandised' => 'Merchandise'];

        return view('admin.merchandise.products.form', [
            'product' => $product,
            'mode' => 'edit',
            'schools' => $schools,
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::findOrFail($id);

        $data = $request->validate([
            'product_name' => 'required|string|max:255',
            'category' => 'required|string',
            'grade' => 'nullable|string',
            'school_id' => 'nullable|exists:schools,id',
            'gender' => 'nullable|string',
            'price_regular' => 'required|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'product_type' => 'required|string',
            'status' => 'required|in:live,draft',
            'description' => 'nullable|string',
        ]);
        
        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';

        $product->update($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.merchandise.products.index')->with('success', 'Product deleted successfully.');
    }

    protected function saveVariants($product, $variants)
    {
        if (!is_array($variants)) return;

        $existingIds = $product->variants()->pluck('id')->toArray();
        $processedIds = [];

        foreach ($variants as $variantData) {
            if (empty($variantData['option'])) continue;

            $stock = isset($variantData['stock']) ? (int)$variantData['stock'] : 0;
            $lowStock = isset($variantData['low_stock_threshold']) ? (int)$variantData['low_stock_threshold'] : 5;

            if (isset($variantData['id']) && in_array($variantData['id'], $existingIds)) {
                ProductVariant::where('id', $variantData['id'])->update([
                    'option' => $variantData['option'],
                    'stock' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'name' => 'Size'
                ]);
                $processedIds[] = $variantData['id'];
            } else {
                $product->variants()->create([
                    'option' => $variantData['option'],
                    'stock' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'name' => 'Size'
                ]);
            }
        }

        $variantsToDelete = array_diff($existingIds, $processedIds);
        if (!empty($variantsToDelete)) {
            ProductVariant::destroy($variantsToDelete);
        }

        $product->updateTotalStock();
    }
}
