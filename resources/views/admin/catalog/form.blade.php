@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product | The Skool Store')
@section('page_heading', ($isEdit ? 'Edit' : 'Add') . ' Product')
@section('page_subheading', 'Curate listings with full catalog metadata')

@section('content')
    <div class="card" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
        <p style="margin:0;color:#475467;">Fill out each section to keep listings consistent across schools.</p>
        <a href="{{ route('master.admin.catalog.index') }}" style="color:#490d59;font-weight:600;">← Back to catalog</a>
    </div>
    <div class="card">
        <form method="POST" action="{{ $isEdit ? route('master.admin.catalog.update', $product) : route('master.admin.catalog.store') }}">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;margin-bottom:24px;">
                <label>
                    <span>School *</span>
                    <select name="school_id" required>
                        <option value="">Select school</option>
                        @foreach($schools as $school)
                            <option value="{{ $school->id }}" @selected(old('school_id', $product->school_id) == $school->id)>{{ $school->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Grade</span>
                    <select name="grade_id">
                        <option value="">All grades</option>
                        @foreach($grades as $grade)
                            <option value="{{ $grade->id }}" @selected(old('grade_id', $product->grade_id) == $grade->id)>
                                {{ $grade->name }} ({{ $grade->school?->name }})
                            </option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Category</span>
                    <input type="text" name="category" value="{{ old('category', $product->category) }}">
                </label>
                <label>
                    <span>Product Type</span>
                    <input type="text" name="product_type" value="{{ old('product_type', $product->product_type) }}">
                </label>
                <label>
                    <span>Gender *</span>
                    <select name="gender" required>
                        @foreach(['boys','girls','unisex'] as $gender)
                            <option value="{{ $gender }}" @selected(old('gender', $product->gender) === $gender)>{{ ucfirst($gender) }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Stock status *</span>
                    <select name="stock_status">
                        @foreach(['in_stock' => 'In stock','out_of_stock' => 'Out of stock'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <label>
                    <span>Availability label</span>
                    <input type="text" name="availability_label" value="{{ old('availability_label', $product->availability_label) }}" placeholder="Eg: Ships in 2-3 days">
                </label>
            </div>

            <label>
                <span>Product Name *</span>
                <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
            </label>

            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin-top:24px;">
                <label>
                    <span>Description</span>
                    <textarea name="description" rows="5" placeholder="Rich text / marketing copy...">{{ old('description', $product->description) }}</textarea>
                </label>
                <label>
                    <span>Size Guidance</span>
                    <textarea name="size_guidance" rows="5" placeholder="Add measurement tips or conversion charts...">{{ old('size_guidance', $product->size_guidance) }}</textarea>
                </label>
            </div>

            <div style="margin-top:24px;">
                <h3 style="margin:0 0 12px;color:#111827;">Pricing</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                    <label>
                        <span>Regular price *</span>
                        <input type="number" name="price_regular" min="0" step="0.01" value="{{ old('price_regular', $product->price_regular) }}" required>
                    </label>
                    <label>
                        <span>Sale price</span>
                        <input type="number" name="price_sale" min="0" step="0.01" value="{{ old('price_sale', $product->price_sale) }}">
                    </label>
                    <label>
                        <span>Tax (%)</span>
                        <input type="number" name="price_tax" min="0" step="0.01" value="{{ old('price_tax', $product->price_tax) }}">
                    </label>
                    <label>
                        <span>Tax profile</span>
                        <select name="tax_profile">
                            <option value="">Select profile</option>
                            @foreach(['gst-5','gst-12','gst-18'] as $profile)
                                <option value="{{ $profile }}" @selected(old('tax_profile', $product->tax_profile) === $profile)>{{ strtoupper($profile) }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span>Product weight (kg)</span>
                        <input type="number" name="product_weight" min="0" step="0.01" value="{{ old('product_weight', $product->product_weight) }}">
                    </label>
                </div>
            </div>

            <div style="margin-top:24px;">
                <h3 style="margin:0 0 12px;color:#111827;">Inventory</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:16px;">
                    <label>
                        <span>Stock *</span>
                        <input type="number" name="inventory_stock" min="0" value="{{ old('inventory_stock', $product->inventory_stock) }}" required>
                    </label>
                    <label>
                        <span>Low stock alert *</span>
                        <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required>
                    </label>
                </div>
            </div>

            <div style="margin-top:24px;">
                <h3 style="margin:0 0 12px;color:#111827;">Media</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px;">
                    <label>
                        <span>Featured product image</span>
                        <input type="text" name="featured_image" value="{{ old('featured_image', $product->featured_image) }}" placeholder="https://...">
                    </label>
                    <label>
                        <span>Tag name</span>
                        <input type="text" name="tag_name" value="{{ old('tag_name', $product->tag_name) }}" placeholder="Eg: Bestseller">
                    </label>
                    <label>
                        <span>Featured images (comma separated URLs)</span>
                        <input type="text" name="media_images" value="{{ old('media_images', implode(', ', $product->media_images ?? [])) }}">
                    </label>
                    <label>
                        <span>Gallery images (comma separated URLs)</span>
                        <input type="text" name="media_gallery" value="{{ old('media_gallery', implode(', ', $product->media_gallery ?? [])) }}">
                    </label>
                    <label>
                        <span>Size chart URL</span>
                        <input type="text" name="media_size_chart" value="{{ old('media_size_chart', $product->media_size_chart) }}">
                    </label>
                    <label>
                        <span>Size measurement image URL</span>
                        <input type="text" name="size_measurement_image" value="{{ old('size_measurement_image', $product->size_measurement_image) }}">
                    </label>
                    <label>
                        <span>Measurement video URL</span>
                        <input type="text" name="media_measurement_video" value="{{ old('media_measurement_video', $product->media_measurement_video) }}">
                    </label>
                </div>
            </div>

            <div style="margin-top:32px;display:flex;gap:12px;flex-wrap:wrap;">
                <button type="submit" name="status" value="draft" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;background:#fff;color:#475467;">
                    Save draft
                </button>
                <button type="submit" name="status" value="live" style="padding:12px 20px;border:none;border-radius:12px;background:#490d59;color:#fff;font-weight:600;">
                    Publish
                </button>
                @if($isEdit)
                    <button type="submit" name="status" value="archived" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;background:#fff;color:#b42318;">
                        Archive
                    </button>
                @endif
                <a href="{{ route('master.admin.catalog.index') }}" style="padding:12px 20px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;">Cancel</a>
            </div>
        </form>
    </div>
@endsection

