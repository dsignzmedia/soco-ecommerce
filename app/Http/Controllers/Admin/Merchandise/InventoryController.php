<?php

namespace App\Http\Controllers\Admin\Merchandise;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        // Strict scope for Merchandise products
        $query = ProductMapping::query()->where('product_type', 'merchandised');

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderBy('product_name')->paginate(20);

        return view('admin.merchandise.inventory.index', compact('products'));
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
}
