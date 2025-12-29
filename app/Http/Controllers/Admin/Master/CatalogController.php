<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Grade;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use App\Support\AuditLogger;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class CatalogController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->only(['school_id', 'grade_id', 'status', 'gender', 'category', 'product_type', 'stock_status', 'q']);

        $query = ProductMapping::with(['school', 'gradePricing', 'variants'])->withCount('variants');
        $this->applyFilters($query, $filters);

        if (! empty($filters['q'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('product_name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('product_type', 'like', '%' . $filters['q'] . '%');
            });
        }

        $mappings = $query->orderBy('product_name')
            ->paginate(10)
            ->withQueryString();

        $schools = School::orderBy('name')->get();
        
        // Hardcoded grades to match frontend profile creation
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
            $categories = [
                'regular_uniforms' => 'Regular Uniforms',
                'house_uniforms' => 'House Uniforms',
                'sports' => 'Sports Uniforms',
                'shoes' => 'Shoes',
                'belts' => 'Belts',
                'socks' => 'Socks',
                'ties' => 'Ties',
                'fabrics' => 'Fabrics',
            ];
        }

        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = [
                'authorized' => 'Authorized Product',
                'optional' => 'Optional Product',
                'merchandised' => 'Merchandised Product',
                'back_to_school' => 'Back to School Product',
            ];
        }

        return view('admin.catalog.index', [
            'mappings' => $mappings,
            'schools' => $schools,
            'grades' => $grades,
            'productTypes' => $productTypes,
            'categories' => $categories,
            'filters' => $filters,
        ]);
    }

    public function create(): View
    {
        return $this->formView(new ProductMapping(), 'create');
    }

    public function edit(ProductMapping $productMapping): View
    {
        // Load variants for editing
        $productMapping->load('variants');
        return $this->formView($productMapping, 'edit');
    }

    protected function formView(ProductMapping $product, string $mode): View
    {
        $schools = School::orderBy('name')->get();

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
            $categories = [
                'regular_uniforms' => 'Regular Uniforms',
                'house_uniforms' => 'House Uniforms',
                'sports' => 'Sports Uniforms',
                'shoes' => 'Shoes',
                'belts' => 'Belts',
                'socks' => 'Socks',
                'ties' => 'Ties',
                'fabrics' => 'Fabrics',
            ];
        }

        // Fetch product types from database, fallback to defaults if empty
        $productTypes = \App\Models\Admin\Master\ProductType::getForSelect();
        if (empty($productTypes)) {
            $productTypes = [
                'authorized' => 'Authorized Product',
                'optional' => 'Optional Product',
                'merchandised' => 'Merchandised Product',
                'back_to_school' => 'Back to School Product',
            ];
        }

        // Load grade pricing if editing
        if ($mode === 'edit') {
            $product->load('gradePricing');
        }

        return view('admin.catalog.form', [
            'product' => $product,
            'mode' => $mode,
            'schools' => $schools,
            'grades' => $grades,
            'categories' => $categories,
            'productTypes' => $productTypes,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $product = ProductMapping::create($data);

        // Handle Variants
        if ($request->has('variants')) {
            foreach ($request->variants as $variantData) {
                if (!empty($variantData['option'])) {
                    $product->variants()->create([
                        'name' => $variantData['name'] ?? 'Size',
                        'option' => $variantData['option'],
                        'price' => $variantData['price'] ?? null,
                        'weight' => $variantData['weight'] ?? null,
                        'stock' => $variantData['stock'] ?? 0,
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    ]);
                }
            }
            $product->updateTotalStock();
        }

        // Handle Grade Pricing (Range-based)
        if ($request->has('enable_grade_pricing') && $request->enable_grade_pricing && $request->has('grade_pricing_ranges')) {
            // Delete all existing grade pricing first
            $product->gradePricing()->delete();
            
            // Clear the grade field since we're using grade-wise pricing
            $product->grade = null;
            $product->save();
            
            // Get all grades in order
            $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            
            // Process each range
            foreach ($request->grade_pricing_ranges as $range) {
                if (!empty($range['from']) && !empty($range['price']) && is_numeric($range['price'])) {
                    // Validate: If "to" is provided, "from" must also be provided (already checked above)
                    if (!empty($range['to'])) {
                        // Range pricing: from-to
                        $fromIndex = array_search($range['from'], $gradeOrder);
                        $toIndex = array_search($range['to'], $gradeOrder);
                        
                        if ($fromIndex !== false && $toIndex !== false && $fromIndex <= $toIndex) {
                            // Create pricing for all grades in the range
                            for ($i = $fromIndex; $i <= $toIndex; $i++) {
                                $product->gradePricing()->updateOrCreate(
                                    ['grade' => $gradeOrder[$i]],
                                    ['price' => (float)$range['price']]
                                );
                            }
                        }
                    } else {
                        // Single grade pricing: only "from" provided
                        $product->gradePricing()->updateOrCreate(
                            ['grade' => $range['from']],
                            ['price' => (float)$range['price']]
                        );
                    }
                }
            }
        } else {
            // If grade pricing is disabled, delete all existing grade pricing
            $product->gradePricing()->delete();
        }

        return redirect()->route('master.admin.catalog.index')->with('status', 'Product created.');
    }



    public function update(Request $request, ProductMapping $productMapping): RedirectResponse
    {
        $originalPricing = $productMapping->only(['price_regular', 'price_sale', 'price_tax']);
        $data = $this->validatedData($request, $productMapping);
        
        $productMapping->update($data);

        // Handle Variants: Sync (Delete missing, update existing, create new)
        // Simplest approach: Delete all and recreate, or update by ID if provided.
        // For better UX, let's try to update if ID exists, create if not, delete if not in request.
        
        if ($request->has('variants')) {
            // Get IDs of variants present in the request
            $submittedIds = collect($request->variants)->pluck('id')->filter();
            
            // Delete variants not in the request
            $productMapping->variants()->whereNotIn('id', $submittedIds)->delete();

            foreach ($request->variants as $variantData) {
                if (!empty($variantData['option'])) {
                    if (!empty($variantData['id'])) {
                        $productMapping->variants()->where('id', $variantData['id'])->update([
                            'name' => $variantData['name'] ?? 'Size',
                            'option' => $variantData['option'],
                            'price' => $variantData['price'] ?? null,
                            'weight' => $variantData['weight'] ?? null,
                            'stock' => $variantData['stock'] ?? 0,
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                        ]);
                    } else {
                        // Create new
                        $productMapping->variants()->create([
                            'name' => $variantData['name'] ?? 'Size',
                            'option' => $variantData['option'],
                            'price' => $variantData['price'] ?? null,
                            'weight' => $variantData['weight'] ?? null,
                            'stock' => $variantData['stock'] ?? 0,
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                        ]);
                    }
                }
            }
            $productMapping->refresh()->updateTotalStock();
        } else {
             // If variants array is explicitly sent as empty, strictly it means delete all? 
             // Or maybe the form didn't send it? 
             // Let's assume if it's not present, we don't touch it, UNLESS we know the form always sends it.
             // We'll rely on the form sending 'variants' array even if empty or ensuring we check logic.
        }

        // Handle Grade Pricing (Range-based)
        if ($request->has('enable_grade_pricing') && $request->enable_grade_pricing && $request->has('grade_pricing_ranges')) {
            // Delete all existing grade pricing first
            $productMapping->gradePricing()->delete();
            
            // Clear the grade field since we're using grade-wise pricing
            $productMapping->grade = null;
            $productMapping->save();
            
            // Get all grades in order
            $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            
            // Process each range
            foreach ($request->grade_pricing_ranges as $range) {
                if (!empty($range['from']) && !empty($range['price']) && is_numeric($range['price'])) {
                    if (!empty($range['to'])) {
                        // Range pricing: from-to
                        $fromIndex = array_search($range['from'], $gradeOrder);
                        $toIndex = array_search($range['to'], $gradeOrder);
                        
                        if ($fromIndex !== false && $toIndex !== false && $fromIndex <= $toIndex) {
                            // Create pricing for all grades in the range
                            for ($i = $fromIndex; $i <= $toIndex; $i++) {
                                $productMapping->gradePricing()->updateOrCreate(
                                    ['grade' => $gradeOrder[$i]],
                                    ['price' => (float)$range['price']]
                                );
                            }
                        }
                    } else {
                        // Single grade pricing: only "from" provided
                        $productMapping->gradePricing()->updateOrCreate(
                            ['grade' => $range['from']],
                            ['price' => (float)$range['price']]
                        );
                    }
                }
            }
        } else {
            // If grade pricing is disabled, delete all existing grade pricing
            $productMapping->gradePricing()->delete();
        }

        $priceFields = ['price_regular', 'price_sale', 'price_tax'];
        $changes = [];
        foreach ($priceFields as $field) {
            $before = $originalPricing[$field] ?? null;
            $after = $productMapping->{$field};
            if ($before != $after) {
                $changes[$field] = [
                    'before' => $before,
                    'after' => $after,
                ];
            }
        }

        if (! empty($changes)) {
            AuditLogger::record(
                'price_change',
                $productMapping,
                [
                    'product' => $productMapping->product_name,
                    'changes' => $changes,
                ],
                'Product pricing updated'
            );
        }

        return redirect()->route('master.admin.catalog.index')->with('status', 'Product updated.');
    }

    public function show(ProductMapping $productMapping): View
    {
        $productMapping->load(['school', 'grade', 'variants']);
        return view('admin.catalog.show', ['product' => $productMapping]);
    }

    public function destroy(ProductMapping $productMapping): RedirectResponse
    {
        $productMapping->delete();

        return redirect()->route('master.admin.catalog.index')->with('status', 'Product deleted.');
    }

    public function export(Request $request, string $type)
    {
        $filters = $request->only(['school_id', 'grade_id', 'status', 'gender', 'category', 'product_type', 'stock_status', 'q']);

        $query = ProductMapping::with(['school']);
        $this->applyFilters($query, $filters);

        if (! empty($filters['q'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('product_name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('product_type', 'like', '%' . $filters['q'] . '%');
            });
        }

        $products = $query->orderBy('product_name')->get();

        switch (strtolower($type)) {
            case 'csv':
                return $this->downloadDelimited($products, ',', 'catalog-export-' . date('Y-m-d') . '.csv', 'text/csv');
            
            case 'excel':
                return $this->downloadDelimited($products, "\t", 'catalog-export-' . date('Y-m-d') . '.xls', 'application/vnd.ms-excel');
            
            case 'pdf':
                return $this->downloadPdf($products);
            
            default:
                return redirect()->route('master.admin.catalog.index')
                    ->with('error', 'Invalid export type. Supported types: csv, excel, pdf.');
        }
    }

    protected function validatedData(Request $request, ?ProductMapping $product = null): array
    {
        // Check if variant-based pricing is enabled
        $variantBasedPricing = $request->has('variant_based_pricing') && $request->input('variant_based_pricing') == '1';
        
        $rules = [
            'school_id' => ['required', 'exists:schools,id'],
            'grade' => ['nullable', 'string'],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,unisex'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock'],
            'availability_label' => ['nullable', 'string', 'max:255'],
            'delivery_duration' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:live,draft,archived'],
            'description' => ['nullable', 'string'],
            'size_guidance' => ['nullable', 'string'],
            'price_regular' => ['nullable', 'numeric', 'min:0'],
            'price_tax' => ['nullable', 'numeric', 'min:0'],
            'tax_profile' => ['nullable', 'string', 'max:255'],
            'price_inclusive_tax' => ['nullable', 'boolean'],
            'product_weight' => ['nullable', 'numeric', 'min:0'],
            'tag_name' => ['nullable', 'string', 'max:255'],
            'inventory_stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'media_gallery' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'variants' => ['nullable', 'array'],
            'variants.*.option' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.weight' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
            'existing_media_images' => ['nullable', 'array'],
            'media_order_ids' => ['nullable', 'string'],
        ];

        // Add file validation rules
        if ($request->hasFile('featured_image')) {
            $rules['featured_image'] = ['image', 'max:2048']; 
        } else {
            $rules['featured_image'] = ['nullable', 'string', 'max:2048'];
        }
        
        if ($request->hasFile('size_chart_path')) {
            $rules['size_chart_path'] = ['image', 'max:2048']; 
        } else {
            $rules['size_chart_path'] = ['nullable', 'string', 'max:2048'];
        }
        
        if ($request->hasFile('size_measurement_image')) {
            $rules['size_measurement_image'] = ['image', 'max:2048']; 
        } else {
            $rules['size_measurement_image'] = ['nullable', 'string', 'max:2048'];
        }

        if ($request->hasFile('media_images')) {
            $rules['media_images.*'] = [
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg,mov,avi,wmv,flv,mkv,m3u8',
                'max:20480' // 20MB for videos (increased from 2MB)
            ];
        }

        $validated = $request->validate($rules);
        
        // Validate: Either price_regular OR grade pricing must be provided
        $hasGradePricing = false;
        $gradePricingErrors = [];
        
        if ($request->has('enable_grade_pricing') && $request->enable_grade_pricing && $request->has('grade_pricing_ranges')) {
            // Clear the grade field when grade-wise pricing is enabled
            $validated['grade'] = null;
            
            foreach ($request->grade_pricing_ranges as $index => $range) {
                // Validate: If "to" is provided, "from" must also be provided
                if (!empty($range['to']) && empty($range['from'])) {
                    $gradePricingErrors["grade_pricing_ranges.{$index}.from"] = ['From Grade is required when To Grade is provided.'];
                }
                
                // Check if valid grade pricing exists (from is required, to is optional)
                if (!empty($range['from']) && !empty($range['price']) && is_numeric($range['price'])) {
                    $hasGradePricing = true;
                }
            }
        }
        
        if (!empty($gradePricingErrors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($gradePricingErrors);
        }
        
        $hasRegularPrice = !empty($validated['price_regular']);
        
        // Check for variant prices when variant-based pricing is enabled
        // Note: If grade pricing is also enabled, variant prices might be hidden/not required
        $hasVariantPrice = false;
        if ($variantBasedPricing && $request->has('variants')) {
            $variants = $request->input('variants', []);
            foreach ($variants as $index => $variant) {
                // Check if variant has a valid price (can be string or numeric, but must be > 0)
                $variantPrice = $variant['price'] ?? null;
                if (!empty($variant['option']) && $variantPrice !== null && $variantPrice !== '') {
                    // Convert to float to handle string numbers (including "0.00", "0", etc.)
                    $priceValue = is_numeric($variantPrice) ? (float)$variantPrice : 0;
                    if ($priceValue > 0) {
                        $hasVariantPrice = true;
                        break; // At least one variant has a price
                    }
                }
            }
        }
        
        // Validate: Either regular price, grade pricing, OR variant prices must be provided
        // Special case: If both variant pricing AND grade pricing are enabled, grade pricing takes precedence
        // So we only require variant prices if variant pricing is enabled AND grade pricing is NOT enabled
        $requireVariantPrice = $variantBasedPricing && !$hasGradePricing;
        
        if (!$hasRegularPrice && !$hasGradePricing && !$hasVariantPrice) {
            // If variant-based pricing is enabled but no variant prices found, give a more specific error
            if ($variantBasedPricing && !$hasGradePricing) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => ['Variant-based pricing is enabled, but no variant prices were provided. Please add prices for at least one variant in the "Price of Fabric" field.']
                ]);
            } else {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'price_regular' => ['Either regular price or grade-wise pricing must be provided.']
                ]);
            }
        }
        
        // Handle checkbox
        $validated['price_inclusive_tax'] = $request->has('price_inclusive_tax') ? 1 : 0;
        
        // Validate variant prices when variant-based pricing is enabled (detailed validation)
        if ($variantBasedPricing && $request->has('variants')) {
            $errors = [];
            foreach ($request->input('variants', []) as $index => $variant) {
                if (!empty($variant['option'])) {
                    // If variant has an option, it must have a price
                    if (empty($variant['price']) || $variant['price'] <= 0) {
                        $errors["variants.{$index}.price"] = "Price is required for variant '{$variant['option']}' when variant-based pricing is enabled.";
                    }
                }
            }
            if (!empty($errors)) {
                throw \Illuminate\Validation\ValidationException::withMessages($errors);
            }
            // Only require variant prices if grade pricing is NOT enabled
            // If grade pricing is enabled, it takes precedence and variant prices are not required
            if (!$hasVariantPrice && !$hasGradePricing) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'variants' => ['At least one variant with an option must have a price when variant-based pricing is enabled (and grade pricing is disabled).']
                ]);
            }
        }

        // Handle Featured Image
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }
        
        // Handle Size Chart
        if ($request->hasFile('size_chart_path')) {
            $validated['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }
        
        // Handle Size Measurement Image
        if ($request->hasFile('size_measurement_image')) {
            $validated['size_measurement_image'] = $request->file('size_measurement_image')->store('size_charts', 'public');
        }

        // Handle Gallery Images
        $paths = [];
        if ($request->hasFile('media_images')) {
            foreach ($request->file('media_images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
        }

        // Logic: If 'existing_media_images' is present (even empty), we use it as the base.
        // This supports reordering and deletion. 
        // If NOT present (e.g., API usage or legacy), we append new files to existing DB images.
        // Reconstruction Logic
        if ($request->filled('media_order_ids')) {
            $existing = $request->input('existing_media_images', []);
            $orderMap = explode(',', $request->input('media_order_ids'));
            $finalImages = [];
            
            $existingIndex = 0;
            $newIndex = 0;

            foreach ($orderMap as $type) {
                if ($type === 'existing') {
                    if (isset($existing[$existingIndex])) {
                        $finalImages[] = $existing[$existingIndex];
                        $existingIndex++;
                    }
                } elseif ($type === 'new') {
                    if (isset($paths[$newIndex])) {
                        $finalImages[] = $paths[$newIndex];
                        $newIndex++;
                    }
                }
            }
            
            // Safety: Append any remaining (shouldn't happen if map is accurate, but just in case)
            while(isset($existing[$existingIndex])) {
                $finalImages[] = $existing[$existingIndex++];
            }
            while(isset($paths[$newIndex])) {
                $finalImages[] = $paths[$newIndex++];
            }

            $validated['media_images'] = $finalImages;

            \Illuminate\Support\Facades\Log::info('Reconstructed Media Structure:', ['order' => $orderMap, 'final' => $finalImages]);

        } elseif ($request->exists('existing_media_images') || $request->has('media_list_modified')) {
            $existing = $request->input('existing_media_images', []);
            \Illuminate\Support\Facades\Log::info('Existing Media Images Input Order:', ['existing' => $existing]);
            
            $validated['media_images'] = array_merge($existing, $paths);
            
            \Illuminate\Support\Facades\Log::info('Final Media Images to Save:', ['media_images' => $validated['media_images']]);
        } elseif (!empty($paths)) {
            // Fallback: Append only mode
            if ($product && $product->media_images) {
                $validated['media_images'] = array_merge($product->media_images, $paths);
            } else {
                $validated['media_images'] = $paths;
            }
        }
        
        // Handle legacy gallery string
        $validated['media_gallery'] = $this->stringToArray($validated['media_gallery'] ?? '');

        return $validated;
    }
    
    protected function stringToArray(string $input): array
    {
        if (empty($input)) {
            return [];
        }

        return array_map('trim', explode(',', $input));
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        if (! empty($filters['school_id'])) {
            $query->where('school_id', $filters['school_id']);
        }

        if (! empty($filters['grade_id'])) {
            // Check if it's searching by ID or name (since grade searches might be mixed)
            // Assuming grade is stored as string in product_mappings, matching 'grade' column
            $query->where('grade', $filters['grade_id']);
        }

        if(! empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if(! empty($filters['product_type'])) {
            $query->where('product_type', $filters['product_type']);
        }

        if(! empty($filters['gender'])) {
            $query->where('gender', $filters['gender']);
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['stock_status'])) {
            $query->where('stock_status', $filters['stock_status']);
        }
    }

    protected function downloadDelimited(Collection $products, string $delimiter, string $filename, string $contentType)
    {
        $headers = [
            'Content-Type' => $contentType,
        ];

        return response()->streamDownload(function () use ($products, $delimiter) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Product', 'School', 'Grade', 'Category', 'Product Type', 'Gender', 'Regular', 'Sale', 'Tax', 'Tax Profile', 'Weight', 'Stock', 'Low Stock', 'Status', 'Stock Status'], $delimiter);
            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->product_name,
                    optional($product->school)->name,
                    $product->grade ?? 'All grades',
                    $product->gender,
                    $product->category,
                    $product->product_type,
                    $product->price_regular,
                    $product->price_sale,
                    $product->price_tax,
                    $product->tax_profile,
                    $product->product_weight,
                    $product->inventory_stock,
                    $product->low_stock_threshold,
                    $product->status,
                    $product->stock_status,
                ], $delimiter);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    protected function downloadPdf(Collection $products)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.catalog.export-pdf', ['products' => $products]);
        return $pdf->download('catalog-export.pdf');
    }
}

