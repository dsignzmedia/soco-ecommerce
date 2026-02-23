@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product Type | The Skool Store')
@section('page_heading', $isEdit ? 'Edit Product Type' : 'Add Product Type')
@section('page_subheading', 'Create or modify product types for better categorization')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">
        <a href="{{ route('master.admin.product-settings.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to Product Settings
        </a>
    </div>

    <div class="card">
        <form method="POST" action="{{ $mode === 'edit' ? route('master.admin.product-types.update', $productType) : route('master.admin.product-types.store') }}">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif


            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:24px;margin-bottom:24px;">
                <label>
                    <span style="font-weight: 500; font-size: 14px; color: #4b5563; margin-bottom: 6px; display: block;">Name *</span>
                    <input type="text" name="name" value="{{ old('name', $productType->name) }}" required placeholder="e.g., Back to School, Merchandise" style="width: 100%; border: 1px solid #d1d5db; border-radius: 6px; padding: 10px 12px; font-size: 14px; transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;">
                    <small style="color:#6b7280;font-size:12px;margin-top:4px;display:block;">Display name shown in dropdowns and filters</small>
                </label>

                <label>
                    <span>Product Tag</span>
                    <input type="text" name="product_tag" value="{{ old('product_tag', $productType->product_tag) }}" placeholder="e.g., NEW ARRIVAL">
                    <small style="color:#6b7280;font-size:12px;margin-top:4px;display:block;">Tag displayed on product card (if enabled)</small>
                </label>
            </div>

            <div style="margin-bottom:24px;display:flex;flex-wrap:wrap;gap:24px;">
                <label style="display:flex;align-items:center;gap:8px;cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $productType->is_active ?? true)) style="width: 16px; height: 16px; border-radius: 4px; border: 1px solid #d1d5db; cursor: pointer;">
                    <span style="font-size: 14px; color: #374151;">Active (visible in dropdowns)</span>
                </label>
            </div>

            <input type="hidden" name="slug" value="{{ old('slug', $productType->slug) }}" id="slug-field">

            <div style="display:flex;gap:12px;justify-content:flex-end;margin-top:24px;">
                <a href="{{ route('master.admin.product-settings.index') }}" class="btn-back-outline">Cancel</a>
                <button type="submit" class="nav__item" style="background:#490d59;color:#fff;border:none;border-radius:9999px;padding:8px 24px;font-size:14px;font-weight:600;cursor:pointer;">
                    {{ $isEdit ? 'Update' : 'Create' }} Product Type
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

