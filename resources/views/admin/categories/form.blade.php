@php
    $isEdit = $mode === 'edit';
    $layout = $layout ?? 'admin.layouts.base';
    $redirectRoute = $redirectRoute ?? 'master.admin.product-settings.index';
    $storeRoute = $storeRoute ?? 'master.admin.categories.store';
    $updateRoute = $updateRoute ?? 'master.admin.categories.update';
@endphp

@extends($layout)

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Category | The Skool Store')
@section('page_heading', $isEdit ? 'Edit Category' : 'Add Category')
@section('page_subheading', 'Create or modify categories for better product organization')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">
        <a href="{{ route($redirectRoute) }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to Product Settings
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ $mode === 'edit' ? route($updateRoute, $category) : route($storeRoute) }}">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif

            @if($errors->any())
                <div style="margin-bottom:16px;padding:12px 16px;background:#fef2f2;color:#991b1b;border-radius:8px;border:1px solid #ef4444;">
                    <strong>Please fix the following errors:</strong>
                    <ul style="margin:8px 0 0 20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:24px;">
                <label>
                    <span>Name *</span>
                    <input type="text" name="name" value="{{ old('name', $category->name) }}" required placeholder="e.g., Uniform, Shoes, Bags">
                    <small style="color:#6b7280;font-size:12px;margin-top:4px;display:block;">Display name shown in dropdowns</small>
                </label>
                <label>
                    <span>Sort Order</span>
                    <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order ?? ($nextSortOrder ?? 0)) }}" min="0" placeholder="0">
                    <small style="color:#6b7280;font-size:12px;margin-top:4px;display:block;">Lower numbers appear first</small>
                </label>
            </div>

            <div style="margin-bottom:24px;">
                <label style="display:flex;align-items:center;gap:8px;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category->is_active ?? true)) style="width:auto;">
                    <span>Active (visible in dropdowns)</span>
                </label>
            </div>

            <input type="hidden" name="slug" value="{{ old('slug', $category->slug) }}" id="slug-field">

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <a href="{{ route($redirectRoute) }}" class="btn-back-outline">Cancel</a>
                <button type="submit" class="nav__item" style="background:#490d59;color:#fff;border:none;border-radius:9999px;padding:8px 24px;font-size:14px;font-weight:600;cursor:pointer;">
                    {{ $isEdit ? 'Update' : 'Create' }} Category
                </button>
            </div>
        </form>
    </div>

    <script>
        // Auto-generate slug from name (hidden field)
        document.querySelector('input[name="name"]')?.addEventListener('input', function(e) {
            const slugInput = document.getElementById('slug-field');
            if (slugInput && !slugInput.dataset.manual) {
                const slug = e.target.value
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '_')
                    .replace(/^_+|_+$/g, '');
                slugInput.value = slug;
            }
        });
    </script>
@endsection

