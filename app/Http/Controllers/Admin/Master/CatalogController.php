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
        $grades = Grade::orderBy('name')->get();
        $productTypes = ProductMapping::select('product_type')->whereNotNull('product_type')->distinct()->orderBy('product_type')->pluck('product_type');
        $categories = ProductMapping::select('category')->whereNotNull('category')->distinct()->orderBy('category')->pluck('category');

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

        $filters = $request->only(['school_id', 'grade_id', 'status', 'gender', 'category', 'q']);
        $query = ProductMapping::with(['school', 'grade'])->orderBy('product_name');
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
            'excel' => $this->downloadDelimited($products, "\t", 'catalog-export.xls', 'application/vnd.ms-excel'),
            'pdf' => $this->downloadPdf($products),
        };
    }

    protected function formView(ProductMapping $product, string $mode): View
    {
        $schools = School::with('grades')->orderBy('name')->get();
        $grades = Grade::orderBy('name')->get();

        return view('admin.catalog.form', compact('product', 'schools', 'grades', 'mode'));
    }

    protected function validatedData(Request $request, ?ProductMapping $product = null): array
    {
        $validated = $request->validate([
            'school_id' => ['required', 'exists:schools,id'],
            'grade_id' => ['nullable', 'exists:grades,id'],
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
            'featured_image' => ['nullable', 'string', 'max:2048'],
            'inventory_stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'media_images' => ['nullable', 'string'],
            'media_gallery' => ['nullable', 'string'],
            'media_size_chart' => ['nullable', 'string', 'max:2048'],
            'size_measurement_image' => ['nullable', 'string', 'max:2048'],
            'media_measurement_video' => ['nullable', 'string', 'max:2048'],
        ]);

        if (! empty($validated['grade_id'])) {
            $grade = Grade::find($validated['grade_id']);
            abort_if(! $grade || (int) $validated['school_id'] !== (int) $grade->school_id, 422, 'Selected grade does not belong to the school.');
        }

        $validated['media_images'] = $this->stringToArray($validated['media_images'] ?? '');
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
            ->when($filters['grade_id'] ?? null, fn ($q, $gradeId) => $q->where('grade_id', $gradeId))
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
                    optional($product->grade)->name,
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
        $lines = [
            'Products & Catalog export',
            str_repeat('-', 40),
        ];

        foreach ($products as $product) {
            $lines[] = 'Product: ' . $product->product_name;
            $lines[] = 'School: ' . optional($product->school)->name . ' / Grade: ' . (optional($product->grade)->name ?? 'All');
            $lines[] = 'Pricing: ' . $product->price_regular . ' (Sale: ' . ($product->price_sale ?? '—') . ', Tax: ' . ($product->price_tax ?? '—') . ')';
            $lines[] = 'Inventory: stock ' . $product->inventory_stock . ' • alert ' . $product->low_stock_threshold;
            $lines[] = 'Status: ' . ucfirst($product->status);
            $lines[] = '';
        }

        $pdf = $this->buildSimplePdf($lines);

        return response()->streamDownload(fn () => print($pdf), 'catalog-export.pdf', [
            'Content-Type' => 'application/pdf',
        ]);
    }

    protected function buildSimplePdf(array $lines): string
    {
        $escaped = array_map(fn ($line) => $this->escapePdfText($line), $lines);
        $contentBody = "BT\n/F1 10 Tf\n72 720 Td\n";
        foreach ($escaped as $index => $line) {
            $contentBody .= '(' . $line . ") Tj\n";
            if ($index !== array_key_last($escaped)) {
                $contentBody .= "T*\n";
            }
        }
        $contentBody .= "ET";
        $length = strlen($contentBody);

        $pdf = "%PDF-1.4\n";
        $objects = [
            '<< /Type /Catalog /Pages 2 0 R >>',
            '<< /Type /Pages /MediaBox [0 0 612 792] /Count 1 /Kids [3 0 R] >>',
            '<< /Type /Page /Parent 2 0 R /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>',
            "<< /Length $length >>\nstream\n$contentBody\nendstream",
            '<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>',
        ];

        $offsets = [];
        foreach ($objects as $index => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($index + 1) . " 0 obj\n" . $object . "\nendobj\n";
        }

        $xrefPosition = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        foreach ($offsets as $offset) {
            $pdf .= sprintf("%010d 00000 n \n", $offset);
        }

        $pdf .= "trailer << /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n$xrefPosition\n%%EOF";

        return $pdf;
    }

    protected function escapePdfText(string $value): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $value);
    }
}

