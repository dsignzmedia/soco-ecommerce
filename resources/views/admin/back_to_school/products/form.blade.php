
@extends('admin.layouts.back_to_school')

@php($isEdit = $mode === 'edit')

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product | The Skool Store')
@section('page_heading', ($isEdit ? 'Edit' : 'Add') . ' Product')
@section('page_subheading', 'Curate listings with full catalog metadata')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">

        <a href="{{ route('admin.back_to_school.products.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to catalog
        </a>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.back_to_school.products.update', $product) : route('admin.back_to_school.products.store') }}" enctype="multipart/form-data">
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
                    
                    <label>
                        <span>Description</span>
                        <textarea name="description" rows="5" placeholder="Rich text / marketing copy...">{{ old('description', $product->description) }}</textarea>
                    </label>
                    

                </div>

                <!-- Pricing -->
                <div class="card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                            <i class="fas fa-tag" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Pricing
                        </h3>
                    
                    <!-- Pricing Section -->
                    <div id="main-pricing-section">
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                            <label class="main-price-field">
                                <span>Price *</span>
                                <input type="number" id="main-price-input" name="price_regular" min="0" step="0.01" value="{{ old('price_regular', $product->price_regular) }}" required>
                            </label>
                            <label class="tax-fields">
                                <span>Tax (%)</span>
                                <input type="number" name="price_tax" min="0" step="0.01" value="{{ old('price_tax', $product->price_tax) }}">
                            </label>
                            <label class="tax-fields">
                                <span>Tax profile</span>
                                <select name="tax_profile">
                                    <option value="">Select profile</option>
                                    @foreach(['gst-5','gst-12','gst-18'] as $profile)
                                        <option value="{{ $profile }}" @selected(old('tax_profile', $product->tax_profile) === $profile)>{{ strtoupper($profile) }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="inclusive-tax-field" style="display:flex;align-items:center;gap:8px;padding-top:24px;">
                                <input type="checkbox" name="price_inclusive_tax" value="1" @checked(old('price_inclusive_tax', $product->price_inclusive_tax ?? true)) style="width:auto;">
                                <span>Inclusive of all tax</span>
                            </label>
                            {{-- Weight field removed - now using variant-wise weight --}}
                        </div>
                        </div>
                    </div>

                <!-- Product Variants -->
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="margin:0;color:#111827;display:flex;align-items:center;gap:10px;">
                            <i class="fas fa-list" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                            Product Variants
                        </h3>
                    </div>
                    
                    <!-- Metadata Hidden Inputs -->
                    <input type="hidden" name="inventory_stock" value="{{ old('inventory_stock', $product->inventory_stock ?? 0) }}">
                    <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">

                    <div id="variants-container">
                        @if(old('variants'))
                            @foreach(old('variants') as $index => $variant)
                                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                                    <label>
                                        <span style="font-size:12px;">Size / Option</span>
                                        <input type="text" name="variants[{{$index}}][option]" value="{{ $variant['option'] }}" placeholder="e.g. S, M, 10" required>
                                        <input type="hidden" name="variants[{{$index}}][id]" value="{{ $variant['id'] ?? '' }}">
                                    </label>
                                    <label class="variant-price-label" style="display:none;position:absolute;visibility:hidden;">
                                        <span class="variant-price-label-text" style="font-size:12px;">Price *</span>
                                        <input type="number" name="variants[{{$index}}][price]" min="0" step="0.01" value="{{ $variant['price'] ?? '' }}" placeholder="0.00" class="variant-price-input">
                                    </label>
                                    <label class="variant-weight-label" style="display:block;">
                                        <span class="variant-weight-label-text" style="font-size:12px;">Weight (kg)</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][weight]" min="0" step="0.01" value="{{ $variant['weight'] ?? '' }}" placeholder="0.00" class="variant-weight-input">
                                            @if($index == 0)
                                            <button type="button" class="apply-all-btn" data-field="weight" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <label>
                                        <span class="variant-stock-label-text" style="font-size:12px;">Stock</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant['stock'] }}" placeholder="Qty" min="0" class="variant-stock">
                                            @if($index == 0)
                                            <button type="button" class="apply-all-btn" data-field="stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <label>
                                        <span class="variant-low-stock-label-text" style="font-size:12px;">Low Stock Alert</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant['low_stock_threshold'] ?? 5 }}" placeholder="Alert Qty" min="0" class="variant-low-stock">
                                            @if($index == 0)
                                            <button type="button" class="apply-all-btn" data-field="low_stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @elseif($product->variants && $product->variants->count() > 0)
                            @foreach($product->variants as $index => $variant)
                                <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                                    <label>
                                        <span style="font-size:12px;">Size / Option</span>
                                        <input type="text" name="variants[{{$index}}][option]" value="{{ $variant->option }}" placeholder="e.g. S, M, 10" required>
                                        <input type="hidden" name="variants[{{$index}}][id]" value="{{ $variant->id }}">
                                    </label>
                                    <label class="variant-price-label" style="display:none;position:absolute;visibility:hidden;">
                                        <span class="variant-price-label-text" style="font-size:12px;">Price *</span>
                                        <input type="number" name="variants[{{$index}}][price]" min="0" step="0.01" value="{{ $variant->price ?? '' }}" placeholder="0.00" class="variant-price-input">
                                    </label>
                                    <label class="variant-weight-label" style="display:block;">
                                        <span class="variant-weight-label-text" style="font-size:12px;">Weight (kg)</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][weight]" min="0" step="0.01" value="{{ $variant->weight ?? '' }}" placeholder="0.00" class="variant-weight-input">
                                            @if($index === 0)
                                            <button type="button" class="apply-all-btn" data-field="weight" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <label>
                                        <span class="variant-stock-label-text" style="font-size:12px;">Stock</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant->stock }}" placeholder="Qty" min="0" class="variant-stock" @if($product->exists) readonly style="background-color:#f3f4f6;cursor:not-allowed;" @endif>
                                            @if($index === 0)
                                            <button type="button" class="apply-all-btn" data-field="stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <label>
                                        <span class="variant-low-stock-label-text" style="font-size:12px;">Low Stock Alert</span>
                                        <div style="display:flex; gap:5px;">
                                            <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant->low_stock_threshold }}" placeholder="Alert Qty" min="0" class="variant-low-stock">
                                            @if($index === 0)
                                            <button type="button" class="apply-all-btn" data-field="low_stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                                <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                            </button>
                                            @endif
                                        </div>
                                    </label>
                                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Empty State / One Default Row -->
                             <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;">
                                <label>
                                    <span style="font-size:12px;">Size / Option</span>
                                    <input type="text" name="variants[0][option]" value="22" placeholder="e.g. S, M, 10">
                                </label>
                                <label class="variant-price-label" style="display:none;position:absolute;visibility:hidden;">
                                    <span class="variant-price-label-text" style="font-size:12px;">Price *</span>
                                    <input type="number" name="variants[0][price]" min="0" step="0.01" placeholder="0.00" class="variant-price-input">
                                </label>
                                <label class="variant-weight-label" style="display:block;">
                                    <span class="variant-weight-label-text" style="font-size:12px;">Weight (kg)</span>
                                    <div style="display:flex; gap:5px;">
                                        <input type="number" name="variants[0][weight]" value="1" min="0" step="0.01" placeholder="0.00" class="variant-weight-input">
                                        <button type="button" class="apply-all-btn" data-field="weight" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                            <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                        </button>
                                    </div>
                                </label>
                                <label>
                                    <span class="variant-stock-label-text" style="font-size:12px;">Stock</span>
                                    <div style="display:flex; gap:5px;">
                                        <input type="number" name="variants[0][stock]" value="100" placeholder="Qty" min="0" class="variant-stock" @if($product->exists) readonly style="background-color:#f3f4f6;cursor:not-allowed;" @endif>
                                        <button type="button" class="apply-all-btn" data-field="stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                            <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                        </button>
                                    </div>
                                </label>
                                <label>
                                    <span class="variant-low-stock-label-text" style="font-size:12px;">Low Stock Alert</span>
                                    <div style="display:flex; gap:5px;">
                                        <input type="number" name="variants[0][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5" class="variant-low-stock">
                                        <button type="button" class="apply-all-btn" data-field="low_stock" title="Apply to all variants" style="background:#f3f4f6; border:1px solid #d1d5db; border-radius:4px; padding:0 8px; cursor:pointer;">
                                            <i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>
                                        </button>
                                    </div>
                                </label>
                                <div style="display:flex;align-items:end;padding-bottom:10px;">
                                    <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <div style="margin-top:10px; display: flex; gap: 10px; align-items: center;">
                        <input type="number" id="add-variant-count" value="1" min="1" style="width: 80px; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px;" placeholder="Qty">
                        <button type="button" id="add-variant-btn" style="flex-grow: 1; background:#f9fafb; border:1px dashed #d0d5dd; border-radius:8px; padding:10px; color:#475467; font-size:13px; cursor:pointer;">
                            + Add size/variant(s)
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Column (Sidebar Style) -->
            <div style="display:flex;flex-direction:column;gap:24px;">
                <div class="card organization-card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-sliders-h" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Organization
                    </h3>
                    
                    <div style="display:flex;flex-direction:column;gap:16px;">
                            <div style="margin-bottom: 16px;">
                                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:4px;">
                                    <span style="font-size:14px; font-weight:600; color:#374151;">School *</span>
                                    <label style="display:flex; align-items:center; gap:8px; cursor:pointer;" title="Select All Schools">
                                        <span style="font-size:12px; font-weight:600; color:#490d59;">Select All</span>
                                        <label class="custom-toggle" style="margin:0;">
                                            <input type="checkbox" id="select-all-schools-toggle" onchange="toggleAllSchools(this)">
                                            <span class="slider"></span>
                                        </label>
                                    </label>
                                </div>
                                <p style="font-size:12px; color:#6b7280; margin: 0 0 10px 0;">Hold Ctrl/Cmd to select multiple schools</p>
                                
                                <div id="school-selection-container" style="{{ (count((array)$selectedSchoolIds) > 0 && count((array)$selectedSchoolIds) === (int)$allSchoolsCount) ? 'display:none;' : 'display:block;' }}">
                                    <select name="school_ids[]" id="school-ids-select" required multiple style="width: 100%; min-height: 120px; padding: 10px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; box-sizing: border-box;">
                                        @foreach($schools as $school)
                                            <option value="{{ $school->id }}" @selected(in_array($school->id, (array)$selectedSchoolIds))>{{ $school->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        <script>
                             function toggleAllSchools(toggle) {
                                 const sel = document.getElementById('school-ids-select');
                                 const container = document.getElementById('school-selection-container');
                                 if (!sel || !container) return;
                                 
                                 if (toggle.checked) {
                                     // Select all options
                                     Array.from(sel.options).forEach(o => o.selected = true);
                                     container.style.display = 'none';
                                 } else {
                                     // Deselect all
                                     Array.from(sel.options).forEach(o => o.selected = false);
                                     container.style.display = 'block';
                                 }
                             }
                            // On page load: if all options are already selected, turn the toggle on
                            document.addEventListener('DOMContentLoaded', function() {
                                 const sel = document.getElementById('school-ids-select');
                                 const toggle = document.getElementById('select-all-schools-toggle');
                                 const container = document.getElementById('school-selection-container');
                                 
                                 if (sel && toggle && container && sel.options.length > 0) {
                                     const allSelected = Array.from(sel.options).every(o => o.selected);
                                     if (allSelected) {
                                         toggle.checked = true;
                                         container.style.display = 'none';
                                     }
                                 }
                            });
                        </script>
                        {{-- <label>
                            <span>Grade</span>
                            <select name="grade">
                                <option value="">All grades</option>
                                @foreach($grades as $key => $label)
                                    <option value="{{ $key }}" @selected(old('grade', $product->grade) == $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </label> --}}
                        <label id="category-label" style="display: block !important; position: relative; z-index: 100;">
                            <span>Category</span>
                            <select name="category" id="category-select" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; position: relative; z-index: 100;">
                                <option value="">Select Category</option>
                                @foreach($categories as $key => $cat)
                                    @if(is_object($cat))
                                        <option value="{{ $cat->slug }}" data-type="{{ $cat->type }}" @selected(old('category', $product->category) === $cat->slug)>{{ $cat->name }}</option>
                                    @else
                                        {{-- Fallback for array --}}
                                        <option value="{{ $key }}" @selected(old('category', $product->category) === $key)>{{ $cat }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </label>
                        <input type="hidden" name="product_type" value="{{ old('product_type', $product->product_type ?? 'back_to_school') }}">
                        
                        <!-- Product Tag Field -->
                        <!-- Product Tag Field -->
                        <div style="margin-top:20px; border:1px solid #e5e7eb; padding:16px; border-radius:12px; background:#f9fafb;">
                            <style>
                                .custom-toggle { position: relative; display: inline-block; width: 50px; height: 26px; margin-bottom: 0; }
                                .custom-toggle input { opacity: 0; width: 0; height: 0; }
                                .custom-toggle .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px; }
                                .custom-toggle .slider:before { position: absolute; content: ""; height: 18px; width: 18px; left: 4px; bottom: 4px; background-color: white; transition: .4s; border-radius: 50%; }
                                .custom-toggle input:checked + .slider { background-color: #490D59; }
                                .custom-toggle input:focus + .slider { box-shadow: 0 0 1px #490D59; }
                                .custom-toggle input:checked + .slider:before { transform: translateX(24px); }
                            </style>
                            
                            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                                <div>
                                    <h6 style="margin:0; font-weight:600; color:#111827; font-size:14px;">Product Tag</h6>
                                    <p style="margin:2px 0 0; font-size:12px; color:#6b7280;">Show a badge on the product card.</p>
                                </div>
                                <label class="custom-toggle">
                                    <input type="checkbox" name="show_product_tag" id="show_product_tag_toggle" value="1" @checked(old('show_product_tag', $product->show_product_tag ?? false))>
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div id="product_tag_container" style="display: {{ old('show_product_tag', $product->show_product_tag ?? false) ? 'block' : 'none' }}; border-top: 1px solid #e5e7eb; padding-top: 12px; margin-top: 12px;">
                                <label style="display:block; font-size:13px; font-weight:500; color:#374151; margin-bottom:6px;">Tag Text</label>
                                <input type="text" name="product_tag" id="product_tag_input" value="{{ old('product_tag', $product->product_tag) }}" placeholder="e.g. NEW ARRIVAL" style="width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:8px; font-size:14px; color:#111827;">
                            </div>
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const productTypeTags = @json($productTypeTags ?? []);
                                const productTypeInput = document.querySelector('input[name="product_type"]');
                                const tagInput = document.getElementById('product_tag_input');
                                const tagToggle = document.getElementById('show_product_tag_toggle');
                                const tagContainer = document.getElementById('product_tag_container');

                                // Handle Toggle Change
                                tagToggle.addEventListener('change', function() {
                                    if (this.checked) {
                                        tagContainer.style.display = 'block';
                                    } else {
                                        tagContainer.style.display = 'none';
                                    }
                                });

                                // Initial Check
                                if (productTypeInput) {
                                    const type = productTypeInput.value;
                                    // Only set default if field is empty (new product) AND we haven't manually toggled it (though hard to track manual toggle without state).
                                    // Actually, if we are in 'create' mode (no ID), we should pre-fill.
                                    // But blade value="{{ old('product_tag', $product->product_tag) }}" handles persistence.
                                    // So we only need to pre-fill if the input is empty AND it's a new product or we want to force default?
                                    // User said: "defaultly accordingo that product te initially defaulty take that value"
                                    // I'll check if the input is empty.
                                    if (tagInput.value.trim() === '' && productTypeTags[type]) {
                                         tagInput.value = productTypeTags[type];
                                         tagToggle.checked = true;
                                         tagContainer.style.display = 'block';
                                    }
                                }
                            });
                        </script>
                        {{-- <label>
                            <span>Product Type</span>
                            <select name="product_type">
                                @foreach($productTypes as $key => $label)
                                    <option value="{{ $key }}" @selected(old('product_type', $product->product_type ?? 'back_to_school') === $key)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Gender *</span>
                            <select name="gender">
                                <option value="">Select Gender (Default: Unisex)</option>
                                @foreach(['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gender', $product->gender) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                         --}}
                    </div>
                </div>

                <div class="card publish-card">
                    <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-check-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                        Publish
                    </h3>
<!--                     
                    <div style="background:#fff5f5;border:1px solid #fed7d7;border-radius:6px;padding:12px;margin-bottom:16px;">
                        <p style="color:#c53030;font-size:13px;margin:0 0 4px;font-weight:600;">Root Restrictions:</p>
                        <ul style="margin:0;padding-left:20px;color:#c53030;font-size:12px;">
                            <li>No Exchange / Return Option</li>
                            <li>No COD Available</li>
                        </ul>
                    </div> -->

                    <label style="margin-bottom:16px;">
                        <span>Stock status *</span>
                        <select name="stock_status">
                            @foreach(['in_stock' => 'In stock','out_of_stock' => 'Out of stock'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('stock_status', $product->stock_status ?? 'in_stock') === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label style="margin-bottom:16px;">
                        <span>Delivery Duration</span>
                        <input type="text" name="delivery_duration" value="{{ old('delivery_duration', $product->delivery_duration) }}" placeholder="e.g. 2-3 days, 1 week">
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
                        <a href="{{ route('admin.back_to_school.products.index') }}" style="text-align:center;padding:12px;color:#475467;text-decoration:none;">Cancel</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media -->
        <div class="card" style="margin-top:24px;width:100%;">
            <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                <i class="fas fa-images" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                Media
            </h3>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                <label>
                    <span>Featured product image</span>
                    <div class="file-input-wrapper">
                        <input type="file" id="featured_image_input" name="featured_image" accept="image/*">
                    </div>
                    <div id="featured_image_preview" style="margin-top:8px;position:relative;display:inline-block;">
                        @if($product->featured_image)
                            <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}" alt="Featured" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:2px solid #ddd;">
                            <button type="button" class="preview-remove-btn" onclick="removeFeaturedImage()" style="position:absolute;top:4px;right:4px;width:24px;height:24px;background:rgba(220,53,69,0.9);color:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:2;">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                </label>
                <div>
                    <span>Gallery images & videos (Drag & Drop to Reorder)</span>
                    <div id="media-drop-zone" style="border: 2px dashed #d1d5db; border-radius: 8px; padding: 20px; text-align: center; cursor: pointer; transition: border-color 0.3s;" onclick="document.getElementById('gallery-upload-input').click()">
                        <i class="fas fa-cloud-upload-alt" style="font-size: 24px; color: #9ca3af; margin-bottom: 8px;"></i>
                        <p style="margin: 0; color: #6b7280;">Click or Drag files here to upload</p>
                        <input type="file" id="gallery-upload-input" multiple accept="image/*,video/*" style="display: none;">
                        <input type="hidden" name="media_list_modified" value="1">
                        <input type="hidden" name="media_order_ids" id="media_order_ids">
                    </div>
                    
                    <!-- Media Navigation Arrows -->
                    <div style="position: relative; margin-top: 16px; min-height: 120px;">
                        <button type="button" id="media-nav-left" class="media-nav-arrow" style="position: absolute; left: 8px; top: 50%; transform: translateY(-50%); z-index: 10; background: rgba(73, 13, 89, 0.9); color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button type="button" id="media-nav-right" class="media-nav-arrow" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); z-index: 10; background: rgba(73, 13, 89, 0.9); color: white; border: none; border-radius: 50%; width: 36px; height: 36px; cursor: pointer; display: none; align-items: center; justify-content: center; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.2); transition: all 0.3s ease;">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    
                    <!-- Unified Media Grid -->
                        <div id="unifiedMediaPreview" style="display: flex; flex-wrap: nowrap; gap: 12px; overflow-x: auto; overflow-y: hidden; padding-bottom: 8px; -webkit-overflow-scrolling: touch; align-items: flex-start; scroll-behavior: smooth;">
                        <!-- Existing images and videos rendered as media items -->
                        <?php 
                        // Check media_gallery first (primary field), then fallback to media_images
                        $existingGallery = null;
                        if (!empty($product->media_gallery) && is_array($product->media_gallery) && count($product->media_gallery) > 0) {
                            $existingGallery = $product->media_gallery;
                        } elseif (!empty($product->media_images) && is_array($product->media_images) && count($product->media_images) > 0) {
                            $existingGallery = $product->media_images;
                        }
                        
                        if($existingGallery): 
                            $index = 0;
                            foreach($existingGallery as $mediaItem):
                                $finalImgUrl = is_array($mediaItem) ? ($mediaItem[0] ?? '') : $mediaItem;
                                if(is_string($finalImgUrl) && !empty($finalImgUrl)):
                                    $index++;
                                    $isVideo = preg_match('/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv|m3u8)(\?.*)?$/i', $finalImgUrl);
                                    $mediaUrl = \Illuminate\Support\Str::startsWith($finalImgUrl, 'http') ? $finalImgUrl : asset('storage/' . $finalImgUrl);
                        ?>
                                <div class="media-item existing-media" draggable="true">
                                    @if($isVideo)
                                        <div class="video-thumbnail">
                                            <video style="width: 100%; height: 100%; object-fit: cover;" preload="metadata">
                                                <source src="{{ $mediaUrl }}" type="video/mp4">
                                            </video>
                                            <div class="video-play-button"><i class="fas fa-play"></i></div>
                                        </div>
                                    @else
                                        <img src="{{ $mediaUrl }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @endif
                                    <div class="position-number">{{ $index }}</div>
                                    <div class="remove-btn" onclick="this.parentElement.remove(); updatePositionNumbers();">
                                        <i class="fas fa-times"></i>
                                    </div>
                                    <input type="hidden" name="existing_media_images[]" value="{{ $finalImgUrl }}">
                                </div>
                        <?php 
                                endif;
                            endforeach;
                        endif; 
                        ?>
                        </div>
                    </div>
                </div>
                
            </div>

            <div style="margin-top:16px;display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;">
                
            <label style="margin-bottom:16px;">
                    <span>Size Chart (Image)</span>
                    <div class="file-input-wrapper">
                        <input type="file" id="size_chart_path_input" name="size_chart_path" accept="image/*">
                    </div>
                    <div id="size_chart_path_preview" style="margin-top:8px;position:relative;display:inline-block;">
                        @if($product->size_chart_path)
                            <img src="{{ asset('storage/' . $product->size_chart_path) }}" alt="Size Chart" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:2px solid #ddd;">
                            <button type="button" class="preview-remove-btn" onclick="removeSizeChartImage()" style="position:absolute;top:4px;right:4px;width:24px;height:24px;background:rgba(220,53,69,0.9);color:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:2;">
                                <i class="fas fa-times"></i>
                            </button>
                            <a href="{{ asset('storage/' . $product->size_chart_path) }}" target="_blank" style="font-size:12px;color:#490d59;display:block;margin-top:4px;">View current chart</a>
                        @endif
                    </div>
                </label>
                <label style="margin-bottom:16px;">
                    <span>Size Measurement Image</span>
                    <div class="file-input-wrapper">
                        <input type="file" id="size_measurement_image_input" name="size_measurement_image" accept="image/*">
                    </div>
                    <div id="size_measurement_image_preview" style="margin-top:8px;position:relative;display:inline-block;">
                        @if($product->size_measurement_image)
                            <img src="{{ Str::startsWith($product->size_measurement_image, 'http') ? $product->size_measurement_image : asset('storage/' . $product->size_measurement_image) }}" alt="Size Measurement" style="width:120px;height:120px;object-fit:cover;border-radius:8px;border:2px solid #ddd;">
                            <button type="button" class="preview-remove-btn" onclick="removeSizeMeasurementImage()" style="position:absolute;top:4px;right:4px;width:24px;height:24px;background:rgba(220,53,69,0.9);color:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:2;">
                                <i class="fas fa-times"></i>
                            </button>
                            <a href="{{ Str::startsWith($product->size_measurement_image, 'http') ? $product->size_measurement_image : asset('storage/' . $product->size_measurement_image) }}" target="_blank" style="font-size:12px;color:#490d59;display:block;margin-top:4px;">View current image</a>
                        @endif
                    </div>
                </label>
                <label>
                    <span>Measurement Video (YouTube URL)</span>
                    <div style="position: relative;">
                        <input type="url" name="video_url" id="video_url_input" value="{{ old('video_url', $product->video_url) }}" placeholder="https://youtube.com/watch?v=...">
                        @if($product->video_url)
                            <button type="button" onclick="removeVideoUrl()" style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; background: rgba(220,53,69,0.9); color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                <i class="fas fa-times"></i>
                            </button>
                        @endif
                    </div>
                    @if($product->video_url)
                        <div style="margin-top: 8px; padding: 8px; background: #e8f5e9; border-radius: 6px; border: 1px solid #c8e6c9;">
                            <a href="{{ $product->video_url }}" target="_blank" style="font-size: 12px; color: #2e7d32; text-decoration: underline;">
                                <i class="fab fa-youtube" style="margin-right: 4px;"></i> View Current Video
                            </a>
                        </div>
                    @endif
                    <input type="hidden" name="remove_video_url" id="remove_video_url" value="0">
                </label>
                <label style="margin-top: 16px;">
                    <span>Measurement Video (Local File)</span>
                    <label for="video_file_input" class="file-input-wrapper" style="display: inline-block; width: 100%; cursor: pointer;">
                        <input type="file" id="video_file_input" name="video_file" accept="video/*" style="display: none;">
                    </label>
                    <div id="video_file_preview" style="margin-top: 8px;">
                        @if($product->video_file)
                            <div style="display: flex; align-items: center; gap: 12px; padding: 8px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                                <i class="fas fa-video" style="color: #490d59; font-size: 20px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #333; font-size: 14px;">Current Video File</div>
                                    <a href="{{ asset('storage/' . $product->video_file) }}" target="_blank" style="font-size: 12px; color: #490d59; text-decoration: underline;">View/Download Video</a>
                                </div>
                                <button type="button" class="preview-remove-btn" onclick="removeVideoFile()" style="width: 28px; height: 28px; background: rgba(220,53,69,0.9); color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                    </div>
                    <input type="hidden" name="remove_video_file" id="remove_video_file" value="0">
                </label>
            </div>
        </div>
    </form>
    
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        .media-item { position: relative; width: 120px; height: 120px; min-width: 120px; flex-shrink: 0; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; cursor: grab; transition: all 0.3s ease; user-select: none; background: white; }
        .media-item:active { cursor: grabbing; }
        .media-item.dragging { opacity: 0.7; transform: rotate(3deg) scale(1.05); z-index: 1000; box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3); }
        .media-item.drag-over { border: 2px dashed #007bff; transform: scale(1.02); }
        .position-number { position: absolute; bottom: 8px; left: 8px; width: 28px; height: 28px; background: rgba(0, 123, 255, 0.9); color: white; border-radius: 50%; font-weight: bold; display: flex; align-items: center; justify-content: center; pointer-events: none; font-size: 12px; z-index: 2; }
        .remove-btn { position: absolute; top: 8px; right: 8px; width: 24px; height: 24px; background: rgba(220, 53, 69, 0.9); color: white; border-radius: 50%; cursor: pointer; display: none; align-items: center; justify-content: center; z-index: 2; font-size: 12px; }
        .media-item:hover .remove-btn { display: flex; }
        .video-thumbnail { width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center; }
        .video-play-button { width: 40px; height: 40px; background: rgba(255,255,255,0.2); border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; backdrop-filter: blur(2px); transition: background 0.2s; }
        .video-thumbnail:hover .video-play-button { background: rgba(255,255,255,0.4); }
        .preview-remove-btn:hover {
            background: rgba(220, 53, 69, 1) !important;
            transform: scale(1.1);
        }
        
        /* Media Navigation Arrows */
        .media-nav-arrow {
            transition: all 0.3s ease !important;
        }
        
        .media-nav-arrow:hover {
            background: rgba(73, 13, 89, 1) !important;
            transform: translateY(-50%) scale(1.1) !important;
            box-shadow: 0 4px 12px rgba(73, 13, 89, 0.4) !important;
        }
        
        .media-nav-arrow:active {
            transform: translateY(-50%) scale(0.95) !important;
        }
        
        .media-nav-arrow[style*="opacity: 0.5"] {
            cursor: not-allowed !important;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Media Drag & Drop Logic ---
            const dropZone = document.getElementById('media-drop-zone');
            const fileInput = document.getElementById('gallery-upload-input');
            const previewContainer = document.getElementById('unifiedMediaPreview');
            let draggedItem = null;
            // Store all new files that need to be uploaded (for fallback)
            const newFilesMap = new Map(); // Map of hidden input element to file object

            window.updatePositionNumbers = function() {
                const items = previewContainer.querySelectorAll('.media-item');
                const orderIds = [];
                
                items.forEach((item, index) => {
                    const badge = item.querySelector('.position-number');
                    if(badge) badge.textContent = index + 1;
                    
                    // Determine if this is an existing media item or new
                    const isExisting = item.classList.contains('existing-media');
                    orderIds.push(isExisting ? 'existing' : 'new');
                });
                
                // Update the hidden input with the order
                const orderInput = document.getElementById('media_order_ids');
                if(orderInput) {
                    orderInput.value = orderIds.join(',');
                }
            }

            if(fileInput) {
                fileInput.addEventListener('change', (e) => {
                    if(e.target.files && e.target.files.length > 0) {
                        handleFiles(e.target.files);
                        // Reset the input so the same files can be selected again if needed
                        e.target.value = '';
                    }
                });
            }

            if(dropZone) {
                dropZone.addEventListener('dragover', (e) => { e.preventDefault(); dropZone.style.borderColor = '#490d59'; dropZone.style.background = '#f7f2fb'; });
                dropZone.addEventListener('dragleave', (e) => { e.preventDefault(); dropZone.style.borderColor = '#d1d5db'; dropZone.style.background = ''; });
                dropZone.addEventListener('drop', (e) => { e.preventDefault(); dropZone.style.borderColor = '#d1d5db'; dropZone.style.background = ''; handleFiles(e.dataTransfer.files); });
            }

            function handleFiles(files) {
                if(files && files.length > 0) {
                    Array.from(files).forEach(file => createUploadedMediaPreview(file));
                    updatePositionNumbers();
                }
            }

            window.createUploadedMediaPreview = function(file) {
                 const mediaContainer = document.createElement('div');
                 mediaContainer.className = 'media-item';
                 mediaContainer.draggable = true;
                 
                 if (file.type.startsWith('image/')) {
                     const img = document.createElement('img');
                     img.src = URL.createObjectURL(file);
                     Object.assign(img.style, { width: '100%', height: '100%', objectFit: 'cover' });
                     mediaContainer.appendChild(img);
                 } else if (file.type.startsWith('video/')) {
                     const thumb = document.createElement('div');
                     thumb.className = 'video-thumbnail';
                     thumb.innerHTML = '<div class="video-play-button"><i class="fas fa-play"></i></div>';
                     thumb.onclick = (e) => { e.stopPropagation(); playVideo(file, thumb); };
                     mediaContainer.appendChild(thumb);
                 }
                 const posBadge = document.createElement('div');
                 posBadge.className = 'position-number';
                 mediaContainer.appendChild(posBadge);
                const removeBtn = document.createElement('div');
                removeBtn.className = 'remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = (e) => { 
                    e.stopPropagation(); 
                    // Remove file from map when container is removed
                    const hiddenInput = mediaContainer.querySelector('input[type="file"]');
                    if(hiddenInput && newFilesMap.has(hiddenInput)) {
                        newFilesMap.delete(hiddenInput);
                    }
                    mediaContainer.remove(); 
                    updatePositionNumbers(); 
                };
                mediaContainer.appendChild(removeBtn);
                
                // Hidden Input for Form Submission (like merchandise form)
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = 'media_images[]';
                hiddenInput.style.display = 'none';
                hiddenInput.style.visibility = 'hidden';
                hiddenInput.style.position = 'absolute';
                hiddenInput.style.width = '0';
                hiddenInput.style.height = '0';
                
                // Store file reference for fallback
                newFilesMap.set(hiddenInput, file);
                
                try {
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    hiddenInput.files = dataTransfer.files;
                } catch(error) {
                    console.warn('DataTransfer not supported, will use fallback on submit:', error);
                    // File will be added via fallback on form submit
                }
                mediaContainer.appendChild(hiddenInput);
                
                addDragEvents(mediaContainer);
                previewContainer.appendChild(mediaContainer);
            };

            function addDragEvents(item) {
                item.addEventListener('dragstart', () => { draggedItem = item; setTimeout(() => item.classList.add('dragging'), 0); });
                item.addEventListener('dragend', () => { draggedItem = null; item.classList.remove('dragging'); updatePositionNumbers(); });
                item.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (item !== draggedItem) {
                        const bounding = item.getBoundingClientRect();
                        if (e.clientX - (bounding.x + bounding.width / 2) > 0) { item.style.borderRight = '2px solid #007bff'; item.style.borderLeft = ''; }
                        else { item.style.borderLeft = '2px solid #007bff'; item.style.borderRight = ''; }
                    }
                });
                item.addEventListener('dragleave', () => { item.style.borderLeft = ''; item.style.borderRight = ''; });
                item.addEventListener('drop', (e) => {
                    e.preventDefault();
                    item.style.borderLeft = ''; item.style.borderRight = '';
                    if (draggedItem && draggedItem !== item) {
                         const bounding = item.getBoundingClientRect();
                         if (e.clientX - (bounding.x + bounding.width / 2) > 0) item.after(draggedItem); else item.before(draggedItem);
                    }
                });
            }

            function playVideo(file, container) {
                const video = document.createElement('video');
                video.controls = true; video.style.width = '100%'; video.style.height = '100%';
                if (file.name.endsWith('.m3u8') && Hls.isSupported()) {
                    const hls = new Hls(); hls.loadSource(URL.createObjectURL(file)); hls.attachMedia(video);
                } else { video.src = URL.createObjectURL(file); }
                container.innerHTML = ''; container.appendChild(video); video.play();
            }

            document.querySelectorAll('.existing-media').forEach(item => addDragEvents(item));
            
            // Initialize position numbers and order on page load
            updatePositionNumbers();

            // Image Preview Functionality
            function setupImagePreview(inputId, previewId) {
                const input = document.getElementById(inputId);
                const preview = document.getElementById(previewId);
                
                if (input && preview) {
                    input.addEventListener('change', function(e) {
                        const file = e.target.files[0];
                        if (file && file.type.startsWith('image/')) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                // Clear existing content
                                preview.innerHTML = '';
                                
                                // Create wrapper for positioning
                                const wrapper = document.createElement('div');
                                wrapper.style.cssText = 'position:relative;display:inline-block;';
                                
                                // Create image element
                                const img = document.createElement('img');
                                img.src = e.target.result;
                                img.alt = 'Preview';
                                img.style.cssText = 'width:120px;height:120px;object-fit:cover;border-radius:8px;border:2px solid #ddd;';
                                
                                // Create remove button
                                const removeBtn = document.createElement('button');
                                removeBtn.type = 'button';
                                removeBtn.className = 'preview-remove-btn';
                                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                                removeBtn.style.cssText = 'position:absolute;top:4px;right:4px;width:24px;height:24px;background:rgba(220,53,69,0.9);color:white;border:none;border-radius:50%;cursor:pointer;display:flex;align-items:center;justify-content:center;font-size:12px;z-index:2;';
                                removeBtn.onclick = function() {
                                    preview.innerHTML = '';
                                    resetFileInput(inputId);
                                };
                                
                                wrapper.appendChild(img);
                                wrapper.appendChild(removeBtn);
                                preview.appendChild(wrapper);
                            };
                            reader.readAsDataURL(file);
                        } else {
                            // Clear preview if not an image
                            preview.innerHTML = '';
                        }
                    });
                }
            }

            // Helper function to properly reset a file input
            function resetFileInput(inputId) {
                const input = document.getElementById(inputId);
                if (input) {
                    // Simply reset the value - this is the safest approach
                    // This allows the file input to accept new files
                    input.value = '';
                    
                    // Re-setup the preview handler in case it was lost
                    const previewId = inputId.replace('_input', '_preview');
                    setupImagePreview(inputId, previewId);
                }
            }

            // Remove functions for existing images
            window.removeSizeChartImage = function() {
                const preview = document.getElementById('size_chart_path_preview');
                if (preview) preview.innerHTML = '';
                resetFileInput('size_chart_path_input');
            };

            window.removeSizeMeasurementImage = function() {
                const preview = document.getElementById('size_measurement_image_preview');
                if (preview) preview.innerHTML = '';
                resetFileInput('size_measurement_image_input');
            };

            window.removeVideoFile = function() {
                const preview = document.getElementById('video_file_preview');
                const input = document.getElementById('video_file_input');
                const removeInput = document.getElementById('remove_video_file');
                if (preview) preview.innerHTML = '';
                if (input) input.value = '';
                if (removeInput) removeInput.value = '1';
            };

            window.removeVideoUrl = function() {
                const input = document.getElementById('video_url_input');
                const removeInput = document.getElementById('remove_video_url');
                if (input) input.value = '';
                if (removeInput) removeInput.value = '1';
            };

            // Make file input wrapper clickable - simple and reliable
            document.addEventListener('click', function(e) {
                const wrapper = e.target.closest('.file-input-wrapper');
                if (wrapper && e.target.type !== 'file') {
                    e.preventDefault();
                    e.stopPropagation();
                    const input = wrapper.querySelector('input[type="file"]');
                    if (input) {
                        // Use a small timeout to ensure the click is processed
                        setTimeout(() => {
                            input.click();
                        }, 10);
                    }
                }
            }, true); // Use capture phase for better reliability

            // Video file preview
            const videoFileInput = document.getElementById('video_file_input');
            if (videoFileInput) {
                videoFileInput.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.getElementById('video_file_preview');
                    const removeInput = document.getElementById('remove_video_file');
                    
                    if (file) {
                        // Reset remove flag if new file is selected
                        if (removeInput) removeInput.value = '0';
                        
                        const fileSize = (file.size / (1024 * 1024)).toFixed(2);
                        const fileName = file.name;
                        
                        preview.innerHTML = `
                            <div style="display: flex; align-items: center; gap: 12px; padding: 8px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0;">
                                <i class="fas fa-video" style="color: #490d59; font-size: 20px;"></i>
                                <div style="flex: 1;">
                                    <div style="font-weight: 600; color: #333; font-size: 14px;">${fileName}</div>
                                    <div style="font-size: 12px; color: #666;">Size: ${fileSize} MB</div>
                                </div>
                            </div>
                        `;
                    }
                });
            }

            window.removeFeaturedImage = function() {
                const preview = document.getElementById('featured_image_preview');
                if (preview) preview.innerHTML = '';
                resetFileInput('featured_image_input');
            };

            // Setup previews for all three image fields
            setupImagePreview('featured_image_input', 'featured_image_preview');
            setupImagePreview('size_measurement_image_input', 'size_measurement_image_preview');
            setupImagePreview('size_chart_path_input', 'size_chart_path_preview');

            // Media Navigation Arrows
            const mediaNavLeft = document.getElementById('media-nav-left');
            const mediaNavRight = document.getElementById('media-nav-right');
            const mediaPreview = document.getElementById('unifiedMediaPreview');
            
            function updateMediaNavArrows() {
                if (!mediaPreview || !mediaNavLeft || !mediaNavRight) return;
                
                const hasOverflow = mediaPreview.scrollWidth > mediaPreview.clientWidth;
                if (hasOverflow) {
                    mediaNavLeft.style.display = 'flex';
                    mediaNavRight.style.display = 'flex';
                } else {
                    mediaNavLeft.style.display = 'none';
                    mediaNavRight.style.display = 'none';
                }
                
                // Update arrow visibility based on scroll position
                mediaNavLeft.style.opacity = mediaPreview.scrollLeft > 0 ? '1' : '0.5';
                mediaNavRight.style.opacity = (mediaPreview.scrollLeft + mediaPreview.clientWidth) < mediaPreview.scrollWidth ? '1' : '0.5';
            }
            
            if (mediaNavLeft && mediaNavRight && mediaPreview) {
                // Left arrow click
                mediaNavLeft.addEventListener('click', function() {
                    const scrollAmount = 140; // Width of one media item + gap
                    mediaPreview.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
                });
                
                // Right arrow click
                mediaNavRight.addEventListener('click', function() {
                    const scrollAmount = 140; // Width of one media item + gap
                    mediaPreview.scrollBy({ left: scrollAmount, behavior: 'smooth' });
                });
                
                // Update arrows on scroll
                mediaPreview.addEventListener('scroll', updateMediaNavArrows);
                
                // Update arrows on resize
                window.addEventListener('resize', updateMediaNavArrows);
                
                // Initial check
                setTimeout(updateMediaNavArrows, 100);
                
                // Update after media items are added
                const observer = new MutationObserver(updateMediaNavArrows);
                observer.observe(mediaPreview, { childList: true, subtree: true });
            }

            // --- Variants Logic (Original) ---
            const container = document.getElementById('variants-container');
            const addBtn = document.getElementById('add-variant-btn');
            const mainStockInput = document.querySelector('input[name="inventory_stock"]');

            function updateMainStock() {
                const stockInputs = container.querySelectorAll('input[name*="[stock]"]');
                let totalStock = 0;
                if (stockInputs.length > 0) {
                    stockInputs.forEach(input => {
                        totalStock += parseInt(input.value) || 0;
                    });
                }
                if (mainStockInput) {
                    mainStockInput.value = totalStock;
                }
            }

            // Initial check
            updateMainStock();

            // Variant-based pricing toggle functionality
            const variantPricingToggle = document.getElementById('variant-pricing-toggle');
            const mainPricingSection = document.getElementById('main-pricing-section');
            const mainPriceInput = document.getElementById('main-price-input');
            
            function toggleVariantPricing() {
                const isEnabled = variantPricingToggle.checked;
                const variantRows = container.querySelectorAll('.variant-row');
                const variantPriceLabels = container.querySelectorAll('.variant-price-label');
                const variantWeightLabels = container.querySelectorAll('.variant-weight-label');
                const weightField = document.querySelector('.weight-field');
                const mainPriceField = document.querySelector('.main-price-field');
                const inclusiveTaxField = document.querySelector('.inclusive-tax-field');
                
                // Hide/show main price field (but keep pricing section visible for tax fields and inclusive tax)
                if (mainPriceField) {
                    mainPriceField.style.display = isEnabled ? 'none' : 'block';
                }
                
                // Tax fields remain visible and functional always
                // (No need to hide/show tax fields)
                
                // Hide/show main weight field
                if (weightField) {
                    weightField.style.display = isEnabled ? 'none' : 'block';
                }
                
                // Keep inclusive tax field always visible
                if (inclusiveTaxField) {
                    inclusiveTaxField.style.display = 'flex';
                }
                
                // Auto-select "Fabrics" category and hide category field when variant pricing is enabled
                const categoryLabel = document.getElementById('category-label');
                const categorySelect = document.getElementById('category-select');
                if (categorySelect && categoryLabel) {
                    if (isEnabled) {
                        // Set category to 'fabrics' if it exists
                        const fabricsOption = categorySelect.querySelector('option[value="fabrics"]');
                        if (fabricsOption) {
                            categorySelect.value = 'fabrics';
                        }
                        // Hide the category field
                        categoryLabel.style.display = 'none';
                    } else {
                        // Show the category field
                        categoryLabel.style.display = 'block';
                    }
                }
                
                // Make main price optional when variant pricing is enabled
                if (mainPriceInput) {
                    mainPriceInput.required = !isEnabled;
                    if (isEnabled) {
                        mainPriceInput.removeAttribute('required');
                    } else {
                        mainPriceInput.setAttribute('required', 'required');
                    }
                }
                
                // Show/hide price inputs in variants and update labels
                variantPriceLabels.forEach(label => {
                    label.style.display = isEnabled ? 'block' : 'none';
                    const priceInput = label.querySelector('.variant-price-input');
                    const priceLabelText = label.querySelector('.variant-price-label-text');
                    if (priceInput) {
                        priceInput.required = isEnabled;
                    }
                    if (priceLabelText) {
                        priceLabelText.textContent = isEnabled ? 'Price of Fabric' : 'Price *';
                    }
                });
                
                // Show/hide weight inputs in variants and update labels
                variantWeightLabels.forEach(label => {
                    label.style.display = isEnabled ? 'block' : 'none';
                    const weightInput = label.querySelector('.variant-weight-input');
                    const weightLabelText = label.querySelector('.variant-weight-label-text');
                    if (weightInput) {
                        weightInput.required = isEnabled;
                    }
                    if (weightLabelText) {
                        weightLabelText.textContent = isEnabled ? 'Weight of Fabric (kg)' : 'Weight (kg)';
                    }
                });
                
                // Update stock and low stock alert labels
                const stockLabels = container.querySelectorAll('.variant-stock-label-text');
                const lowStockLabels = container.querySelectorAll('.variant-low-stock-label-text');
                stockLabels.forEach(label => {
                    label.textContent = isEnabled ? 'Stock of Fabric' : 'Stock';
                });
                lowStockLabels.forEach(label => {
                    label.textContent = isEnabled ? 'Qty of Fabric' : 'Low Stock Alert';
                });
                
                // Update grid columns for variant rows
                variantRows.forEach(row => {
                    if (isEnabled) {
                        row.style.gridTemplateColumns = '1fr 1fr 1fr 1fr 1fr auto';
                    } else {
                        row.style.gridTemplateColumns = '1fr 1fr 1fr auto';
                    }
                });
            }
            
            // Initialize on page load
            if (variantPricingToggle) {
                variantPricingToggle.addEventListener('change', toggleVariantPricing);
                toggleVariantPricing(); // Initial state
            } else {
                // Ensure category field is visible if variant pricing toggle doesn't exist
                const categoryLabel = document.getElementById('category-label');
                if (categoryLabel) {
                    categoryLabel.style.display = 'block';
                }
            }

            if(addBtn) {
                addBtn.addEventListener('click', function() {
                    const countInput = document.getElementById('add-variant-count');
                    const count = countInput ? parseInt(countInput.value) || 1 : 1;
                    
                    for (let i = 0; i < count; i++) {
                        createVariant();
                    }
                    
                    // Reset count to 1 after adding
                    if (countInput) countInput.value = 1;
                });
            }

            function createVariant() {
                const index = new Date().getTime() + Math.floor(Math.random() * 1000); // Unique ID
                const isVariantPricing = variantPricingToggle ? variantPricingToggle.checked : false;
                
                const row = document.createElement('div');
                row.className = 'variant-row';
                // Always show weight field for variants
                row.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;';
                
                // Auto-increment size, copy weight and stock logic
                // Default values: Size=22, Weight=1, Stock=100, Low Stock Alert=5
                let newSize = '22';
                let lastWeight = '1';
                let lastStock = '100';
                
                const lastRow = container.querySelector('.variant-row:last-child');
                if (lastRow) {
                    const lastSizeInput = lastRow.querySelector('input[name*="[option]"]');
                    if (lastSizeInput && lastSizeInput.value) {
                        const sizeNum = parseFloat(lastSizeInput.value);
                        // Check if it's a valid number
                        if (!isNaN(sizeNum)) {
                             newSize = sizeNum + 2;
                        } else {
                            // If not a number, use default
                            newSize = '22';
                        }
                    }
                    
                    // Get weight
                    const lastWeightInput = lastRow.querySelector('input[name*="[weight]"]');
                    if (lastWeightInput && lastWeightInput.value) {
                        lastWeight = lastWeightInput.value;
                    } else {
                        lastWeight = '1';
                    }
                    
                    // Get stock
                    const lastStockInput = lastRow.querySelector('input[name*="[stock]"]');
                    if (lastStockInput && lastStockInput.value) {
                        lastStock = lastStockInput.value;
                    } else {
                        lastStock = '100';
                    }
                }

                row.innerHTML = `
                    <label>
                        <span style="font-size:12px;">Size / Option</span>
                        <input type="text" name="variants[${index}][option]" value="${newSize}" placeholder="e.g. S, M, 10" required>
                    </label>
                    ${isVariantPricing ? `
                    <label class="variant-price-label" style="display:block;">
                        <span class="variant-price-label-text" style="font-size:12px;">Price of Fabric</span>
                        <input type="number" name="variants[${index}][price]" min="0" step="0.01" placeholder="0.00" class="variant-price-input" required>
                    </label>
                    ` : ''}
                    <label class="variant-weight-label" style="display:block;">
                        <span class="variant-weight-label-text" style="font-size:12px;">Weight (kg)</span>
                        <input type="number" name="variants[${index}][weight]" value="${lastWeight}" min="0" step="0.01" placeholder="0.00" class="variant-weight-input">
                    </label>
                    <label>
                        <span class="variant-stock-label-text" style="font-size:12px;">${isVariantPricing ? 'Stock of Fabric' : 'Stock'}</span>
                        <input type="number" name="variants[${index}][stock]" value="${lastStock}" placeholder="Qty" min="0" class="variant-stock">
                    </label>
                    <label>
                        <span class="variant-low-stock-label-text" style="font-size:12px;">${isVariantPricing ? 'Qty of Fabric' : 'Low Stock Alert'}</span>
                        <div style="display:flex; gap:5px;">
                            <input type="number" name="variants[${index}][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5" class="variant-low-stock">
                        </div>
                    </label>
                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
                updateMainStock();
            }
            if(container) {
                container.addEventListener('click', function(e) {
                    if (e.target.closest('.btn-remove-variant')) {
                        e.target.closest('.variant-row').remove();
                        updateMainStock();
                    }
                    
                    // Handle Apply to All button click
                    const applyBtn = e.target.closest('.apply-all-btn');
                    if (applyBtn) {
                        const fieldType = applyBtn.dataset.field; // 'weight', 'stock', or 'low_stock'
                        const variantRow = applyBtn.closest('.variant-row');
                        let selector = '';
                        if (fieldType === 'weight') selector = '.variant-weight-input';
                        else if (fieldType === 'stock') selector = '.variant-stock';
                        else if (fieldType === 'low_stock') selector = '.variant-low-stock';

                        const input = variantRow.querySelector(selector);
                        
                        if (input && input.value) {
                            const value = input.value;
                            const allRows = container.querySelectorAll('.variant-row');
                            
                            allRows.forEach(row => {
                                if (row === variantRow) return; // Skip source row
                                
                                const targetInput = row.querySelector(selector);
                                if (targetInput) {
                                    targetInput.value = value;
                                }
                            });
                            
                            // Update main stock if stock was changed
                            if (fieldType === 'stock') {
                                updateMainStock();
                            }
                            
                            // Visual feedback (optional)
                            applyBtn.innerHTML = '<i class="fas fa-check" style="color:#059669; font-size:12px;"></i>';
                            setTimeout(() => {
                                applyBtn.innerHTML = '<i class="fas fa-angle-double-down" style="color:#4b5563; font-size:12px;"></i>';
                            }, 1000);
                        }
                    }
                });

                container.addEventListener('input', function(e) {
                    if (e.target.classList.contains('variant-stock')) {
                        updateMainStock();
                    }
                });
                
            }
        });
    </script>
@endpush

<!-- Add this enhanced CSS to your stylesheet or in a <style> tag -->
<style>

    /* Force native/default select UI */
.grade-from-select,
.grade-to-select {
    appearance: auto !important;
    -webkit-appearance: auto !important;
    -moz-appearance: auto !important;

    background: initial !important;
    border-radius: 4px !important;
    padding: 6px 8px !important;
    box-shadow: none !important;
}
.grade-pricing-range-row select {
    all: revert;
}
    /* Enhanced Card Styling */
    .card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: box-shadow 0.3s ease;
        position: relative;
        z-index: 1;
    }
    
    /* Organization card needs higher z-index for dropdown */
    .organization-card {
        z-index: 10 !important;
        overflow: visible !important;
    }
    
    /* Publish card should be below Organization card */
    .publish-card {
        z-index: 1 !important;
    }
    
    .card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    /* Enhanced Input Styling */
    input[type="text"],
    input[type="number"],
    input[type="url"],
    select,
    textarea {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        transition: all 0.2s ease;
        background: white;
    }
    
    /* Fix z-index for category dropdown to appear above other cards */
    #category-label {
        position: relative;
        z-index: 100 !important;
    }
    
    #category-select {
        position: relative;
        z-index: 100 !important;
    }
    
    /* Ensure select dropdown options appear above other elements */
    #category-select:focus {
        z-index: 1000 !important;
    }
    
    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="url"]:focus,
    select:focus,
    textarea:focus {
        outline: none;
        border-color: #490d59;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
    }
    
    input[type="text"]:hover,
    input[type="number"]:hover,
    input[type="url"]:hover,
    select:hover,
    textarea:hover {
        border-color: #9ca3af;
    }

    /* Label Styling */
    label {
        display: block;
    }
    
    label > span {
        display: block;
        font-size: 13px;
        font-weight: 500;
        color: #374151;
        margin-bottom: 6px;
    }

    /* Section Headers with Icons */
    .card h3 {
        display: flex;
        align-items: center;
        gap: 10px;
        margin: 0 0 20px;
        color: #111827;
        font-size: 16px;
        font-weight: 600;
    }
    
    .card h3 i {
        color: #490d59;
        background: linear-gradient(135deg, #f7f2fb 0%, #ede7f3 100%);
        padding: 10px;
        border-radius: 10px;
        font-size: 16px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .card h4 {
        color: #374151;
        font-size: 15px;
        font-weight: 600;
        margin: 0 0 16px;
        padding-bottom: 8px;
        border-bottom: 2px solid #f3f4f6;
    }

    /* Button Enhancements */
    .btn-back-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: white;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        color: #374151;
        font-size: 14px;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .btn-back-outline:hover {
        background: #f9fafb;
        border-color: #490d59;
        color: #490d59;
        transform: translateY(-1px);
    }

    /* Primary Buttons */
    button[type="submit"],
    button[style*="background:#490d59"],
    #add-variant-btn,
    #add-grade-range-btn {
        transition: all 0.2s ease;
        font-weight: 600;
        position: relative;
        overflow: hidden;
    }
    
    button[type="submit"]:hover,
    button[style*="background:#490d59"]:hover,
    #add-variant-btn:hover,
    #add-grade-range-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(73, 13, 89, 0.3);
    }
    
    button[type="submit"]:active,
    button[style*="background:#490d59"]:active {
        transform: translateY(0);
    }

    /* Variant Row Enhancements */
    .variant-row {
        background: #f9fafb;
        padding: 16px;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }
    
    .variant-row:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    /* Grade Pricing Range Row */
    .grade-pricing-range-row {
        background: white;
        padding: 20px;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.2s ease;
        margin-bottom: 16px;
    }
    
    .grade-pricing-range-row:hover {
        border-color: #490d59;
        box-shadow: 0 4px 12px rgba(73, 13, 89, 0.08);
        transform: translateY(-2px);
    }
    
    /* Grade pricing row labels */
    .grade-pricing-range-row label {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    
    .grade-pricing-range-row label > span {
        font-size: 13px;
        font-weight: 600;
        color: #374151;
        display: block;
    }
    
    /* Optional text styling */
    .grade-pricing-range-row label > span span {
        font-size: 12px;
        font-weight: 400;
        color: #6b7280;
        font-style: italic;
    }
    
    /* Grade pricing select and input styling */
    .grade-pricing-range-row select,
    .grade-pricing-range-row input[type="number"] {
        width: 100%;
        padding: 10px 14px;
        border: 1.5px solid #d1d5db;
        border-radius: 8px;
        font-size: 14px;
        background: #fafbfc;
        transition: all 0.2s ease;
    }
    
    .grade-pricing-range-row select:focus,
    .grade-pricing-range-row input[type="number"]:focus {
        border-color: #490d59;
        background: white;
        box-shadow: 0 0 0 3px rgba(73, 13, 89, 0.1);
        outline: none;
    }
    
    .grade-pricing-range-row select:hover,
    .grade-pricing-range-row input[type="number"]:hover {
        border-color: #9ca3af;
        background: white;
    }
    
    /* Remove button in grade pricing row */
    .grade-pricing-range-row .remove-grade-range-btn {
        padding: 10px 14px;
        background: #fee2e2;
        color: #dc2626;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        transition: all 0.2s ease;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .grade-pricing-range-row .remove-grade-range-btn:hover {
        background: #fecaca;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2);
    }

    /* Remove Button Styling */
    .btn-remove-variant,
    .remove-grade-range-btn {
        transition: all 0.2s ease;
        border-radius: 8px;
    }
    
    .btn-remove-variant:hover,
    .remove-grade-range-btn:hover {
        background: #fee2e2 !important;
        transform: scale(1.05);
    }

    /* File Input Wrapper */
    .file-input-wrapper {
        position: relative;
        overflow: visible;
        display: inline-block;
        width: 100%;
        cursor: pointer;
    }
    
    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
        opacity: 0;
        width: 1px;
        height: 1px;
        pointer-events: none;
        z-index: -1;
    }
    
    .file-input-wrapper::before {
        content: 'Choose File';
        display: inline-block;
        background: linear-gradient(135deg, #490d59 0%, #6b1179 100%);
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        pointer-events: none;
    }
    
    .file-input-wrapper:hover::before {
        background: linear-gradient(135deg, #6b1179 0%, #490d59 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(73, 13, 89, 0.3);
    }
    
    

    /* Media Drop Zone Enhancement */
    #media-drop-zone {
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 24px;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #fafbfc 0%, #f9fafb 100%);
    }
    
    #media-drop-zone:hover {
        border-color: #490d59;
        background: linear-gradient(135deg, #f7f2fb 0%, #ede7f3 100%);
        transform: scale(1.01);
    }
    
    #media-drop-zone i {
        font-size: 32px;
        color: #490d59;
        margin-bottom: 12px;
        display: block;
    }
    
    #media-drop-zone p {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        font-weight: 500;
    }

    /* Toggle Switch Wrapper */
    label:has(input[type="checkbox"]) {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        user-select: none;
        margin: 0 !important;
    }
    
    /* Hide default checkbox */
    input[type="checkbox"] {
        appearance: none;
        -webkit-appearance: none;
        width: 0;
        height: 0;
        position: absolute;
        opacity: 0;
    }
    
    /* Toggle switch container */
    input[type="checkbox"] + span {
        position: relative;
        display: flex;
        align-items: center;
        padding-left: 0;
    }
    
    /* Create toggle background track */
    input[type="checkbox"] + span::before {
        content: '';
        display: inline-block;
        width: 50px;
        height: 26px;
        background: #d1d5db;
        border-radius: 13px;
        margin-right: 12px;
        position: relative;
        transition: background 0.3s ease;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
        flex-shrink: 0;
    }
    
    /* Toggle knob/circle */
    input[type="checkbox"] + span::after {
        content: '';
        position: absolute;
        width: 22px;
        height: 22px;
        background: white;
        border-radius: 50%;
        top: 50%;
        left: 2px;
        transform: translateY(-50%);
        transition: left 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    /* Checked state - violet/purple color */
    input[type="checkbox"]:checked + span::before {
        background: linear-gradient(135deg, #490d59 0%, #6b1179 100%);
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.2), 0 0 8px rgba(73, 13, 89, 0.3);
    }
    
    input[type="checkbox"]:checked + span::after {
        left: 26px;
    }
    
    /* Hover effects */
    label:has(input[type="checkbox"]):hover input[type="checkbox"]:not(:checked) + span::before {
        background: #b5b8bd;
    }
    
    label:has(input[type="checkbox"]):hover input[type="checkbox"]:checked + span::before {
        background: linear-gradient(135deg, #5a0f6a 0%, #7d1a8a 100%);
    }
    
    /* Focus state for accessibility */
    input[type="checkbox"]:focus-visible + span::before {
        outline: 2px solid #490d59;
        outline-offset: 2px;
    }
    
    /* Ensure text doesn't wrap oddly */
    input[type="checkbox"] + span {
        font-size: 13px;
        color: #475467;
        font-weight: 500;
        white-space: nowrap;
    }

    /* Alert/Info Box */
    .info-box {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border-left: 4px solid #3b82f6;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #1e40af;
    }
    
    .info-box strong {
        color: #1e3a8a;
    }

    /* Success State */
    .success-message {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
        border-left: 4px solid #22c55e;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 16px;
        font-size: 13px;
        color: #15803d;
    }

    /* Loading State */
    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
        backdrop-filter: blur(4px);
    }
    
    .spinner {
        width: 48px;
        height: 48px;
        border: 4px solid #f3f4f6;
        border-top-color: #490d59;
        border-radius: 50%;
        animation: spin 0.8s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    /* Responsive Grid Improvements */
    @media (max-width: 1024px) {
        div[style*="grid-template-columns:2fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }

    /* Smooth Transitions */
    * {
        transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
        transition-duration: 0.2s;
        transition-timing-function: ease;
    }
    
    button,
    a,
    input,
    select,
    textarea {
        transition-duration: 0.2s;
    }

    /* Enhanced Placeholder Styling */
    ::placeholder {
        color: #9ca3af;
        opacity: 1;
    }

    /* Focus Visible for Accessibility */
    *:focus-visible {
        outline: 2px solid #490d59;
        outline-offset: 2px;
    }

    /* Improved Textarea */
    textarea {
        resize: vertical;
        min-height: 100px;
        font-family: inherit;
    }

    /* Badge/Tag Styling */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 10px;
        background: linear-gradient(135deg, #f7f2fb 0%, #ede7f3 100%);
        color: #490d59;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    /* Image Preview Enhancement */
    img[style*="border-radius:8px"] {
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    img[style*="border-radius:8px"]:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    }

    /* Enhanced Section Spacing */
    .card + .card {
        margin-top: 24px;
    }

    /* Improved Number Input Buttons */
    input[type="number"]::-webkit-inner-spin-button,
    input[type="number"]::-webkit-outer-spin-button {
        opacity: 1;
    }
</style>

<!-- Add this JavaScript for enhanced interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add ripple effect to buttons
    document.querySelectorAll('button, .btn-back-outline').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                position: absolute;
                width: ${size}px;
                height: ${size}px;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.6);
                left: ${x}px;
                top: ${y}px;
                pointer-events: none;
                transform: scale(0);
                animation: ripple 0.6s ease-out;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
    
    // Add animation to form sections on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '0';
                entry.target.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    entry.target.style.transition = 'all 0.5s ease';
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }, 100);
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    
    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
    
    // Show loading overlay on form submit
    document.querySelector('form')?.addEventListener('submit', function(e) {
        console.log('[BTS Form Submit] Form submission started');
        
        // Count existing and new media items
        const existingMedia = previewContainer?.querySelectorAll('.existing-media') || [];
        const newMediaItems = previewContainer?.querySelectorAll('.media-item:not(.existing-media)') || [];
        const allFileInputs = document.querySelectorAll('input[name="media_images[]"]');
        
        console.log('[BTS Form Submit] Media items count:', {
            existing: existingMedia.length,
            new: newMediaItems.length,
            total_file_inputs: allFileInputs.length,
            newFilesMap_size: newFilesMap?.size || 0
        });
        
        // Log each file input
        allFileInputs.forEach((input, index) => {
            console.log(`[BTS Form Submit] File input ${index}:`, {
                has_files: input.files && input.files.length > 0,
                file_count: input.files?.length || 0,
                file_names: input.files ? Array.from(input.files).map(f => f.name) : []
            });
        });
        
        // Fallback: Collect all files from hidden inputs that might not have files set
        // This ensures files are submitted even if DataTransfer didn't work
        if(previewContainer && fileInput && newFilesMap && newFilesMap.size > 0) {
            const filesToAdd = [];
            
            newMediaItems.forEach((item, index) => {
                const hiddenInput = item.querySelector('input[type="file"]');
                if(hiddenInput) {
                    // Check if the hidden input has files (DataTransfer worked)
                    if(hiddenInput.files && hiddenInput.files.length > 0) {
                        // DataTransfer worked, file is already attached
                        console.log(`[BTS Form Submit] File ${index}: Using DataTransfer file`, hiddenInput.files[0].name);
                        filesToAdd.push(hiddenInput.files[0]);
                    } else if(newFilesMap.has(hiddenInput)) {
                        // DataTransfer didn't work, use stored file reference
                        const file = newFilesMap.get(hiddenInput);
                        console.log(`[BTS Form Submit] File ${index}: Using fallback file`, file.name);
                        filesToAdd.push(file);
                    } else {
                        console.warn(`[BTS Form Submit] File ${index}: No file found in hidden input or map`);
                    }
                }
            });
            
            console.log('[BTS Form Submit] Files to add:', filesToAdd.length);
            
            // If we have files to add and the main file input is empty or needs updating
            if(filesToAdd.length > 0) {
                try {
                    const dt = new DataTransfer();
                    filesToAdd.forEach(file => {
                        dt.items.add(file);
                    });
                    if(dt.files.length > 0) {
                        // Append to existing files if any, or replace
                        fileInput.files = dt.files;
                        console.log('[BTS Form Submit] Successfully set files on main input:', dt.files.length);
                    }
                } catch(error) {
                    console.error('[BTS Form Submit] Error setting files on submit:', error);
                }
            }
        } else {
            console.log('[BTS Form Submit] No new files to process');
        }
        
        // Final check before submit
        const finalFileInputs = document.querySelectorAll('input[name="media_images[]"]');
        let totalFiles = 0;
        finalFileInputs.forEach(input => {
            if (input.files && input.files.length > 0) {
                totalFiles += input.files.length;
            }
        });
        console.log('[BTS Form Submit] Total files being submitted:', totalFiles);
        
        const overlay = document.createElement('div');
        overlay.className = 'loading-overlay';
        overlay.innerHTML = '<div class="spinner"></div>';
        document.body.appendChild(overlay);
    });
    
    // Auto-save draft indicator (optional)
    let autoSaveTimeout;
    document.querySelectorAll('input, select, textarea').forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(autoSaveTimeout);
            autoSaveTimeout = setTimeout(() => {
                console.log('Auto-save triggered (implement backend integration)');
            }, 2000);
        });
    });
});

// Add ripple animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>  
@endsection
