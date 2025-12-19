<?php

namespace App\Http\Controllers\Admin\BackToSchool;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductMapping;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(Request $request): View
    {
        $query = ProductMapping::query()
            ->where(function($q) {
                // strict scope: explicit type only
                $q->where('product_type', 'back_to_school');
            });

        if ($request->has('q')) {
            $query->where('product_name', 'like', '%' . $request->q . '%');
        }

        $products = $query->orderBy('product_name')->paginate(20);

        return view('admin.back_to_school.inventory.index', compact('products'));
    }

    public function update(Request $request, ProductMapping $product)
    {
        $validated = $request->validate([
            'inventory_stock' => 'required|integer|min:0',
        ]);

        $product->update(['inventory_stock' => $validated['inventory_stock']]);

        return back()->with('success', 'Stock updated successfully.');
    }
}
