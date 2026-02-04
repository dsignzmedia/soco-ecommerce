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
        $routeName = $request->route()?->getName() ?? '';
        
        $scope = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'back_to_school',
            str_starts_with($routeName, 'admin.merchandise.') => 'merchandise',
            default => 'school',
        };

        $layout = match ($scope) {
            'back_to_school' => 'admin.layouts.back_to_school',
            'merchandise' => 'admin.layouts.merchandise',
            default => 'admin.layouts.base',
        };

        // $hideProductTypes = ($scope !== 'school');
        // Show Product Types for all admins as requested
        $hideProductTypes = false;

        $productTypes = ProductType::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'product_types')
            ->appends(array_merge($request->query(), ['tab' => 'product-types']));

        // Fetch categories scoped to the current context
        $categories = Category::where('type', $scope)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(10, ['*'], 'categories')
            ->appends(array_merge($request->query(), ['tab' => 'categories']));

        $defaultTab = $hideProductTypes ? 'categories' : 'product-types';

        return view('admin.product-settings.index', compact('productTypes', 'categories', 'layout', 'hideProductTypes', 'defaultTab'));
    }
}
