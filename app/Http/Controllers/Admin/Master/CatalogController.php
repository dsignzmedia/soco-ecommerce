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

        $query = ProductMapping::with(['school', 'grade']);
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
            'fabrics' => 'Fabrics',
            'sports' => 'Sports',
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

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        ProductMapping::create($data);

        return redirect()->route('master.admin.catalog.index')->with('status', 'Product created.');
    }

    public function edit(ProductMapping $productMapping): View
    {
        return $this->formView($productMapping, 'edit');
    }

    public function update(Request $request, ProductMapping $productMapping): RedirectResponse
    {
        $originalPricing = $productMapping->only(['price_regular', 'price_sale', 'price_tax']);
        $productMapping->update($this->validatedData($request, $productMapping));

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
        $productMapping->load(['school', 'grade']);
        return view('admin.catalog.show', ['product' => $productMapping]);
    }

    public function destroy(ProductMapping $productMapping): RedirectResponse
    {
        $productMapping->delete();

        return redirect()->route('master.admin.catalog.index')->with('status', 'Product deleted.');
    }

    public function export(Request $request, string $type)
    {
        $type = strtolower($type);
        abort_unless(in_array($type, ['csv', 'excel', 'pdf'], true), 404);

        $filters = $request->only(['school_id', 'grade', 'status', 'gender', 'category', 'q']);
        $query = ProductMapping::with(['school'])->orderBy('product_name');
        $this->applyFilters($query, $filters);

        if (! empty($filters['q'])) {
            $query->where(function (Builder $builder) use ($filters) {
                $builder->where('product_name', 'like', '%' . $filters['q'] . '%')
                    ->orWhere('product_type', 'like', '%' . $filters['q'] . '%');
            });
        }

        $products = $query->get();

        return match ($type) {
            'csv' => $this->downloadDelimited($products, ',', 'catalog-export.csv', 'text/csv'),
            'excel' => $this->downloadExcelXml($products),
            'pdf' => $this->downloadPdf($products),
        };
    }

    protected function downloadExcelXml(Collection $products)
    {
        $grouped = $products->groupBy(function ($product) {
            return $product->school ? $product->school->name : 'Unassigned';
        });

        return response()->streamDownload(function () use ($grouped) {
            echo '<?xml version="1.0"?>' . "\n";
            echo '<?mso-application progid="Excel.Sheet"?>' . "\n";
            echo '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
            echo ' xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
            echo ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
            echo ' xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";
            
            echo ' <Styles>' . "\n";
            echo '  <Style ss:ID="Header">' . "\n";
            echo '   <Font ss:Bold="1"/>' . "\n";
            echo '   <Interior ss:Color="#E0E0E0" ss:Pattern="Solid"/>' . "\n";
            echo '  </Style>' . "\n";
            echo ' </Styles>' . "\n";

            foreach ($grouped as $schoolName => $schoolProducts) {
                // Excel sheet names must not exceed 31 chars and must not contain : \ / ? * [ ]
                $safeName = preg_replace('/[\\\\\\/?*\\[\\]:]/', '', $schoolName);
                $safeName = substr(trim($safeName), 0, 30);
                if (empty($safeName)) $safeName = 'Sheet';
                
                echo ' <Worksheet ss:Name="' . htmlspecialchars($safeName) . '">' . "\n";
                echo '  <Table>' . "\n";
                
                // Header
                echo '   <Row ss:StyleID="Header">' . "\n";
                $headers = ['Product Name', 'Grade', 'Category', 'Type', 'Gender', 'Price (Reg)', 'Price (Sale)', 'Stock', 'Status'];
                foreach ($headers as $h) {
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($h) . '</Data></Cell>' . "\n";
                }
                echo '   </Row>' . "\n";
                
                // Data
                foreach ($schoolProducts as $p) {
                    echo '   <Row>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($p->product_name) . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($p->grade ?? 'All') . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($p->category) . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($p->product_type) . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . htmlspecialchars($p->gender) . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="Number">' . $p->price_regular . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="Number">' . ($p->price_sale ?? 0) . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="Number">' . $p->inventory_stock . '</Data></Cell>' . "\n";
                    echo '    <Cell><Data ss:Type="String">' . $p->status . '</Data></Cell>' . "\n";
                    echo '   </Row>' . "\n";
                }
                
                echo '  </Table>' . "\n";
                echo ' </Worksheet>' . "\n";
            }
            
            echo '</Workbook>';
        }, 'catalog-export-schools.xls', [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }

    protected function formView(ProductMapping $product, string $mode): View
    {
        $schools = School::with('grades')->orderBy('name')->get();
        
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

        $categories = [
            'regular_uniforms' => 'Regular Uniforms',
            'fabrics' => 'Fabrics',
            'sports' => 'Sports',
        ];

        $productTypes = [
            'authorized' => 'Authorized Product',
            'optional' => 'Optional Product',
            'merchandised' => 'Merchandised Product',
            'back_to_school' => 'Back to School Product',
        ];

        return view('admin.catalog.form', compact('product', 'schools', 'grades', 'mode', 'categories', 'productTypes'));
    }

    protected function validatedData(Request $request, ?ProductMapping $product = null): array
    {
        $rules = [
            'school_id' => ['required', 'exists:schools,id'],
            'grade' => ['nullable', 'string'],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:boys,girls,unisex'],
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
            'media_size_chart' => ['nullable', 'string', 'max:2048'],
            'size_measurement_image' => ['nullable', 'string', 'max:2048'],
            'media_measurement_video' => ['nullable', 'string', 'max:2048'],
        ];

        // Add file validation rules
        if ($request->hasFile('featured_image')) {
            $rules['featured_image'] = ['image', 'max:2048']; // Max 2MB
        } else {
            $rules['featured_image'] = ['nullable', 'string', 'max:2048']; // Allow keeping existing path
        }

        if ($request->hasFile('media_images')) {
            $rules['media_images.*'] = ['image', 'max:2048'];
        }

        $validated = $request->validate($rules);

        // Handle Featured Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('products', 'public');
            $validated['featured_image'] = $path;
        }

        // Handle Gallery Images Upload
        if ($request->hasFile('media_images')) {
            $paths = [];
            foreach ($request->file('media_images') as $file) {
                $paths[] = $file->store('products', 'public');
            }
            // If editing, you might want to merge with existing or replace. 
            // For now, let's append if it's an update, or just set if new.
            // But since the form input for file doesn't show existing files to "keep", 
            // usually file inputs replace. However, for multiple, we might want to keep old ones.
            // Let's assume replacement for simplicity unless we add logic to keep specific ones.
            // Actually, a common pattern is: new uploads are added to existing ones.
            
            if ($product && $product->media_images) {
                $validated['media_images'] = array_merge($product->media_images, $paths);
            } else {
                $validated['media_images'] = $paths;
            }
        } else {
            // If no new files uploaded, keep existing array (handled by not being in $validated if not present in request?)
            // But wait, 'media_images' is not in $rules as a direct field if it's a file array.
            // If we don't upload anything, we should preserve the old value if it exists.
            if ($product) {
                 // We don't need to do anything if we don't include it in $validated, 
                 // but we need to make sure we don't overwrite it with null.
                 // The validate() call filters out non-validated fields.
                 // So if we don't validate 'media_images' when it's not present, it won't be in $validated.
                 // But we need to ensure we don't accidentally clear it if the user didn't upload anything.
            }
        }
        
        // Handle legacy gallery string if provided
        $validated['media_gallery'] = $this->stringToArray($validated['media_gallery'] ?? '');

        return $validated;
    }

    protected function stringToArray(?string $value): array
    {
        if (! $value) {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    protected function applyFilters(Builder $query, array $filters): void
    {
        $query->when($filters['school_id'] ?? null, fn ($q, $schoolId) => $q->where('school_id', $schoolId))
            ->when($filters['grade'] ?? null, fn ($q, $grade) => $q->where('grade', $grade))
            ->when($filters['status'] ?? null, fn ($q, $status) => $q->where('status', $status))
            ->when($filters['gender'] ?? null, fn ($q, $gender) => $q->where('gender', $gender))
            ->when($filters['category'] ?? null, fn ($q, $category) => $q->where('category', $category))
            ->when($filters['product_type'] ?? null, fn ($q, $type) => $q->where('product_type', $type))
            ->when($filters['stock_status'] ?? null, fn ($q, $stock) => $q->where('stock_status', $stock));
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

