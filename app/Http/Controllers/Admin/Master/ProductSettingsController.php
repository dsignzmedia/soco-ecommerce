<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\ProductType;
use App\Models\Admin\Master\Category;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductSettingsController extends Controller
{
    public function index(Request $request): View
    {
        $productTypes = ProductType::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'product_types')
            ->appends($request->query());

        $categories = Category::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'categories')
            ->appends($request->query());

        return view('admin.product-settings.index', compact('productTypes', 'categories'));
    }
}
