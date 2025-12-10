@extends('admin.layouts.base')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product | The Skool Store')
@section('page_heading', ($isEdit ? 'Edit' : 'Add') . ' Product')
@section('page_subheading', 'Curate listings with full catalog metadata')

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div>
            <h2 style="margin:0;color:#111827;font-size:24px;">{{ $isEdit ? 'Edit Product' : 'Add Product' }}</h2>
            <p style="margin:4px 0 0;color:#475467;">Fill out each section to keep listings consistent across schools.</p>
        </div>
        <a href="{{ route('master.admin.catalog.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to catalog
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('master.admin.catalog.update', $product) : route('master.admin.catalog.store') }}" enctype="multipart/form-data">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
            <!-- Left Column -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                
                <!-- Basic Info -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-info-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Product Information
                    </h3>
                    <label style="margin-bottom:16px;">
                        <span>Product Name *</span>
                        <input type="text" name="product_name" value="{{ old('product_name', $product->product_name) }}" required>
                    </label>
                    
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Description</span>
                            <textarea name="description" rows="5" placeholder="Rich text / marketing copy...">{{ old('description', $product->description) }}</textarea>
                        </label>
                        <label>
                            <span>Size Guidance</span>
                            <textarea name="size_guidance" rows="5" placeholder="Add measurement tips or conversion charts...">{{ old('size_guidance', $product->size_guidance) }}</textarea>
                        </label>
                    </div>
                </div>

                <!-- Pricing -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-tag" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Pricing
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
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
                    </div>
                </div>

                <!-- Inventory -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-cubes" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Inventory
                    </h3>
                    <div style="display:grid;grid-template-columns:repeat(3, 1fr);gap:16px;">
                        <label>
                            <span>Stock *</span>
                            <input type="number" name="inventory_stock" min="0" value="{{ old('inventory_stock', $product->inventory_stock) }}" required>
                        </label>
                        <label>
                            <span>Low stock alert *</span>
                            <input type="number" name="low_stock_threshold" min="0" value="{{ old('low_stock_threshold', $product->low_stock_threshold) }}" required>
                        </label>
                        <label>
                            <span>Product weight (kg)</span>
                            <input type="number" name="product_weight" min="0" step="0.01" value="{{ old('product_weight', $product->product_weight) }}">
                        </label>
                    </div>
                </div>

                <!-- Media -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-images" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Media
                    </h3>
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Featured product image</span>
                            <input type="file" name="featured_image" accept="image/*">
                            @if($product->featured_image)
                                <div style="margin-top:8px;">
                                    <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}" alt="Featured" style="width:80px;height:80px;object-fit:cover;border-radius:8px;">
                                </div>
                            @endif
                        </label>
                        <label>
                            <span>Gallery images</span>
                            <input type="file" name="media_images[]" multiple accept="image/*">
                            @if($product->media_images)
                                <div style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap;">
                                    @foreach($product->media_images as $img)
                                        <img src="{{ Str::startsWith($img, 'http') ? $img : asset('storage/' . $img) }}" alt="Gallery" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                    @endforeach
                                </div>
                            @endif
                        </label>
                    </div>
                    
                    <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                        <label>
                            <span>Size chart URL</span>
                            <input type="text" name="media_size_chart" value="{{ old('media_size_chart', $product->media_size_chart) }}">
                        </label>
                        <label>
                            <span>Measurement video URL</span>
                            <input type="text" name="media_measurement_video" value="{{ old('media_measurement_video', $product->media_measurement_video) }}">
                        </label>
                    </div>
                </div>
            </div>

            <!-- Right Column (Sidebar Style) -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-sliders-h" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Organization
                    </h3>
                    
                    <div style="display:flex;flex-direction:column;gap:16px;">
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
                            <select name="grade">
                                <option value="">All grades</option>
                                @foreach($grades as $key => $label)
                                    <option value="{{ $key }}" @selected(old('grade', $product->grade) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Category</span>
                            <select name="category">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Product Type</span>
                            <select name="product_type">
                                <option value="">Select Type</option>
                                @foreach($productTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('product_type', $product->product_type) === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
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
                            <span>Tag name</span>
                            <input type="text" name="tag_name" value="{{ old('tag_name', $product->tag_name) }}" placeholder="Eg: Bestseller">
                        </label>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-check-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Publish
                    </h3>
                    <label style="margin-bottom:16px;">
                        <span>Stock status *</span>
                        <select name="stock_status">
                            @foreach(['in_stock' => 'In stock','out_of_stock' => 'Out of stock'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="margin-bottom:20px;">
                        <span>Availability label</span>
                        <input type="text" name="availability_label" value="{{ old('availability_label', $product->availability_label) }}" placeholder="Eg: Ships in 2-3 days">
                    </label>

                    <div style="display:flex;flex-direction:column;gap:10px;">
                        <button type="submit" name="status" value="live" style="width:100%;padding:12px;border:none;border-radius:8px;background:#490d59;color:#fff;font-weight:600;cursor:pointer;">
                            Publish Product
                        </button>
                        <button type="submit" name="status" value="draft" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d0d5dd;background:#fff;color:#475467;cursor:pointer;">
                            Save Draft
                        </button>
                        @if($isEdit)
                            <button type="submit" name="status" value="archived" style="width:100%;padding:12px;border-radius:8px;border:1px solid #d0d5dd;background:#fff;color:#b42318;cursor:pointer;">
                                Archive Product
                            </button>
                        @endif
                        <a href="{{ route('master.admin.catalog.index') }}" style="text-align:center;padding:12px;color:#475467;text-decoration:none;">Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

