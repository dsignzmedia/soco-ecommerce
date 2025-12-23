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

<<<<<<< HEAD
        $routeName = $request->route()?->getName() ?? '';
        $layout = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.layouts.back_to_school',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.layouts.merchandise',
            default => 'admin.layouts.base',
        };

        return view('admin.product-settings.index', compact('productTypes', 'categories', 'layout'));
=======
        return view('admin.product-settings.index', compact('productTypes', 'categories'));
>>>>>>> 299705238ea0ca997c2d2210725d7c82bc6ed1a2
    }
}
