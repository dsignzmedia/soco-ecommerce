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
        
        // Exclude BTS and Merchandise products from Master Admin catalog
        // Master Admin should only see school-specific catalog products
        $query->where(function($q) {
            $q->whereNotIn('product_type', ['back_to_school', 'merchandised'])
              ->orWhereNull('product_type'); // Include legacy products without product_type
        });
        
        $this->applyFilters($query, $filters);

        if (! empty($filters['q'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('product_name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('product_type', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['q'] . '%');
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

        // Remove BTS and Merchandise from product types filter in Master Admin
        // Master Admin should only manage school-specific catalog products
        unset($productTypes['merchandised']);
        unset($productTypes['back_to_school']);

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

        // Hide specific types in Master Admin
        unset($productTypes['merchandised']);
        unset($productTypes['back_to_school']);

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
        
        // Handle video file upload (independent of video_url)
        if ($request->hasFile('video_file')) {
            $data['video_file'] = $request->file('video_file')->store('videos', 'public');
        }
        
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

        $query = ProductMapping::with(['school', 'gradePricing', 'variants'])->withCount('variants');
        
        // Exclude BTS and Merchandise products from Master Admin exports
        // Master Admin should only export school-specific catalog products
        $query->where(function($q) {
            $q->whereNotIn('product_type', ['back_to_school', 'merchandised'])
              ->orWhereNull('product_type'); // Include legacy products without product_type
        });
        
        $this->applyFilters($query, $filters);

        if (! empty($filters['q'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('product_name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('product_type', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('sku', 'like', '%' . $filters['q'] . '%');
            });
        }

        $products = $query->orderBy('product_name')->get();
        
        // Filter out any empty/null products to ensure no blank rows
        $products = $products->filter(function($product) {
            return $product !== null && !empty($product->product_name);
        })->values(); // Reindex the collection to ensure continuous indices

        switch (strtolower($type)) {
            case 'csv':
                return $this->downloadDelimited($products, ',', 'catalog-export-' . date('Y-m-d') . '.csv', 'text/csv');
            
            case 'excel':
                return $this->downloadExcelFormatted($products);
            
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
            'video_file' => ['nullable', 'file', 'mimes:mp4,webm,ogg,mov,avi,wmv,flv,mkv', 'max:102400'], // 100MB max
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
            $rules['featured_image'] = [
                'image',
                function ($attribute, $value, $fail) {
                    if ($value && $value->getSize() < 1024) { // 1 KB = 1024 bytes
                        $fail('The featured image must be at least 1 KB in size.');
                    }
                }
            ]; 
        } else {
            $rules['featured_image'] = ['nullable', 'string', 'max:200'];
        }
        
        if ($request->hasFile('size_chart_path')) {
            $rules['size_chart_path'] = ['image']; 
        } else {
            $rules['size_chart_path'] = ['nullable', 'string', 'max:2048'];
        }
        
        if ($request->hasFile('size_measurement_image')) {
            $rules['size_measurement_image'] = ['image']; 
        } else {
            $rules['size_measurement_image'] = ['nullable', 'string', 'max:2048'];
        }
        if ($request->hasFile('video_file')) {
            $rules['video_file'] = ['file', 'mimes:mp4,webm,ogg,mov,avi,wmv,flv,mkv', 'max:102400']; // 100MB max
        } else {
            $rules['video_file'] = ['nullable'];
        }

        if ($request->hasFile('media_images')) {
            $rules['media_images.*'] = [
                'file',
                'mimes:jpeg,jpg,png,gif,webp,mp4,webm,ogg,mov,avi,wmv,flv,mkv,m3u8',
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
        // Handle video file upload (independent of video_url)
        if ($request->hasFile('video_file')) {
            // Delete old video file if exists (only in update mode)
            if ($product && $product->video_file && \Storage::disk('public')->exists($product->video_file)) {
                \Storage::disk('public')->delete($product->video_file);
            }
            $validated['video_file'] = $request->file('video_file')->store('videos', 'public');
        }
        
        // Handle video file removal
        if ($request->has('remove_video_file') && $request->input('remove_video_file') == '1') {
            if ($product && $product->video_file && \Storage::disk('public')->exists($product->video_file)) {
                \Storage::disk('public')->delete($product->video_file);
            }
            $validated['video_file'] = null;
        }
        
        // Handle video URL removal (independent of video_file)
        if ($request->has('remove_video_url') && $request->input('remove_video_url') == '1') {
            $validated['video_url'] = null;
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

        // Helper function to format grades
        $formatGrades = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $grades = $product->gradePricing->pluck('grade')->toArray();
                
                if (empty($grades)) {
                    return $product->grade ?? 'All';
                }
                
                usort($grades, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a, $gradeOrder);
                    $bIndex = array_search($b, $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                $displayGrades = [];
                $currentRange = null;
                foreach ($grades as $i => $grade) {
                    $gradeIndex = array_search($grade, $gradeOrder);
                    if ($i === 0) {
                        $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                    } else {
                        $prevIndex = array_search($grades[$i-1], $gradeOrder);
                        if ($gradeIndex === $prevIndex + 1) {
                            $currentRange['end'] = $grade;
                        } else {
                            if ($currentRange['start'] === $currentRange['end']) {
                                $displayGrades[] = $currentRange['start'];
                            } else {
                                $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                            }
                            $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                        }
                    }
                }
                if ($currentRange) {
                    if ($currentRange['start'] === $currentRange['end']) {
                        $displayGrades[] = $currentRange['start'];
                    } else {
                        $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                    }
                }
                return implode(', ', $displayGrades);
            } else {
                return $product->grade ?? 'All';
            }
        };

        // Helper function to format grade pricing with ranges
        $formatGradePricing = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                
                // Group by price
                $pricingByPrice = [];
                foreach ($product->gradePricing as $gp) {
                    if (!isset($pricingByPrice[$gp->price])) {
                        $pricingByPrice[$gp->price] = [];
                    }
                    $pricingByPrice[$gp->price][] = $gp->grade;
                }
                
                $ranges = [];
                foreach ($pricingByPrice as $price => $gradeList) {
                    // Sort by grade order
                    usort($gradeList, function($a, $b) use ($gradeOrder) {
                        $aIndex = array_search($a, $gradeOrder);
                        $bIndex = array_search($b, $gradeOrder);
                        if ($aIndex === false) $aIndex = 999;
                        if ($bIndex === false) $bIndex = 999;
                        return $aIndex <=> $bIndex;
                    });
                    
                    // Group consecutive grades into ranges
                    $currentRange = ['from' => $gradeList[0], 'to' => $gradeList[0]];
                    foreach ($gradeList as $i => $grade) {
                        if ($i === 0) continue;
                        $prevIndex = array_search($gradeList[$i-1], $gradeOrder);
                        $currIndex = array_search($grade, $gradeOrder);
                        if ($currIndex === $prevIndex + 1) {
                            // Consecutive, extend range
                            $currentRange['to'] = $grade;
                        } else {
                            // Not consecutive, save current range and start new
                            $rangeStr = $currentRange['from'] === $currentRange['to'] 
                                ? $currentRange['from'] 
                                : $currentRange['from'] . '-' . $currentRange['to'];
                            $ranges[] = $rangeStr . ': Rs.' . number_format($price, 2);
                            $currentRange = ['from' => $grade, 'to' => $grade];
                        }
                    }
                    // Add last range for this price
                    $rangeStr = $currentRange['from'] === $currentRange['to'] 
                        ? $currentRange['from'] 
                        : $currentRange['from'] . '-' . $currentRange['to'];
                    $ranges[] = $rangeStr . ': Rs.' . number_format($price, 2);
                }
                
                // For Excel/CSV, use semicolon separator for better readability
                // Format: "Grade1-5:Rs.1000; Grade6-12:Rs.1200"
                return implode('; ', $ranges);
            }
            // If no grade pricing, show all grades with regular price
            $regularPrice = $product->price_regular ?? 0;
            if ($regularPrice > 0) {
                return 'Pre-KG–12: Rs.' . number_format($regularPrice, 2);
            }
            return 'N/A';
        };

        return response()->streamDownload(function () use ($products, $delimiter, $formatGrades, $formatGradePricing) {
            $handle = fopen('php://output', 'w');
            // Fixed column order to match data order - added Grade Pricing column
            // Helper function to format variants
            $formatVariants = function($product) {
                if ($product->variants && $product->variants->count() > 0) {
                    $variantLines = [];
                    foreach ($product->variants as $variant) {
                        $weight = $variant->weight ? number_format($variant->weight, 2) : 'N/A';
                        $stock = $variant->stock ?? 0;
                        $variantLines[] = $variant->option . ' | Weight: ' . $weight . ' | Stock: ' . $stock;
                    }
                    return implode('; ', $variantLines);
                }
                return 'N/A';
            };

            fputcsv($handle, ['Product', 'School', 'Grade', 'Category', 'Product Type', 'Gender', 'Regular', 'Tax', 'Tax Profile', 'Weight', 'Grade Pricing', 'Variants', 'Stock', 'Low Stock', 'Status', 'Stock Status'], $delimiter);
            foreach ($products as $product) {
                fputcsv($handle, [
                    $product->product_name,
                    optional($product->school)->name ?? 'N/A',
                    $formatGrades($product),
                    $product->category ?? 'N/A',
                    $product->product_type ?? 'N/A',
                    ucfirst($product->gender ?? 'N/A'),
                    $product->price_regular ?? '0.00',
                    $product->price_tax ?? '0.00',
                    $product->tax_profile ?? 'N/A',
                    $product->product_weight ?? '0.00',
                    $formatGradePricing($product),
                    $formatVariants($product),
                    $product->inventory_stock ?? 0,
                    $product->low_stock_threshold ?? 0,
                    ucfirst($product->status ?? 'N/A'),
                    ucfirst($product->stock_status ?? 'N/A'),
                ], $delimiter);
            }
            fclose($handle);
        }, $filename, $headers);
    }

    protected function downloadExcelFormatted(Collection $products)
    {
        // Helper functions
        $formatGrades = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $grades = $product->gradePricing->pluck('grade')->toArray();
                
                if (empty($grades)) {
                    return $product->grade ?? 'All';
                }
                
                usort($grades, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a, $gradeOrder);
                    $bIndex = array_search($b, $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                $displayGrades = [];
                $currentRange = null;
                foreach ($grades as $i => $grade) {
                    $gradeIndex = array_search($grade, $gradeOrder);
                    if ($i === 0) {
                        $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                    } else {
                        $prevIndex = array_search($grades[$i-1], $gradeOrder);
                        if ($gradeIndex === $prevIndex + 1) {
                            $currentRange['end'] = $grade;
                        } else {
                            if ($currentRange['start'] === $currentRange['end']) {
                                $displayGrades[] = $currentRange['start'];
                            } else {
                                $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                            }
                            $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                        }
                    }
                }
                if ($currentRange) {
                    if ($currentRange['start'] === $currentRange['end']) {
                        $displayGrades[] = $currentRange['start'];
                    } else {
                        $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                    }
                }
                return implode(', ', $displayGrades);
            } else {
                return $product->grade ?? 'All';
            }
        };

        // Format grade pricing as line-by-line structured format with HTML formatting
        $formatGradePricing = function($product) {
            if ($product->gradePricing && $product->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                
                // Group by price
                $pricingByPrice = [];
                foreach ($product->gradePricing as $gp) {
                    if (!isset($pricingByPrice[$gp->price])) {
                        $pricingByPrice[$gp->price] = [];
                    }
                    $pricingByPrice[$gp->price][] = $gp->grade;
                }
                
                $allRanges = [];
                foreach ($pricingByPrice as $price => $gradeList) {
                    // Sort by grade order
                    usort($gradeList, function($a, $b) use ($gradeOrder) {
                        $aIndex = array_search($a, $gradeOrder);
                        $bIndex = array_search($b, $gradeOrder);
                        if ($aIndex === false) $aIndex = 999;
                        if ($bIndex === false) $bIndex = 999;
                        return $aIndex <=> $bIndex;
                    });
                    
                    // Group consecutive grades into ranges
                    $currentRange = ['from' => $gradeList[0], 'to' => $gradeList[0]];
                    foreach ($gradeList as $i => $grade) {
                        if ($i === 0) continue;
                        $prevIndex = array_search($gradeList[$i-1], $gradeOrder);
                        $currIndex = array_search($grade, $gradeOrder);
                        if ($currIndex === $prevIndex + 1) {
                            $currentRange['to'] = $grade;
                        } else {
                            $rangeStr = $currentRange['from'] === $currentRange['to'] 
                                ? $currentRange['from'] 
                                : $currentRange['from'] . '–' . $currentRange['to'];
                            $allRanges[] = [
                                'range' => $rangeStr,
                                'fromGrade' => $currentRange['from'],
                                'price' => $price
                            ];
                            $currentRange = ['from' => $grade, 'to' => $grade];
                        }
                    }
                    $rangeStr = $currentRange['from'] === $currentRange['to'] 
                        ? $currentRange['from'] 
                        : $currentRange['from'] . '–' . $currentRange['to'];
                    $allRanges[] = [
                        'range' => $rangeStr,
                        'fromGrade' => $currentRange['from'],
                        'price' => $price
                    ];
                }
                
                // Sort all ranges by starting grade order
                usort($allRanges, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a['fromGrade'], $gradeOrder);
                    $bIndex = array_search($b['fromGrade'], $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                // Build formatted lines
                $lines = [];
                foreach ($allRanges as $rangeData) {
                    // Pad grade range to fixed width for alignment
                    $paddedRange = str_pad($rangeData['range'], 8, ' ', STR_PAD_RIGHT);
                    $lines[] = '<span class="grade-range">' . htmlspecialchars($paddedRange) . '</span><span class="grade-price-value">→   ₹' . number_format($rangeData['price'], 2) . '</span>';
                }
                
                return implode("\n", $lines);
            }
            // If no grade pricing, show all grades with regular price
            $regularPrice = $product->price_regular ?? 0;
            if ($regularPrice > 0) {
                $paddedRange = str_pad('Pre-KG–12', 8, ' ', STR_PAD_RIGHT);
                return '<span class="grade-range">' . htmlspecialchars($paddedRange) . '</span><span class="grade-price-value">→   ₹' . number_format($regularPrice, 2) . '</span>';
            }
            return 'N/A';
        };

        // Determine row class for zebra striping
        $getRowClass = function($index) {
            return ($index % 2 === 0) ? 'row-even' : 'row-odd';
        };

        // Get status styling
        $getStatusStyle = function($status) {
            $status = strtolower($status ?? '');
            if ($status === 'live') {
                return 'status-live';
            } elseif ($status === 'draft') {
                return 'status-draft';
            } else {
                return 'status-archived';
            }
        };

        // Get stock status styling
        $getStockStatusStyle = function($stockStatus, $stock, $lowStockThreshold) {
            $stockStatus = strtolower($stockStatus ?? '');
            $stock = (int)($stock ?? 0);
            $threshold = (int)($lowStockThreshold ?? 0);
            
            if ($stockStatus === 'in_stock') {
                if ($stock > 0 && $stock <= $threshold) {
                    return 'stock-low';
                }
                return 'stock-in';
            } else {
                return 'stock-out';
            }
        };

        // Format N/A values
        $formatNA = function($value) {
            if (empty($value) || $value === 'N/A' || $value === null) {
                return '<span class="text-na">N/A</span>';
            }
            return htmlspecialchars($value);
        };

        // Format numeric values
        $formatPrice = function($value) {
            $val = (float)($value ?? 0);
            if ($val == 0) {
                return '<span class="text-zero">0.00</span>';
            }
            return '<span class="price-regular">₹' . number_format($val, 2) . '</span>';
        };

        $formatSalePrice = function($value) {
            $val = (float)($value ?? 0);
            if ($val == 0) {
                return '<span class="text-zero">0.00</span>';
            }
            return '<span class="price-sale">₹' . number_format($val, 2) . '</span>';
        };

        // Format weight (numeric without currency symbol)
        $formatWeight = function($value) {
            if (empty($value) || $value === null) {
                return '<span class="text-na">N/A</span>';
            }
            $val = (float)$value;
            if ($val == 0) {
                return '<span class="text-zero">0.00</span>';
            }
            return number_format($val, 2);
        };

        $formatStock = function($stock, $lowStockThreshold) {
            $stockVal = (int)($stock ?? 0);
            $threshold = (int)($lowStockThreshold ?? 0);
            if ($stockVal == 0) {
                return '<span class="text-zero">0</span>';
            }
            // Bold if > 0
            $class = 'stock-bold';
            if ($stockVal > 0 && $stockVal <= $threshold) {
                $class .= ' stock-low-warning';
            }
            return '<span class="' . $class . '">' . number_format($stockVal) . '</span>';
        };

        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<!--[if gte mso 9]><xml>
    <x:ExcelWorkbook>
        <x:ExcelWorksheets>
            <x:ExcelWorksheet>
                <x:Name>Products</x:Name>
                <x:WorksheetOptions>
                    <x:DefaultRowHeight>576</x:DefaultRowHeight>
                    <x:FreezePanes/>
                    <x:FrozenNoSplit/>
                    <x:SplitHorizontal>1</x:SplitHorizontal>
                    <x:SplitVertical>1</x:SplitVertical>
                    <x:TopRowBottomPane>1</x:TopRowBottomPane>
                    <x:LeftColumnRightPane>1</x:LeftColumnRightPane>
                    <x:ActivePane>0</x:ActivePane>
                    <x:Panes>
                        <x:Pane>
                            <x:Number>3</x:Number>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>1</x:Number>
                            <x:ActiveRow>0</x:ActiveRow>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>2</x:Number>
                            <x:ActiveCol>0</x:ActiveCol>
                        </x:Pane>
                        <x:Pane>
                            <x:Number>0</x:Number>
                            <x:ActiveRow>0</x:ActiveRow>
                            <x:ActiveCol>0</x:ActiveCol>
                        </x:Pane>
                    </x:Panes>
                    <x:ProtectContents>False</x:ProtectContents>
                    <x:Zoom>100</x:Zoom>
                </x:WorksheetOptions>
                <x:AutoFilter x:Range="A1:P1"/>
            </x:ExcelWorksheet>
        </x:ExcelWorksheets>
    </x:ExcelWorkbook>
</xml><![endif]-->
<style>
    * {
        font-family: "Segoe UI", "Roboto", "Inter", Arial, sans-serif;
    }
    body {
        background-color: #f8f9fa;
        margin: 0;
        padding: 8px;
    }
    table {
        border-collapse: collapse;
        width: 100%;
        background-color: #ffffff;
        font-size: 11pt;
        border-left: none;
    }
    th {
        background-color: #FFF3B0;
        color: #333333;
        font-weight: bold;
        font-size: 11pt;
        text-align: center;
        vertical-align: middle;
        padding: 8px 6px;
        border: 1px solid #000000;
        white-space: nowrap;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    td {
        padding: 8px 6px;
        border: 1px solid #000000;
        font-size: 11pt;
        color: #000000;
        vertical-align: middle;
    }
    td:first-child {
        border-left: none;
        border-right: 1px solid #000000;
    }
    th:first-child {
        border-left: none;
        border-right: 1px solid #000000;
    }
    table {
        border-left: none;
    }
    .row-even {
        background-color: #FFFFFF;
    }
    .row-odd {
        background-color: #F8FAFC;
    }
    .col-left {
        text-align: left;
    }
    .col-center {
        text-align: center;
    }
    .col-right {
        text-align: right;
    }
    .product-name {
        font-weight: 600;
        color: #111827;
        padding-left: 18px;
    }
    .school-name {
        font-weight: normal;
        color: #6B7280;
    }
    .grade-pricing {
        background-color: #F5F3FF;
        border-left: 4px solid #6366F1;
        padding: 14px 16px;
        line-height: 2.2;
        font-family: "Segoe UI Variable", "Consolas", "Courier New", monospace;
        font-size: 10.5pt;
        min-width: 280px;
        vertical-align: top;
    }
    .grade-range {
        font-weight: normal;
        color: #374151;
        display: inline-block;
        min-width: 60px;
        text-align: left;
    }
    .grade-price-value {
        font-weight: 600;
        color: #1F2937;
        font-family: "Segoe UI Variable", "Consolas", "Courier New", monospace;
        margin-left: 8px;
    }
    .status-live {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .status-draft {
        background-color: #f3f4f6;
        color: #4b5563;
        font-weight: 500;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .status-archived {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 50px;
        max-width: 65px;
        text-align: center;
    }
    .stock-in {
        background-color: #d1fae5;
        color: #065f46;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .stock-low {
        background-color: #fef3c7;
        color: #92400e;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .stock-out {
        background-color: #fee2e2;
        color: #991b1b;
        font-weight: bold;
        padding: 6px 12px;
        border-radius: 12px;
        display: inline-block;
        font-size: 10pt;
        min-width: 60px;
        max-width: 80px;
        text-align: center;
    }
    .price-regular {
        color: #374151;
        font-weight: normal;
    }
    .price-sale {
        color: #15803D;
        font-weight: 600;
    }
    .text-na {
        color: #9CA3AF;
        font-style: italic;
    }
    .text-zero {
        color: #B0B0B0;
    }
    .stock-bold {
        font-weight: bold;
        color: #1F2937;
    }
    .stock-low-warning {
        color: #dc2626;
    }
    .col-grade-pricing {
        min-width: 280px;
        max-width: 350px;
    }
    .col-status {
        min-width: 50px;
        max-width: 65px;
    }
    .col-stock-status {
        min-width: 60px;
        max-width: 80px;
    }
</style>
</head>
<body>
<table>
<thead>
<tr>
<th>S.No</th>
<th>Product</th>
<th>School</th>
<th>Grade</th>
<th>Category</th>
<th>Product Type</th>
<th>Gender</th>
<th>Regular</th>
<th>Tax</th>
<th>Tax Profile</th>
<th class="col-grade-pricing">Grade Pricing</th>
<th class="col-grade-pricing">Variants</th>
<th>Stock</th>
<th>Low Stock</th>
<th class="col-status">Status</th>
<th class="col-stock-status">Stock Status</th>
</tr>
</thead>
<tbody>';

        // Use a separate serial number counter to ensure continuous numbering
        $serialNumber = 0;
        foreach ($products as $index => $product) {
            // Skip if product is null or empty
            if (empty($product) || empty($product->product_name)) {
                continue;
            }
            
            $serialNumber++; // Increment only for valid products
            
            $rowClass = $getRowClass($index);
            $statusClass = $getStatusStyle($product->status);
            $stockStatusClass = $getStockStatusStyle($product->stock_status, $product->inventory_stock, $product->low_stock_threshold);
            
            $html .= '<tr class="' . $rowClass . '">';
            
            // Serial Number - Center aligned, continuous numbering
            $html .= '<td class="col-center">' . $serialNumber . '</td>';
            
            // Product Name - Left aligned, semi-bold, darker
            $html .= '<td class="col-left product-name">' . htmlspecialchars($product->product_name) . '</td>';
            
            // School - Left aligned, muted color
            $schoolName = optional($product->school)->name;
            if (empty($schoolName) || $schoolName === 'N/A') {
                $html .= '<td class="col-left"><span class="text-na">N/A</span></td>';
            } else {
                $html .= '<td class="col-left"><span class="school-name">' . htmlspecialchars($schoolName) . '</span></td>';
            }
            
            // Grade - Center aligned
            $gradeValue = $formatGrades($product);
            $html .= '<td class="col-center">' . ($gradeValue === 'All' || empty($gradeValue) ? $formatNA($gradeValue) : htmlspecialchars($gradeValue)) . '</td>';
            
            // Category - Left aligned
            $html .= '<td class="col-left">' . $formatNA($product->category) . '</td>';
            
            // Product Type - Left aligned
            $html .= '<td class="col-left">' . $formatNA($product->product_type) . '</td>';
            
            // Gender - Center aligned
            $html .= '<td class="col-center">' . htmlspecialchars(ucfirst($product->gender ?? 'N/A')) . '</td>';
            
            // Regular Price - Right aligned
            $html .= '<td class="col-right">' . $formatPrice($product->price_regular) . '</td>';
            
            // Tax - Right aligned
            $html .= '<td class="col-right">' . $formatPrice($product->price_tax) . '</td>';
            
            // Tax Profile - Left aligned
            $html .= '<td class="col-left" title="Tax Profile: ' . htmlspecialchars($product->tax_profile ?? 'N/A') . '">' . $formatNA($product->tax_profile) . '</td>';
            
            // Grade Pricing - Left aligned, structured format with card-like styling
            $gradePricingHtml = $formatGradePricing($product);
            if ($gradePricingHtml === 'N/A') {
                $html .= '<td class="col-left grade-pricing col-grade-pricing"><span class="text-na">N/A</span></td>';
            } else {
                // Convert newlines to <br> tags for proper line breaks in Excel
                $gradePricingHtmlWithBreaks = str_replace("\n", "<br>", $gradePricingHtml);
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Grade-wise pricing breakdown">' . $gradePricingHtmlWithBreaks . '</td>';
            }
            
            // Variants - Left aligned, structured format
            $formatVariants = function($product) {
                if ($product->variants && $product->variants->count() > 0) {
                    $variantLines = [];
                    foreach ($product->variants as $variant) {
                        $weight = $variant->weight ? number_format($variant->weight, 2) : 'N/A';
                        $stock = $variant->stock ?? 0;
                        $variantLines[] = $variant->option . ' → Weight: ' . $weight . ' | Stock: ' . number_format($stock);
                    }
                    return implode("\n", $variantLines);
                }
                return 'N/A';
            };
            $variantsHtml = $formatVariants($product);
            if ($variantsHtml === 'N/A') {
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Product variants"><span class="text-na">N/A</span></td>';
            } else {
                $variantsHtmlWithBreaks = str_replace("\n", "<br>", $variantsHtml);
                $html .= '<td class="col-left grade-pricing col-grade-pricing" title="Product variants">' . $variantsHtmlWithBreaks . '</td>';
            }
            
            // Stock - Right aligned, bold if low
            $html .= '<td class="col-right">' . $formatStock($product->inventory_stock, $product->low_stock_threshold) . '</td>';
            
            // Low Stock - Right aligned
            $lowStockVal = (int)($product->low_stock_threshold ?? 0);
            $html .= '<td class="col-right">' . ($lowStockVal == 0 ? '<span class="text-zero">0</span>' : number_format($lowStockVal)) . '</td>';
            
            // Status - Center aligned with color coding, narrower column
            $statusLabel = ucfirst($product->status ?? 'N/A');
            $html .= '<td class="col-center col-status"><span class="' . $statusClass . '">' . htmlspecialchars($statusLabel) . '</span></td>';
            
            // Stock Status - Center aligned with color coding, narrower column
            $stockStatusLabel = ucfirst(str_replace('_', ' ', $product->stock_status ?? 'N/A'));
            $html .= '<td class="col-center col-stock-status"><span class="' . $stockStatusClass . '">' . htmlspecialchars($stockStatusLabel) . '</span></td>';
            
            $html .= '</tr>';
        }

        $html .= '</tbody></table>
<script>
// Add filter functionality (Excel will handle this natively)
// Freeze panes is handled via XML configuration above
</script>
</body></html>';

        return response($html, 200)
            ->header('Content-Type', 'application/vnd.ms-excel; charset=utf-8')
            ->header('Content-Disposition', 'attachment; filename="catalog-export-' . date('Y-m-d') . '.xls"');
    }

    protected function downloadPdf(Collection $products)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.catalog.export-pdf', ['products' => $products]);
        return $pdf->download('catalog-export.pdf');
    }
}

