<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use App\Models\Admin\Master\School;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductMappingController extends Controller
{
    public function index(School $school): View
    {
        $mappings = $school->productMappings()->with('grade')->orderBy('product_name')->get();
        $grades = $school->grades()->pluck('name', 'id');

        return view('admin.schools.product-mapping', compact('school', 'mappings', 'grades'));
    }

    public function store(Request $request, School $school): RedirectResponse
    {
        $data = $this->validateMapping($request, $school);
        $school->productMappings()->create($data);

        return back()->with('status', 'Product mapping added.');
    }

    public function update(Request $request, School $school, ProductMapping $productMapping): RedirectResponse
    {
        abort_unless($productMapping->school_id === $school->id, 404);
        $productMapping->update($this->validateMapping($request, $school));

        return back()->with('status', 'Product mapping updated.');
    }

    public function destroy(School $school, ProductMapping $productMapping): RedirectResponse
    {
        abort_unless($productMapping->school_id === $school->id, 404);
        $productMapping->delete();

        return back()->with('status', 'Product mapping deleted.');
    }

    protected function validateMapping(Request $request, School $school): array
    {
        return $request->validate([
            'grade_id' => ['nullable', 'exists:grades,id'],
            'product_name' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'string', 'max:255'],
            'gender' => ['required', 'in:boys,girls,unisex'],
            'price_regular' => ['nullable', 'numeric', 'min:0'],
            'price_sale' => ['nullable', 'numeric', 'min:0'],
            'inventory_stock' => ['required', 'integer', 'min:0'],
            'low_stock_threshold' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:live,draft,archived'],
        ]);
    }
}

