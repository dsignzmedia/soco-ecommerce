<?php

namespace App\Http\Controllers\Admin\Master;

use App\Http\Controllers\Controller;
use App\Models\Admin\Master\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    public function create(Request $request): View
    {
        $routeName = $request->route()?->getName() ?? '';
        $layout = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.layouts.back_to_school',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.layouts.merchandise',
            default => 'admin.layouts.base',
        };
        
        // Determine redirect route and form routes based on context
        $redirectRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.product-settings.index',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.product-settings.index',
            default => 'master.admin.product-settings.index',
        };
        
        $storeRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.categories.store',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.categories.store',
            default => 'master.admin.categories.store',
        };

        // Calculate next sort order
        $maxSortOrder = Category::max('sort_order');
        $nextSortOrder = ($maxSortOrder !== null) ? $maxSortOrder + 1 : 0;

        return view('admin.categories.form', [
            'category' => new Category(),
            'mode' => 'create',
            'layout' => $layout,
            'redirectRoute' => $redirectRoute,
            'storeRoute' => $storeRoute,
            'nextSortOrder' => $nextSortOrder,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'], '_');
        }

        Category::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Determine redirect route based on referrer or route context
        $routeName = $request->route()?->getName() ?? '';
        $redirectRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.product-settings.index',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.product-settings.index',
            default => 'master.admin.product-settings.index',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category, Request $request): View
    {
        $routeName = $request->route()?->getName() ?? '';
        $layout = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.layouts.back_to_school',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.layouts.merchandise',
            default => 'admin.layouts.base',
        };
        
        // Determine redirect route and form routes based on context
        $redirectRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.product-settings.index',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.product-settings.index',
            default => 'master.admin.product-settings.index',
        };
        
        $updateRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.categories.update',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.categories.update',
            default => 'master.admin.categories.update',
        };

        return view('admin.categories.form', [
            'category' => $category,
            'mode' => 'edit',
            'layout' => $layout,
            'redirectRoute' => $redirectRoute,
            'updateRoute' => $updateRoute,
        ]);
    }

    public function update(Request $request, Category $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug,' . $category->id],
            'is_active' => ['boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        // Auto-generate slug from name if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = \Illuminate\Support\Str::slug($validated['name'], '_');
        }

        $category->update([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'is_active' => $validated['is_active'] ?? true,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        // Determine redirect route based on referrer or route context
        $routeName = $request->route()?->getName() ?? '';
        $redirectRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.product-settings.index',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.product-settings.index',
            default => 'master.admin.product-settings.index',
        };

        return redirect()->route($redirectRoute)
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category, Request $request): RedirectResponse
    {
        // Check if category is being used
        $usageCount = \App\Models\Admin\Master\ProductMapping::where('category', $category->slug)->count();
        
        // Determine redirect route based on referrer or route context
        $routeName = $request->route()?->getName() ?? '';
        $redirectRoute = match (true) {
            str_starts_with($routeName, 'admin.back_to_school.') => 'admin.back_to_school.product-settings.index',
            str_starts_with($routeName, 'admin.merchandise.') => 'admin.merchandise.product-settings.index',
            default => 'master.admin.product-settings.index',
        };
        
        if ($usageCount > 0) {
            return redirect()->route($redirectRoute)
                ->with('error', "Cannot delete category. It is being used by {$usageCount} product(s).");
        }

        $category->delete();

        return redirect()->route($redirectRoute)
            ->with('success', 'Category deleted successfully.');
    }
}
