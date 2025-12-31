<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\BackToSchool\Product;
use App\Models\ProductVariant;
use App\Models\Admin\Master\School;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with(['school'])->withCount('variants');

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }
        
        if ($request->filled('school_id')) {
            $query->where('school_id', $request->school_id);
        }

        // Apply other filters if needed (grade, category, etc.)
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
        
        // Data for filters
        $schools = School::orderBy('name')->get();
        // Get distinct grades/categories from products for filtering options
        $grades = Product::whereNotNull('grade')->distinct()->pluck('grade', 'grade');
        $categories = Product::whereNotNull('category')->distinct()->pluck('category', 'category');
        $filters = $request->all();

        return view('admin.back_to_school.products.index', compact('mappings', 'schools', 'grades', 'categories', 'filters'));
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
            $filename = 'products_bts_export_' . date('Y-m-d') . '.csv';
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
        $schools = School::orderBy('name')->get();
        // Defaults for dropdowns if DB is empty, or fetch distinct
        $grades = [
            'Pre-KG' => 'Pre-KG',
            'LKG' => 'LKG',
            'UKG' => 'UKG',
            '1' => 'Class 1',
            '2' => 'Class 2',
            '3' => 'Class 3',
            '4' => 'Class 4',
            '5' => 'Class 5',
            '6' => 'Class 6',
            '7' => 'Class 7',
            '8' => 'Class 8',
            '9' => 'Class 9',
            '10' => 'Class 10',
            '11' => 'Class 11',
            '12' => 'Class 12',
        ];
        // Fetch categories from database, fallback to defaults if empty
        $categories = \App\Models\Admin\Master\Category::getForSelect();
        if (empty($categories)) {
            $categories = ['Uniform' => 'Uniform', 'Shoes' => 'Shoes', 'Bags' => 'Bags', 'Stationery' => 'Stationery', 'Food Container' => 'Food Container', 'Drinkware' => 'Drinkware', 'School-Day Essentials' => 'School-Day Essentials'];
        }
        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = ['back_to_school' => 'Back To School', 'merchandised' => 'Merchandise'];
        }

        $product = new Product();
        $product->product_type = 'back_to_school'; // Set default product type

        return view('admin.back_to_school.products.form', [
            'product' => $product, 
            'schools' => $schools, 
            'mode' => 'create',
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        // Check if variant-based pricing is enabled
        $variantBasedPricing = $request->has('variant_based_pricing') && $request->input('variant_based_pricing') == '1';
        
        // Simplified validation for School products
        $validationRules = [
            'product_name' => 'required|string|max:255',
            'school_id' => 'nullable|exists:schools,id', // Allow null for global products
            'grade' => 'nullable|string',
            'category' => 'nullable|string', // Made nullable
            'product_type' => 'nullable|string', 
            'gender' => 'nullable|string', // Made nullable
            'price_regular' => $variantBasedPricing ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'price_tax' => 'nullable|numeric|min:0',
            'tax_profile' => 'nullable|string',
            'price_inclusive_tax' => 'nullable|boolean',
            'product_weight' => 'nullable|numeric|min:0',
            'delivery_price' => 'nullable|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'status' => 'required|in:live,draft',
            'description' => 'nullable|string',
            'size_chart_path' => 'nullable|image',
            'size_measurement_image' => 'nullable|image',
            'video_url' => 'nullable|url',
            'tag_name' => 'nullable|string',
            'availability_label' => 'nullable|string',
            'delivery_duration' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image',
            'media_images' => 'nullable|array',
            'media_images.*' => [
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg,mov,avi,wmv,flv,mkv,m3u8',
                'max:20480' // 20MB for videos
            ],
            'variants' => 'nullable|array',
            'variants.*.option' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
        ];
        
        $data = $request->validate($validationRules);
        
        // Handle checkbox
        $data['price_inclusive_tax'] = $request->has('price_inclusive_tax') ? 1 : 0;
        
        // Validate variant prices when variant-based pricing is enabled
        if ($variantBasedPricing && $request->has('variants')) {
            $hasVariantPrice = false;
            $errors = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                if (!empty($variant['option'])) {
                    // If variant has an option, it must have a price
                    if (empty($variant['price']) || $variant['price'] <= 0) {
                        $errors["variants.{$index}.price"] = "Price is required for variant '{$variant['option']}' when variant-based pricing is enabled.";
                    } else {
                        $hasVariantPrice = true;
                    }
                }
            }
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }
            if (!$hasVariantPrice) {
                return redirect()->back()->withErrors(['variants' => 'At least one variant with an option must have a price when variant-based pricing is enabled.'])->withInput();
            }
        }
        
        // Handle file uploads
        if ($request->hasFile('size_chart_path')) {
             $data['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }
        if ($request->hasFile('size_measurement_image')) {
             $data['size_measurement_image'] = $request->file('size_measurement_image')->store('size_charts', 'public');
        }

        if (empty($data['product_type'])) {
            $data['product_type'] = 'back_to_school'; // Default
        }
        
        if (empty($data['gender'])) {
            $data['gender'] = 'unisex'; // Default
        }

        // Handle specific featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }

        // Handle media gallery
        if ($request->hasFile('media_images')) {
            $galleryPaths = [];
            foreach($request->file('media_images') as $image) {
                $path = $image->store('products', 'public');
                $galleryPaths[] = $path;
                
                // Set first image as featured if currently null
                if (empty($data['featured_image'])) {
                        $data['featured_image'] = $path;
                }
            }
            $data['media_gallery'] = $galleryPaths;
        }
        
        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';

        $product = Product::create($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        return redirect()->route('admin.back_to_school.products.index')->with('success', 'Product created successfully.');
    }

    public function edit($id): View
    {
        $product = Product::findOrFail($id);
        $schools = School::orderBy('name')->get();
        // Defaults or fetched
        $grades = [
            'Pre-KG' => 'Pre-KG',
            'LKG' => 'LKG',
            'UKG' => 'UKG',
            '1' => 'Class 1',
            '2' => 'Class 2',
            '3' => 'Class 3',
            '4' => 'Class 4',
            '5' => 'Class 5',
            '6' => 'Class 6',
            '7' => 'Class 7',
            '8' => 'Class 8',
            '9' => 'Class 9',
            '10' => 'Class 10',
            '11' => 'Class 11',
            '12' => 'Class 12',
        ];
        // Fetch categories from database, fallback to defaults if empty
        $categories = \App\Models\Admin\Master\Category::getForSelect();
        if (empty($categories)) {
            $categories = ['Uniform' => 'Uniform', 'Shoes' => 'Shoes', 'Bags' => 'Bags', 'Stationery' => 'Stationery', 'Food Container' => 'Food Container', 'Drinkware' => 'Drinkware', 'School-Day Essentials' => 'School-Day Essentials'];
        }
        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = ['back_to_school' => 'Back To School', 'merchandised' => 'Merchandise'];
        }

        return view('admin.back_to_school.products.form', [
            'product' => $product, 
            'schools' => $schools, 
            'mode' => 'edit',
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        
        // Check if variant-based pricing is enabled
        $variantBasedPricing = $request->has('variant_based_pricing') && $request->input('variant_based_pricing') == '1';

        $validationRules = [
            'product_name' => 'required|string|max:255',
            'school_id' => 'nullable|exists:schools,id', // Allow null
            'grade' => 'nullable|string',
            'category' => 'nullable|string', // Made nullable
            'product_type' => 'nullable|string', 
            'gender' => 'nullable|string', // Made nullable
            'price_regular' => $variantBasedPricing ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'price_tax' => 'nullable|numeric|min:0',
            'tax_profile' => 'nullable|string',
            'price_inclusive_tax' => 'nullable|boolean',
            'product_weight' => 'nullable|numeric|min:0',
            'delivery_price' => 'nullable|numeric|min:0',
            'inventory_stock' => 'required|integer|min:0',
            'status' => 'required|in:live,draft',
            'description' => 'nullable|string',
            'size_chart_path' => 'nullable|image',
            'video_url' => 'nullable|url',
            'tag_name' => 'nullable|string',
            'availability_label' => 'nullable|string',
            'delivery_duration' => 'nullable|string|max:255',
            'variants' => 'nullable|array',
            'variants.*.option' => 'nullable|string',
            'variants.*.price' => 'nullable|numeric|min:0',
            'variants.*.weight' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'nullable|integer|min:0',
            'variants.*.low_stock_threshold' => 'nullable|integer|min:0',
        ];
        
        $data = $request->validate($validationRules);
        
        // Handle checkbox
        $data['price_inclusive_tax'] = $request->has('price_inclusive_tax') ? 1 : 0;
        
        // Validate variant prices when variant-based pricing is enabled
        if ($variantBasedPricing && $request->has('variants')) {
            $hasVariantPrice = false;
            $errors = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                if (!empty($variant['option'])) {
                    // If variant has an option, it must have a price
                    if (empty($variant['price']) || $variant['price'] <= 0) {
                        $errors["variants.{$index}.price"] = "Price is required for variant '{$variant['option']}' when variant-based pricing is enabled.";
                    } else {
                        $hasVariantPrice = true;
                    }
                }
            }
            if (!empty($errors)) {
                return redirect()->back()->withErrors($errors)->withInput();
            }
            if (!$hasVariantPrice) {
                return redirect()->back()->withErrors(['variants' => 'At least one variant with an option must have a price when variant-based pricing is enabled.'])->withInput();
            }
        }
        
        // Handle file uploads
        if ($request->hasFile('size_chart_path')) {
             // Delete old? No helper yet, just store new
             $data['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }
        if ($request->hasFile('size_measurement_image')) {
             $data['size_measurement_image'] = $request->file('size_measurement_image')->store('size_charts', 'public');
        }
        
        if (empty($data['product_type'])) {
             // Keep existing type if not changed, or default
             // Don't overwrite if present
        }

        if (empty($data['gender'])) {
            $data['gender'] = 'unisex'; // Default
        }

        // Handle specific featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }
        
        // Handle featured image / media gallery
        if ($request->hasFile('media_images')) {
            // Eloquent casts 'media_gallery' to array automatically. Use it directly.
            $galleryPaths = $product->media_gallery ?? [];
            if (!is_array($galleryPaths)) {
                 $galleryPaths = []; // Safety check
            }
            
            foreach($request->file('media_images') as $image) {
                $path = $image->store('products', 'public');
                $galleryPaths[] = $path;
                
                // Set first image as featured if currently null
                if (empty($data['featured_image']) && empty($product->featured_image)) {
                     $data['featured_image'] = $path;
                }
            }
            $data['media_gallery'] = $galleryPaths; // Eloquent handles encoding
            
            // If featured_image was just set above, ensure it persists (it's in $data)
            // If not, and we have gallery but no featured, pick the first one
            if (empty($data['featured_image']) && empty($product->featured_image) && !empty($galleryPaths)) {
                $data['featured_image'] = $galleryPaths[0];
            }
        }
        
        // Handle specific featured image upload (overrides gallery logic if present)
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }

        $data['stock_status'] = $data['inventory_stock'] > 0 ? 'in_stock' : 'out_of_stock';

        $product->update($data);

        if ($request->has('variants')) {
            $this->saveVariants($product, $request->input('variants'));
        }

        return redirect()->route('admin.back_to_school.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()->route('admin.back_to_school.products.index')->with('success', 'Product deleted successfully.');
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
                    'price' => $variantData['price'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
                    'stock' => $stock,
                    'low_stock_threshold' => $lowStock,
                    'name' => 'Size'
                ]);
                $processedIds[] = $variantData['id'];
            } else {
                $product->variants()->create([
                    'option' => $variantData['option'],
                    'price' => $variantData['price'] ?? null,
                    'weight' => $variantData['weight'] ?? null,
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
