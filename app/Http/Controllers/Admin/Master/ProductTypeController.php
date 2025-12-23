<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductTypeController extends Controller
{
    public function index(): View
    {
        $productTypes = ProductType::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.product-types.index', compact('productTypes'));
    }

    public function create(): View
    {
        return view('admin.product-types.form', [
            'productType' => new ProductType(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_types,slug'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'], '_');
        }

        ProductType::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('master.admin.product-settings.index')
            ->with('success', 'Product type created successfully.');
    }

    public function edit(ProductType $productType): View
    {
        return view('admin.product-types.form', [
            'productType' => $productType,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, ProductType $productType): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:product_types,slug,' . $productType->id],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'], '_');
        }

        $productType->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        return redirect()->route('master.admin.product-settings.index')
            ->with('success', 'Product type updated successfully.');
    }

    public function destroy(ProductType $productType): RedirectResponse
    {
        // Check if product type is being used
        $usageCount = \App\Models\Admin\Master\ProductMapping::where('product_type', $productType->slug)->count();
        
        if ($usageCount > 0) {
            return redirect()->route('master.admin.product-settings.index')
                ->with('error', "Cannot delete product type. It is being used by {$usageCount} product(s).");
        }

        $productType->delete();

        return redirect()->route('master.admin.product-settings.index')
            ->with('success', 'Product type deleted successfully.');
    }
}
