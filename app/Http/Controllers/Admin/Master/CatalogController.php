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

        $query = ProductMapping::with(['school'])->withCount('variants');
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
            'LKG' => 'LKG',
            'UKG' => 'UKG',
            '1' => 'Grade 1',
            '2' => 'Grade 2',
            '3' => 'Grade 3',
            '4' => 'Grade 4',
            '5' => 'Grade 5',
            '6' => 'Grade 6',
            '7' => 'Grade 7',
            '8' => 'Grade 8',
            '9' => 'Grade 9',
            '10' => 'Grade 10',
            '11' => 'Grade 11',
            '12' => 'Grade 12',
        ];
        
        // Hardcoded options to ensure dropdowns are populated even without products
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

        $productTypes = [
            'authorized' => 'Authorized Product',
            'optional' => 'Optional Product',
            'merchandised' => 'Merchandised Product',
            'back_to_school' => 'Back to School Product',
        ];

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
            'PKG' => 'Pre-KG', 'LKG' => 'LKG', 'UKG' => 'UKG',
            'I' => 'Class 1', 'II' => 'Class 2', 'III' => 'Class 3',
            'IV' => 'Class 4', 'V' => 'Class 5', 'VI' => 'Class 6',
            'VII' => 'Class 7', 'VIII' => 'Class 8', 'IX' => 'Class 9',
            'X' => 'Class 10', 'XI' => 'Class 11', 'XII' => 'Class 12',
        ];

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

        $productTypes = [
            'authorized' => 'Authorized Product',
            'optional' => 'Optional Product',
            'merchandised' => 'Merchandised Product',
            'back_to_school' => 'Back to School Product',
        ];

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
                        'stock' => $variantData['stock'] ?? 0,
                        'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                    ]);
                }
            }
            $product->updateTotalStock();
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
                            'stock' => $variantData['stock'] ?? 0,
                            'low_stock_threshold' => $variantData['low_stock_threshold'] ?? 5,
                        ]);
                    } else {
                        // Create new
                        $productMapping->variants()->create([
                            'name' => $variantData['name'] ?? 'Size',
                            'option' => $variantData['option'],
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

    // ... (export methods remain unchanged)

    protected function validatedData(Request $request, ?ProductMapping $product = null): array
    {
        $rules = [
            'school_id' => ['required', 'exists:schools,id'],
            'grade' => ['nullable', 'string'],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:male,female,unisex'],
            'stock_status' => ['required', 'in:in_stock,out_of_stock'],
            'availability_label' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:live,draft,archived'],
            'description' => ['nullable', 'string'],
            'size_guidance' => ['nullable', 'string'],
            'price_regular' => ['required', 'numeric', 'min:0'],
            'price_sale' => ['nullable', 'numeric', 'min:0'],
            'price_tax' => ['nullable', 'numeric', 'min:0'],
            'tax_profile' => ['nullable', 'string', 'max:255'],
            'product_weight' => ['nullable', 'numeric', 'min:0'],
            'tag_name' => ['nullable', 'string', 'max:255'],
            'inventory_stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'media_gallery' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url', 'max:255'],
            'variants' => ['nullable', 'array'],
            'variants.*.option' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'variants.*.low_stock_threshold' => ['nullable', 'integer', 'min:0'],
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

        if ($request->hasFile('media_images')) {
            $rules['media_images.*'] = ['image', 'max:2048'];
        }

        $validated = $request->validate($rules);

        // Handle Featured Image
        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }
        
        // Handle Size Chart
        if ($request->hasFile('size_chart_path')) {
            $validated['size_chart_path'] = $request->file('size_chart_path')->store('size_charts', 'public');
        }

        // Handle Gallery Images
        if ($request->hasFile('media_images')) {
            $paths = [];
            foreach ($request->file('media_images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
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

