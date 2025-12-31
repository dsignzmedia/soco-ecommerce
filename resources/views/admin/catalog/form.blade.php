
@extends('admin.layouts.base')

@php
    $isEdit = isset($mode) && $mode === 'edit';
@endphp

@section('title', ($isEdit ? 'Edit' : 'Add') . ' Product | The Skool Store')
@section('page_heading', ($isEdit ? 'Edit' : 'Add') . ' Product')
@section('page_subheading', 'Curate listings with full catalog metadata')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">

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
                    
                    <label>
                        <span>Description</span>
                        <textarea name="description" rows="5" placeholder="Rich text / marketing copy...">{{ old('description', $product->description) }}</textarea>
                    </label>
                    

                </div>

                <!-- Pricing & Product Variants -->
                <div class="card">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
                        <h3 style="margin:0;color:#111827;display:flex;align-items:center;gap:10px;">
                        <i class="fas fa-tag" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                            Pricing & Product Variants
                    </h3>
                        {{--<label style="display:flex;align-items:center;gap:8px;margin:0;cursor:pointer;">
                            <input type="checkbox" id="variant-pricing-toggle" name="variant_based_pricing" value="1" @checked(old('variant_based_pricing', $product->category === 'fabrics' || $product->category === 'Fabrics')) style="width:auto;">
                            <span style="font-size:13px;color:#475467;">Variant-based pricing (Fabric)</span>
                        </label>--}}
                    </div>
                    
                    <!-- Pricing Section -->
                    <div id="main-pricing-section" style="margin-bottom:24px;padding-bottom:24px;border-bottom:1px solid #e5e7eb;">
                        <h4 style="margin:0 0 16px;color:#374151;font-size:14px;font-weight:600;">Pricing</h4>
                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                            <label class="main-price-field">
                                <span>Price <span id="price-required-indicator">*</span></span>
                                <input type="number" id="main-price-input" name="price_regular" min="0" step="0.01" value="{{ old('price_regular', $product->price_regular) }}">
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

                <!-- Grade-wise Pricing Section -->
                <div class="card" style="margin-bottom:24px;">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                        <h4 style="margin:0;color:#374151;font-size:14px;font-weight:600;">Grade-wise Pricing (Optional)</h4>
                        <label style="display:flex;align-items:center;gap:8px;margin:0;cursor:pointer;">
                            <input type="checkbox" id="grade-pricing-toggle" name="enable_grade_pricing" value="1" @checked(old('enable_grade_pricing', $product->gradePricing && $product->gradePricing->count() > 0 ? true : (old('enable_grade_pricing') === '0' || old('enable_grade_pricing') === false ? false : true))) style="width:auto;">
                            <span style="font-size:13px;color:#475467;">Enable grade-wise pricing</span>
                        </label>
                    </div>
                    
                    <div id="grade-pricing-section" style="display:{{ old('enable_grade_pricing', $product->gradePricing && $product->gradePricing->count() > 0 ? 'block' : (old('enable_grade_pricing') === '0' || old('enable_grade_pricing') === false ? 'none' : 'block')) }};">
                        <p style="margin:0 0 16px;color:#6b7280;font-size:13px;">
                            Set price ranges for different grade groups. Add multiple ranges as needed.<br>
                            <strong style="color:#374151;">Tip:</strong> To set pricing for a single grade, fill only "From Grade" and leave "To Grade" empty.
                        </p>
                        
                        <div id="grade-pricing-ranges-container" style="display:flex;flex-direction:column;gap:12px;">
                            @php
                                // Convert existing grade pricing to ranges for display
                                $existingRanges = [];
                                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                                
                                if ($product->gradePricing && $product->gradePricing->count() > 0) {
                                    $pricingByPrice = [];
                                    foreach ($product->gradePricing as $gp) {
                                        if (!isset($pricingByPrice[$gp->price])) {
                                            $pricingByPrice[$gp->price] = [];
                                        }
                                        $pricingByPrice[$gp->price][] = $gp->grade;
                                    }
                                    
                                    foreach ($pricingByPrice as $price => $gradeList) {
                                        // Sort by grade order, not alphabetically
                                        usort($gradeList, function($a, $b) use ($gradeOrder) {
                                            $aIndex = array_search($a, $gradeOrder);
                                            $bIndex = array_search($b, $gradeOrder);
                                            return ($aIndex !== false && $bIndex !== false) ? $aIndex - $bIndex : 0;
                                        });
                                        
                                        // Group consecutive grades into ranges
                                        if (!empty($gradeList)) {
                                            $currentRange = ['from' => $gradeList[0], 'to' => $gradeList[0]];
                                            foreach ($gradeList as $i => $grade) {
                                                if ($i === 0) continue;
                                                $prevIndex = array_search($gradeList[$i-1], $gradeOrder);
                                                $currIndex = array_search($grade, $gradeOrder);
                                                if ($currIndex === $prevIndex + 1) {
                                                    // Consecutive, extend range
                                                    $currentRange['to'] = $grade;
                                                } else {
                                                    // Not consecutive, save current range and start new
                                                    $existingRanges[] = array_merge($currentRange, ['price' => $price]);
                                                    $currentRange = ['from' => $grade, 'to' => $grade];
                                                }
                                            }
                                            $existingRanges[] = array_merge($currentRange, ['price' => $price]);
                                        }
                                    }
                                }
                                
                                // If no existing ranges, show one empty range
                                if (empty($existingRanges) && old('grade_pricing_ranges')) {
                                    $existingRanges = old('grade_pricing_ranges');
                                } elseif (empty($existingRanges)) {
                                    $existingRanges = [['from' => '', 'to' => '', 'price' => '']];
                                }
                            @endphp
                            
                            @foreach($existingRanges as $index => $range)
                                <div class="grade-pricing-range-row" style="display:grid;grid-template-columns:1fr 1fr 150px auto;gap:12px;align-items:end;padding:12px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;">
                                    <label>
                                        <span style="font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;">From Grade *</span>
                                        <select name="grade_pricing_ranges[{{ $index }}][from]" class="grade-from-select" required>
                                            <option value="">Select</option>
                                            <option value="Pre-KG" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == 'Pre-KG')>Pre-KG</option>
                                            <option value="LKG" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == 'LKG')>LKG</option>
                                            <option value="UKG" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == 'UKG')>UKG</option>
                                            <option value="1" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '1')>Class 1</option>
                                            <option value="2" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '2')>Class 2</option>
                                            <option value="3" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '3')>Class 3</option>
                                            <option value="4" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '4')>Class 4</option>
                                            <option value="5" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '5')>Class 5</option>
                                            <option value="6" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '6')>Class 6</option>
                                            <option value="7" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '7')>Class 7</option>
                                            <option value="8" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '8')>Class 8</option>
                                            <option value="9" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '9')>Class 9</option>
                                            <option value="10" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '10')>Class 10</option>
                                            <option value="11" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '11')>Class 11</option>
                                            <option value="12" @selected(old('grade_pricing_ranges.'.$index.'.from', $range['from'] ?? '') == '12')>Class 12</option>
                                        </select>
                                    </label>
                                    <label>
                                        <span style="font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;">To Grade <span style="color:#6b7280;font-weight:400;">(Optional)</span></span>
                                        <select name="grade_pricing_ranges[{{ $index }}][to]" class="grade-to-select">
                                            <option value="">Leave empty for single grade</option>
                                            <option value="Pre-KG" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == 'Pre-KG')>Pre-KG</option>
                                            <option value="LKG" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == 'LKG')>LKG</option>
                                            <option value="UKG" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == 'UKG')>UKG</option>
                                            <option value="1" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '1')>Class 1</option>
                                            <option value="2" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '2')>Class 2</option>
                                            <option value="3" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '3')>Class 3</option>
                                            <option value="4" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '4')>Class 4</option>
                                            <option value="5" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '5')>Class 5</option>
                                            <option value="6" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '6')>Class 6</option>
                                            <option value="7" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '7')>Class 7</option>
                                            <option value="8" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '8')>Class 8</option>
                                            <option value="9" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '9')>Class 9</option>
                                            <option value="10" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '10')>Class 10</option>
                                            <option value="11" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '11')>Class 11</option>
                                            <option value="12" @selected(old('grade_pricing_ranges.'.$index.'.to', $range['to'] ?? '') == '12')>Class 12</option>
                                        </select>
                                    </label>
                                    <label>
                                        <span style="font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;">Price (₹)</span>
                                        <input type="number" name="grade_pricing_ranges[{{ $index }}][price]" value="{{ old('grade_pricing_ranges.'.$index.'.price', $range['price'] ?? '') }}" min="0" step="0.01" required placeholder="0.00" style="width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;">
                                    </label>
                                    <div style="display:flex;align-items:end;padding-bottom:4px;">
                                        <button type="button" class="remove-grade-range-btn" style="padding:8px 12px;background:#fee2e2;color:#b42318;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <button type="button" id="add-grade-range-btn" style="margin-top:12px;padding:8px 16px;background:#490d59;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;">
                            <i class="fas fa-plus" style="margin-right:6px;"></i> Add Grade Range
                        </button>
                    </div>
                </div>

                <!-- Metadata Hidden Inputs -->
                <input type="hidden" name="inventory_stock" value="{{ old('inventory_stock', $product->inventory_stock ?? 0) }}">
                <input type="hidden" name="low_stock_threshold" value="{{ old('low_stock_threshold', $product->low_stock_threshold ?? 5) }}">

                    <!-- Product Variants Section -->
                    <div>
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;">
                            <h4 style="margin:0;color:#374151;font-size:14px;font-weight:600;">Product Variants</h4>
                            <button type="button" id="apply-weight-all-btn" style="display:none;padding:6px 12px;background:#490d59;color:white;border:none;border-radius:6px;font-size:12px;cursor:pointer;">
                                <i class="fas fa-copy"></i> Apply Weight to All Sizes
                            </button>
                        </div>
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
                                        <input type="number" name="variants[{{$index}}][weight]" min="0" step="0.01" value="{{ $variant['weight'] ?? '' }}" placeholder="0.00" class="variant-weight-input">
                                    </label>
                                    <label>
                                        <span class="variant-stock-label-text" style="font-size:12px;">Stock</span>
                                        <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant['stock'] }}" placeholder="Qty" min="0" class="variant-stock">
                                    </label>
                                    <label>
                                        <span class="variant-low-stock-label-text" style="font-size:12px;">Low Stock Alert</span>
                                        <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant['low_stock_threshold'] ?? 5 }}" placeholder="Alert Qty" min="0">
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
                                        <span style="font-size:12px;">Price *</span>
                                        <input type="number" name="variants[{{$index}}][price]" min="0" step="0.01" value="{{ $variant->price ?? '' }}" placeholder="0.00" class="variant-price-input">
                                    </label>
                                    <label class="variant-weight-label" style="display:none;">
                                        <span style="font-size:12px;">Weight (kg)</span>
                                        <input type="number" name="variants[{{$index}}][weight]" min="0" step="0.01" value="{{ $variant->weight ?? '' }}" placeholder="0.00" class="variant-weight-input">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Stock</span>
                                        <input type="number" name="variants[{{$index}}][stock]" value="{{ $variant->stock }}" placeholder="Qty" min="0" class="variant-stock">
                                    </label>
                                    <label>
                                        <span style="font-size:12px;">Low Stock Alert</span>
                                        <input type="number" name="variants[{{$index}}][low_stock_threshold]" value="{{ $variant->low_stock_threshold }}" placeholder="Alert Qty" min="0">
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
                             <div class="variant-row" style="display:grid;grid-template-columns:1fr 1fr 1fr 1fr auto;gap:12px;margin-bottom:12px;align-items:end;">
                                <label>
                                    <span style="font-size:12px;">Size / Option</span>
                                    <input type="text" name="variants[0][option]" placeholder="e.g. S, M, 10">
                                </label>
                                <label class="variant-price-label" style="display:none;position:absolute;visibility:hidden;">
                                    <span style="font-size:12px;">Price *</span>
                                    <input type="number" name="variants[0][price]" min="0" step="0.01" placeholder="0.00" class="variant-price-input">
                                </label>
                                <label class="variant-weight-label" style="display:block;">
                                    <span style="font-size:12px;">Weight (kg)</span>
                                    <input type="number" name="variants[0][weight]" min="0" step="0.01" placeholder="0.00" class="variant-weight-input">
                                </label>
                                <label>
                                    <span style="font-size:12px;">Stock</span>
                                    <input type="number" name="variants[0][stock]" placeholder="Qty" min="0" class="variant-stock">
                                </label>
                                <label>
                                    <span style="font-size:12px;">Low Stock Alert</span>
                                    <input type="number" name="variants[0][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5">
                                </label>
                                <div style="display:flex;align-items:end;padding-bottom:10px;">
                                    <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    <button type="button" id="add-variant-btn" style="margin-top:10px;background:#f9fafb;border:1px dashed #d0d5dd;border-radius:8px;width:100%;padding:10px;color:#475467;font-size:13px;cursor:pointer;">
                        + Add another size/variant
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
                        <label>
                            <span>School *</span>
                            <select name="school_id" required>
                                <option value="">Select school</option>
                                @foreach($schools as $school)
                                    <option value="{{ $school->id }}" @selected(old('school_id', $product->school_id) == $school->id)>{{ $school->name }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label id="grade-select-label">
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
                        <label id="category-label" style="display: block !important; position: relative; z-index: 100;">
                            <span>Category</span>
                            <select name="category" id="category-select" style="width: 100%; padding: 10px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; background: white; position: relative; z-index: 100;">
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
                                @foreach(['male' => 'Male', 'female' => 'Female', 'unisex' => 'Unisex'] as $value => $label)
                                    <option value="{{ $value }}" @selected(old('gender', $product->gender) === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </label>
                        <label>
                            <span>Tag name</span>
                            <input type="text" name="tag_name" value="{{ old('tag_name', $product->tag_name) }}" placeholder="Eg: Bestseller">
                        </label>
                    </div>
                </div>

                <div class="card publish-card">
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
                        <a href="{{ route('master.admin.catalog.index') }}" style="text-align:center;padding:12px;color:#475467;text-decoration:none;">Cancel</a>
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
                                @if($product->media_images)
                                    @foreach($product->media_images as $index => $img)
                                        @if(empty($img) || !is_string($img)) @continue @endif
                                        @php
                                            $isVideo = preg_match('/\.(mp4|webm|ogg|mov|avi|wmv|flv|mkv|m3u8)(\?.*)?$/i', $img);
                                            $mediaUrl = Str::startsWith($img, 'http') ? $img : asset('storage/' . $img);
                                        @endphp
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
                                            <div class="position-number">{{ $loop->iteration }}</div>
                                            <div class="remove-btn" onclick="this.parentElement.remove(); updatePositionNumbers();">
                                                <i class="fas fa-times"></i>
                                            </div>
                                            <input type="hidden" name="existing_media_images[]" value="{{ $img }}">
                                        </div>
                                    @endforeach
                                @endif
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
                            <input type="url" name="video_url" value="{{ old('video_url', $product->video_url) }}" placeholder="https://youtube.com/watch?v=...">
                        </label>
                    </div>
                </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <style>
        /* Grade dropdown styling - basic, unstyled appearance */
        .grade-from-select option:disabled,
        .grade-to-select option:disabled {
            color: #9ca3af;
        }
        
        /* Media Item Container */
        .media-item {
            position: relative;
            width: 120px;
            height: 120px;
            min-width: 120px;
            flex-shrink: 0;
            border: 2px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
            cursor: grab;
            transition: all 0.3s ease;
            user-select: none;
            background: white;
        }
        .media-item:active { cursor: grabbing; }
        .media-item.dragging {
            opacity: 0.7;
            transform: rotate(3deg) scale(1.05);
            z-index: 1000;
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }
        .media-item.drag-over { border: 2px dashed #007bff; transform: scale(1.02); }
        /* Overlays */
        .position-number {
            position: absolute; bottom: 8px; left: 8px;
            width: 28px; height: 28px; background: rgba(0, 123, 255, 0.9);
            color: white; border-radius: 50%; font-weight: bold;
            display: flex; align-items: center; justify-content: center;
            pointer-events: none;
            font-size: 12px;
            z-index: 2;
        }
        .remove-btn {
            position: absolute; top: 8px; right: 8px;
            width: 24px; height: 24px; background: rgba(220, 53, 69, 0.9);
            color: white; border-radius: 50%; cursor: pointer;
            display: none; align-items: center; justify-content: center;
            z-index: 2;
            font-size: 12px;
        }
        .media-item:hover .remove-btn { display: flex; }
        .video-thumbnail {
            width: 100%; height: 100%; background: #000; display: flex; align-items: center; justify-content: center;
        }
        .video-play-button {
            width: 40px; height: 40px; background: rgba(255,255,255,0.2);
            border-radius: 50%; color: white; display: flex;
            align-items: center; justify-content: center; font-size: 16px;
            backdrop-filter: blur(2px);
             transition: background 0.2s;
        }
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
            const dropZone = document.getElementById('media-drop-zone');
            const fileInput = document.getElementById('gallery-upload-input');
            const previewContainer = document.getElementById('unifiedMediaPreview');
            let draggedItem = null;

            // Make available globally for HTML onclick handlers
            window.updatePositionNumbers = function() {
                const items = previewContainer.querySelectorAll('.media-item');
                items.forEach((item, index) => {
                    const badge = item.querySelector('.position-number');
                    if(badge) badge.textContent = index + 1;
                });
            }

            // Handle File Selection
            fileInput.addEventListener('change', (e) => handleFiles(e.target.files));

            // Drag & Drop Zone Events
            dropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#490d59';
                dropZone.style.background = '#f7f2fb';
            });
            dropZone.addEventListener('dragleave', (e) => {
                 e.preventDefault();
                 dropZone.style.borderColor = '#d1d5db';
                 dropZone.style.background = '';
            });
            dropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                dropZone.style.borderColor = '#d1d5db';
                dropZone.style.background = '';
                handleFiles(e.dataTransfer.files);
            });

            function handleFiles(files) {
                Array.from(files).forEach(file => createUploadedMediaPreview(file));
                updatePositionNumbers();
            }

            window.createUploadedMediaPreview = function(file) {
                const mediaContainer = document.createElement('div');
                mediaContainer.className = 'media-item';
                mediaContainer.draggable = true; // Enable standard drag API

                // Content Type Logic
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    img.style.width = '100%';
                    img.style.height = '100%';
                    img.style.objectFit = 'cover';
                    mediaContainer.appendChild(img);
                } else if (file.type.startsWith('video/')) {
                    const thumb = document.createElement('div');
                    thumb.className = 'video-thumbnail';
                    thumb.innerHTML = `<div class="video-play-button"><i class="fas fa-play"></i></div>`;
                    thumb.onclick = (e) => {
                         e.stopPropagation(); // Prevent drag start interference
                         playVideo(file, thumb);
                    };
                    mediaContainer.appendChild(thumb);
                }

                // Interaction Elements
                const posBadge = document.createElement('div');
                posBadge.className = 'position-number';
                mediaContainer.appendChild(posBadge);

                const removeBtn = document.createElement('div');
                removeBtn.className = 'remove-btn';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = (e) => {
                    e.stopPropagation();
                    mediaContainer.remove();
                    updatePositionNumbers();
                };
                mediaContainer.appendChild(removeBtn);

                // Hidden Input for Form Submission
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'file';
                hiddenInput.name = 'media_images[]'; // Correct array name for backend
                hiddenInput.className = 'd-none';
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                hiddenInput.files = dataTransfer.files;
                mediaContainer.appendChild(hiddenInput);

                // Add Drag Events
                addDragEvents(mediaContainer);

                previewContainer.appendChild(mediaContainer);
            };

            function addDragEvents(item) {
                // Desktop Mouse Dragging
                item.addEventListener('mousedown', (e) => {
                    if(e.target.closest('.remove-btn') || e.target.closest('.video-play-button')) return;
                    // Basic drag start logic can go here for custom visual if needed, 
                    // but we will rely on standard HTML5 DragStart for simplicity with the 'draggable' attr.
                });

                // HTML5 Drag API
                item.addEventListener('dragstart', (e) => {
                     draggedItem = item;
                     setTimeout(() => item.classList.add('dragging'), 0);
                });
                item.addEventListener('dragend', () => {
                    draggedItem = null;
                    item.classList.remove('dragging');
                    updatePositionNumbers();
                });
                item.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    if (item !== draggedItem) {
                        const bounding = item.getBoundingClientRect();
                        const offset = bounding.x + bounding.width / 2;
                        if (e.clientX - offset > 0) {
                            item.style.borderRight = '2px solid #007bff';
                            item.style.borderLeft = '';
                        } else {
                            item.style.borderLeft = '2px solid #007bff';
                            item.style.borderRight = '';
                        }
                    }
                });
                 item.addEventListener('dragleave', () => {
                    item.style.borderLeft = '';
                    item.style.borderRight = '';
                });
                item.addEventListener('drop', (e) => {
                    e.preventDefault();
                    item.style.borderLeft = '';
                    item.style.borderRight = '';
                    if (draggedItem && draggedItem !== item) {
                         const bounding = item.getBoundingClientRect();
                         const offset = bounding.x + bounding.width / 2;
                         if (e.clientX - offset > 0) {
                             item.after(draggedItem);
                         } else {
                             item.before(draggedItem);
                         }
                    }
                });
            }

            function playVideo(file, container) {
                const video = document.createElement('video');
                video.controls = true;
                video.style.width = '100%';
                video.style.height = '100%';
                
                if (file.name.endsWith('.m3u8') && Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(URL.createObjectURL(file));
                    hls.attachMedia(video);
                } else {
                    video.src = URL.createObjectURL(file);
                }
                
                container.innerHTML = '';
                container.appendChild(video);
                video.play();
            }

            // Initialize drag events for existing items
            document.querySelectorAll('.existing-media').forEach(item => {
                addDragEvents(item);
            });

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
                                    input.value = '';
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

            // Remove functions for existing images
            window.removeSizeChartImage = function() {
                const preview = document.getElementById('size_chart_path_preview');
                const input = document.getElementById('size_chart_path_input');
                if (preview) preview.innerHTML = '';
                if (input) input.value = '';
            };

            window.removeSizeMeasurementImage = function() {
                const preview = document.getElementById('size_measurement_image_preview');
                const input = document.getElementById('size_measurement_image_input');
                if (preview) preview.innerHTML = '';
                if (input) input.value = '';
            };

            window.removeFeaturedImage = function() {
                const preview = document.getElementById('featured_image_preview');
                const input = document.getElementById('featured_image_input');
                if (preview) preview.innerHTML = '';
                if (input) input.value = '';
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
        });
    </script>
    @endpush
        </div>
    </form>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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

            // Grade Pricing Toggle - declare early so it's available to all functions
            const gradePricingToggle = document.getElementById('grade-pricing-toggle');

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
                const categoryLabel = document.getElementById('category-label');
                const categorySelect = document.getElementById('category-select');
                
                // Hide/show main price field (but keep pricing section visible for tax fields and inclusive tax)
                // Check if grade pricing is also enabled (use the top-level gradePricingToggle declared below)
                const gradePricingEnabled = gradePricingToggle && gradePricingToggle.checked;
                
                if (mainPriceField) {
                    // If variant pricing is enabled, hide the main price field
                    // If variant pricing is disabled, show it only if grade pricing is also disabled
                    if (isEnabled) {
                        mainPriceField.style.display = 'none';
                    } else {
                        // Variant pricing is disabled, show main price field only if grade pricing is also disabled
                        mainPriceField.style.display = gradePricingEnabled ? 'none' : 'block';
                    }
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
                // Check if grade pricing is enabled - if so, hide variant price inputs
                // Use the top-level gradePricingToggle declared below
                const isGradePricingEnabled = gradePricingToggle && gradePricingToggle.checked;
                
                variantPriceLabels.forEach(label => {
                    // If variant pricing is enabled but grade pricing is also enabled, hide variant price inputs
                    if (isEnabled && isGradePricingEnabled) {
                        label.style.display = 'none';
                        const priceInput = label.querySelector('.variant-price-input');
                        if (priceInput) {
                            priceInput.required = false;
                            priceInput.removeAttribute('required');
                        }
                    } else {
                        label.style.display = isEnabled ? 'block' : 'none';
                        const priceInput = label.querySelector('.variant-price-input');
                        const priceLabelText = label.querySelector('.variant-price-label-text');
                        if (priceInput) {
                            priceInput.required = isEnabled && !isGradePricingEnabled;
                        }
                        if (priceLabelText) {
                            priceLabelText.textContent = isEnabled ? 'Price of Fabric' : 'Price *';
                        }
                    }
                });
                
                // Show/hide weight inputs in variants and update labels
                // Weight is now always visible for all variants
                variantWeightLabels.forEach(label => {
                    label.style.display = 'block'; // Always show weight
                    const weightInput = label.querySelector('.variant-weight-input');
                    const weightLabelText = label.querySelector('.variant-weight-label-text');
                    if (weightInput) {
                        weightInput.required = false; // Weight is optional
                    }
                    if (weightLabelText) {
                        weightLabelText.textContent = 'Weight (kg)'; // Always use standard label
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
                // Check if grade pricing is enabled - if so, hide price field
                // Reuse isGradePricingEnabled from above (line 833)
                const showPriceInVariants = isEnabled && !isGradePricingEnabled;
                
                variantRows.forEach(row => {
                    if (isEnabled && showPriceInVariants) {
                        // Variant pricing enabled, grade pricing disabled: Size, Price, Weight, Stock, Low Stock, Remove
                        row.style.gridTemplateColumns = '1fr 1fr 1fr 1fr 1fr auto';
                    } else if (isEnabled && !showPriceInVariants) {
                        // Variant pricing enabled, grade pricing enabled: Size, Weight, Stock, Low Stock, Remove
                        row.style.gridTemplateColumns = '1fr 1fr 1fr 1fr auto';
                    } else {
                        // Variant pricing disabled: Size, Stock, Low Stock, Remove
                        row.style.gridTemplateColumns = '1fr 1fr 1fr auto';
                    }
                });
            }
            
            // Initialize on page load
            if (variantPricingToggle) {
                variantPricingToggle.addEventListener('change', toggleVariantPricing);
                toggleVariantPricing(); // Initial state
            }

            // Grade Pricing Toggle (gradePricingToggle already declared above)
            const gradePricingSection = document.getElementById('grade-pricing-section');
            const gradeSelectLabel = document.getElementById('grade-select-label');
            const priceRequiredIndicator = document.getElementById('price-required-indicator');
            
            function toggleGradePricing() {
                if (gradePricingToggle && gradePricingSection) {
                    const isEnabled = gradePricingToggle.checked;
                    gradePricingSection.style.display = isEnabled ? 'block' : 'none';
                    
                    // Update required attributes on grade pricing fields based on visibility
                    const gradeFromSelects = gradePricingSection.querySelectorAll('.grade-from-select');
                    const gradePriceInputs = gradePricingSection.querySelectorAll('input[name*="[price]"]');
                    
                    if (isEnabled) {
                        // Section is visible, make fields required
                        gradeFromSelects.forEach(select => {
                            select.setAttribute('required', 'required');
                            select.removeAttribute('tabindex');
                        });
                        gradePriceInputs.forEach(input => {
                            input.setAttribute('required', 'required');
                            input.removeAttribute('tabindex');
                        });
                    } else {
                        // Section is hidden, remove required to prevent validation errors
                        gradeFromSelects.forEach(select => {
                            select.removeAttribute('required');
                            select.setAttribute('tabindex', '-1');
                        });
                        gradePriceInputs.forEach(input => {
                            input.removeAttribute('required');
                            input.setAttribute('tabindex', '-1');
                        });
                    }
                    
                    // Show/hide fabric note when both variant and grade pricing are enabled
                    const gradePricingFabricNote = document.getElementById('grade-pricing-fabric-note');
                    const variantPricingEnabled = variantPricingToggle && variantPricingToggle.checked;
                    if (gradePricingFabricNote) {
                        gradePricingFabricNote.style.display = (isEnabled && variantPricingEnabled) ? 'block' : 'none';
                    }
                    
                    // Hide/show grade dropdown in Organization section
                    if (gradeSelectLabel) {
                        gradeSelectLabel.style.display = isEnabled ? 'none' : 'block';
                    }
                    
                    // Hide/show main price field
                    const mainPriceField = document.querySelector('.main-price-field');
                    const mainPriceInput = document.getElementById('main-price-input');
                    
                    if (mainPriceField) {
                        // If grade pricing is enabled, hide the main price field
                        // If variant pricing is also enabled, it's already hidden by toggleVariantPricing
                        // If grade pricing is disabled, show it (unless variant pricing is enabled)
                        if (isEnabled) {
                            mainPriceField.style.display = 'none';
                        } else {
                            // Grade pricing is disabled, show main price field only if variant pricing is also disabled
                            if (!variantPricingEnabled) {
                                mainPriceField.style.display = 'block';
                            }
                        }
                    }
                    
                    if (mainPriceInput) {
                        // If variant pricing is enabled, main price is already optional
                        // If grade pricing is enabled, main price becomes optional
                        mainPriceInput.required = !isEnabled && !variantPricingEnabled;
                    }
                    
                    // Update price required indicator
                    if (priceRequiredIndicator) {
                        priceRequiredIndicator.style.display = (isEnabled || variantPricingEnabled) ? 'none' : 'inline';
                    }
                    
                    // If variant pricing is also enabled, update variant price inputs visibility
                    if (variantPricingEnabled) {
                        const variantPriceLabels = container.querySelectorAll('.variant-price-label');
                        variantPriceLabels.forEach(label => {
                            if (isEnabled) {
                                // Hide variant price inputs when grade pricing is enabled
                                label.style.display = 'none';
                                const priceInput = label.querySelector('.variant-price-input');
                                if (priceInput) {
                                    priceInput.required = false;
                                    priceInput.removeAttribute('required');
                                }
                            } else {
                                // Show variant price inputs when grade pricing is disabled
                                label.style.display = 'block';
                                const priceInput = label.querySelector('.variant-price-input');
                                if (priceInput) {
                                    priceInput.required = true;
                                }
                            }
                        });
                        
                        // Update grid columns for variant rows
                        const variantRows = container.querySelectorAll('.variant-row');
                        variantRows.forEach(row => {
                            if (isEnabled) {
                                // Grade pricing enabled: Size, Weight, Stock, Low Stock, Remove (no price)
                                row.style.gridTemplateColumns = '1fr 1fr 1fr 1fr auto';
                            } else {
                                // Grade pricing disabled: Size, Price, Weight, Stock, Low Stock, Remove
                                row.style.gridTemplateColumns = '1fr 1fr 1fr 1fr 1fr auto';
                            }
                        });
                    }
                    
                    // Also trigger variant pricing toggle to update variant price inputs
                    if (variantPricingEnabled) {
                        toggleVariantPricing();
                    }
                }
            }
            
            if (gradePricingToggle) {
                gradePricingToggle.addEventListener('change', toggleGradePricing);
                toggleGradePricing(); // Initial state - this will set required attributes correctly
            }
            
            // Also handle form submission to prevent validation errors on hidden fields
            const productForm = document.querySelector('form');
            if (productForm) {
                productForm.addEventListener('submit', function(e) {
                    // Before form submission, ensure hidden grade pricing fields are not required
                    if (gradePricingSection && gradePricingSection.style.display === 'none') {
                        const hiddenRequiredFields = gradePricingSection.querySelectorAll('[required]');
                        hiddenRequiredFields.forEach(field => {
                            field.removeAttribute('required');
                            field.setAttribute('tabindex', '-1');
                        });
                    }
                });
            }

            // Add Grade Range Button
            const addGradeRangeBtn = document.getElementById('add-grade-range-btn');
            const gradePricingRangesContainer = document.getElementById('grade-pricing-ranges-container');
            
            if (addGradeRangeBtn && gradePricingRangesContainer) {
                addGradeRangeBtn.addEventListener('click', function() {
                    const existingRows = gradePricingRangesContainer.querySelectorAll('.grade-pricing-range-row');
                    const index = existingRows.length;
                    
                    // Maintain correct grade order: Pre-KG, LKG, UKG, Class 1-12
                    // Use data-order attribute to ensure correct sorting
                    const gradeOptionsOrdered = [
                        {key: 'Pre-KG', label: 'Pre-KG', order: 0},
                        {key: 'LKG', label: 'LKG', order: 1},
                        {key: 'UKG', label: 'UKG', order: 2},
                        {key: '1', label: 'Class 1', order: 3},
                        {key: '2', label: 'Class 2', order: 4},
                        {key: '3', label: 'Class 3', order: 5},
                        {key: '4', label: 'Class 4', order: 6},
                        {key: '5', label: 'Class 5', order: 7},
                        {key: '6', label: 'Class 6', order: 8},
                        {key: '7', label: 'Class 7', order: 9},
                        {key: '8', label: 'Class 8', order: 10},
                        {key: '9', label: 'Class 9', order: 11},
                        {key: '10', label: 'Class 10', order: 12},
                        {key: '11', label: 'Class 11', order: 13},
                        {key: '12', label: 'Class 12', order: 14}
                    ];
                    // Build options HTML in correct order using createElement to ensure proper DOM order
                    function buildGradeOptionsHtml(includeEmpty = true, emptyText = 'Select') {
                        const fragment = document.createDocumentFragment();
                        if (includeEmpty) {
                            const emptyOpt = document.createElement('option');
                            emptyOpt.value = '';
                            emptyOpt.textContent = emptyText;
                            fragment.appendChild(emptyOpt);
                        }
                        gradeOptionsOrdered.forEach(grade => {
                            const opt = document.createElement('option');
                            opt.value = grade.key;
                            opt.textContent = grade.label;
                            opt.setAttribute('data-order', grade.order);
                            fragment.appendChild(opt);
                        });
                        return fragment;
                    }
                    
                    const newRow = document.createElement('div');
                    newRow.className = 'grade-pricing-range-row';
                    newRow.style.cssText = 'display:grid;grid-template-columns:1fr 1fr 150px auto;gap:12px;align-items:end;padding:12px;border:1px solid #e5e7eb;border-radius:8px;background:#f9fafb;';
                    
                    // Create From Grade select using DOM methods
                    const fromLabel = document.createElement('label');
                    const fromLabelSpan = document.createElement('span');
                    fromLabelSpan.style.cssText = 'font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;';
                    fromLabelSpan.textContent = 'From Grade *';
                    const fromSelect = document.createElement('select');
                    fromSelect.name = `grade_pricing_ranges[${index}][from]`;
                    fromSelect.className = 'grade-from-select';
                    // Set required only if grade pricing is enabled
                    const isGradePricingEnabled = gradePricingToggle && gradePricingToggle.checked;
                    if (isGradePricingEnabled) {
                        fromSelect.required = true;
                    }
                    const fromOptionsFragment = buildGradeOptionsHtml(true, 'Select');
                    fromSelect.appendChild(fromOptionsFragment);
                    fromLabel.appendChild(fromLabelSpan);
                    fromLabel.appendChild(fromSelect);
                    
                    // Create To Grade select using DOM methods
                    const toLabel = document.createElement('label');
                    const toLabelSpan = document.createElement('span');
                    toLabelSpan.style.cssText = 'font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;';
                    const toLabelText = document.createTextNode('To Grade ');
                    const toLabelOptional = document.createElement('span');
                    toLabelOptional.style.cssText = 'color:#6b7280;font-weight:400;';
                    toLabelOptional.textContent = '(Optional)';
                    toLabelSpan.appendChild(toLabelText);
                    toLabelSpan.appendChild(toLabelOptional);
                    const toSelect = document.createElement('select');
                    toSelect.name = `grade_pricing_ranges[${index}][to]`;
                    toSelect.className = 'grade-to-select';
                    const toOptionsFragment = buildGradeOptionsHtml(true, 'Leave empty for single grade');
                    toSelect.appendChild(toOptionsFragment);
                    toSelect.value = ''; // Ensure empty option is selected by default
                    toLabel.appendChild(toLabelSpan);
                    toLabel.appendChild(toSelect);
                    
                    // Create Price input
                    const priceLabel = document.createElement('label');
                    const priceLabelSpan = document.createElement('span');
                    priceLabelSpan.style.cssText = 'font-size:12px;color:#374151;font-weight:500;display:block;margin-bottom:4px;';
                    priceLabelSpan.textContent = 'Price (₹)';
                    const priceInput = document.createElement('input');
                    priceInput.type = 'number';
                    priceInput.name = `grade_pricing_ranges[${index}][price]`;
                    priceInput.value = '';
                    priceInput.min = '0';
                    priceInput.step = '0.01';
                    // Set required only if grade pricing is enabled
                    const isGradePricingEnabledForPrice = gradePricingToggle && gradePricingToggle.checked;
                    if (isGradePricingEnabledForPrice) {
                        priceInput.required = true;
                    }
                    priceInput.placeholder = '0.00';
                    priceInput.style.cssText = 'width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;';
                    priceLabel.appendChild(priceLabelSpan);
                    priceLabel.appendChild(priceInput);
                    
                    // Create Remove button
                    const removeDiv = document.createElement('div');
                    removeDiv.style.cssText = 'display:flex;align-items:end;padding-bottom:4px;';
                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'remove-grade-range-btn';
                    removeBtn.style.cssText = 'padding:8px 12px;background:#fee2e2;color:#b42318;border:none;border-radius:6px;cursor:pointer;font-size:12px;font-weight:600;';
                    removeBtn.innerHTML = '<i class="fas fa-trash"></i>';
                    removeDiv.appendChild(removeBtn);
                    
                    // Append all elements to row
                    newRow.appendChild(fromLabel);
                    newRow.appendChild(toLabel);
                    newRow.appendChild(priceLabel);
                    newRow.appendChild(removeDiv);
                    
                    gradePricingRangesContainer.appendChild(newRow);
                    
                    // Enforce order immediately after adding (though DOM order should be correct now)
                    setTimeout(() => {
                        // enforceGradeOrder(fromSelect);
                        // enforceGradeOrder(toSelect);
                        updateGradeRangeOptions(); // Update options to prevent overlaps
                    }, 0);
                });
            }

            // Remove Grade Range Button (delegate event)
            if (gradePricingRangesContainer) {
                gradePricingRangesContainer.addEventListener('click', function(e) {
                    if (e.target.closest('.remove-grade-range-btn')) {
                        const row = e.target.closest('.grade-pricing-range-row');
                        if (row) {
                            row.remove();
                            updateGradeRangeOptions(); // Update options after removal
                        }
                    }
                });
            }

            // Grade order for range checking
            const gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            
            // Get all existing grade ranges from the form
            function getAllGradeRanges(excludeRow = null) {
                const ranges = [];
                const rows = gradePricingRangesContainer.querySelectorAll('.grade-pricing-range-row');
                
                rows.forEach(row => {
                    if (row === excludeRow) return;
                    
                    const fromSelect = row.querySelector('.grade-from-select');
                    const toSelect = row.querySelector('.grade-to-select');
                    
                    if (fromSelect && fromSelect.value) {
                        const from = fromSelect.value;
                        const to = toSelect && toSelect.value ? toSelect.value : from; // If no "to", it's a single grade
                        ranges.push({ from, to });
                    }
                });
                
                return ranges;
            }
            
            // Check if a grade is within any existing range
            function isGradeInRange(grade, ranges) {
                if (!grade || !ranges.length) return false;
                
                const gradeIndex = gradeOrder.indexOf(grade);
                if (gradeIndex === -1) return false;
                
                return ranges.some(range => {
                    const fromIndex = gradeOrder.indexOf(range.from);
                    const toIndex = gradeOrder.indexOf(range.to);
                    
                    if (fromIndex === -1 || toIndex === -1) return false;
                    
                    return gradeIndex >= Math.min(fromIndex, toIndex) && 
                           gradeIndex <= Math.max(fromIndex, toIndex);
                });
            }
            
            // Check if a range overlaps with existing ranges
            function doesRangeOverlap(from, to, existingRanges, excludeRow = null) {
                if (!from) return false;
                
                const ranges = existingRanges || getAllGradeRanges(excludeRow);
                if (!ranges.length) return false;
                
                const fromIndex = gradeOrder.indexOf(from);
                const toIndex = to ? gradeOrder.indexOf(to) : fromIndex;
                
                if (fromIndex === -1 || (to && toIndex === -1)) return false;
                
                const minIndex = Math.min(fromIndex, toIndex);
                const maxIndex = Math.max(fromIndex, toIndex);
                
                return ranges.some(range => {
                    const rangeFromIndex = gradeOrder.indexOf(range.from);
                    const rangeToIndex = gradeOrder.indexOf(range.to);
                    
                    if (rangeFromIndex === -1 || rangeToIndex === -1) return false;
                    
                    const rangeMin = Math.min(rangeFromIndex, rangeToIndex);
                    const rangeMax = Math.max(rangeFromIndex, rangeToIndex);
                    
                    // Check for overlap: ranges overlap if they share any grade
                    return !(maxIndex < rangeMin || minIndex > rangeMax);
                });
            }
            
            // Update dropdown options to disable overlapping grades
            function updateGradeRangeOptions() {
                const rows = gradePricingRangesContainer.querySelectorAll('.grade-pricing-range-row');
                
                rows.forEach(row => {
                    const fromSelect = row.querySelector('.grade-from-select');
                    const toSelect = row.querySelector('.grade-to-select');
                    
                    if (!fromSelect || !toSelect) return;
                    
                    const currentFrom = fromSelect.value;
                    const currentTo = toSelect.value;
                    const existingRanges = getAllGradeRanges(row);
                    
                    // Update "From Grade" options
                    Array.from(fromSelect.options).forEach(option => {
                        if (!option.value) return; // Skip empty option
                        
                        // Check if selecting this grade would overlap with existing ranges
                        const wouldOverlap = doesRangeOverlap(option.value, currentTo || option.value, existingRanges, row);
                        
                        if (wouldOverlap && option.value !== currentFrom) {
                            option.dataset.blocked = '1';
                            option.style.color = '#9ca3af';

                        } else {
                            option.disabled = '';
                            option.style.color = '';
                        }
                    });
                    
                    // Update "To Grade" options
                    Array.from(toSelect.options).forEach(option => {
                        if (!option.value) return; // Skip empty option
                        
                        // Check if selecting this grade would overlap with existing ranges
                        const wouldOverlap = doesRangeOverlap(currentFrom || option.value, option.value, existingRanges, row);
                        
                        if (wouldOverlap && option.value !== currentTo) {
                            option.disabled = true;
                            option.style.color = '#9ca3af';
                        } else {
                            option.disabled = false;
                            option.style.color = '';
                        }
                    });
                });
            }
            
            // Add change event listeners to grade dropdowns
            if (gradePricingRangesContainer) {
                gradePricingRangesContainer.addEventListener('change', function (e) {
                const opt = e.target.selectedOptions[0];
                if (opt && opt.dataset.blocked === '1') { e.target.value = '';
                alert('This grade overlaps with another range');
    }
});

            }
            
            // Initialize options on page load
            setTimeout(() => {
                updateGradeRangeOptions();
            }, 500);

            // Ensure grade dropdowns maintain correct order (prevent browser sorting)
            function enforceGradeOrder(selectElement) {
                if (!selectElement || selectElement.tagName !== 'SELECT') return;
                
                const gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                const options = Array.from(selectElement.options);
                const emptyOption = options.find(opt => opt.value === '' || opt.textContent.includes('empty') || opt.textContent.includes('Select'));
                const otherOptions = options.filter(opt => opt.value !== '' && !opt.textContent.includes('empty') && !opt.textContent.includes('Select'));
                
                // Sort options by grade order
                otherOptions.sort((a, b) => {
                    const aIndex = gradeOrder.indexOf(a.value);
                    const bIndex = gradeOrder.indexOf(b.value);
                    if (aIndex === -1 && bIndex === -1) return 0;
                    if (aIndex === -1) return 1;
                    if (bIndex === -1) return -1;
                    return aIndex - bIndex;
                });
                
                // Store selected value
                const selectedValue = selectElement.value;
                
                // Clear and rebuild in correct order
                selectElement.innerHTML = '';
                if (emptyOption) {
                    selectElement.appendChild(emptyOption.cloneNode(true));
                }
                otherOptions.forEach(opt => {
                    const newOpt = opt.cloneNode(true);
                    selectElement.appendChild(newOpt);
                });
                
                // Restore selected value
                if (selectedValue) {
                    selectElement.value = selectedValue;
                }
            }

            // Apply to all existing grade selects immediately and on page load
            // function initGradeOrder() {
            //     // Run immediately
            //     document.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
                
            //     // Run again after a short delay to catch any late-rendered elements
            //     setTimeout(() => {
            //         document.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
            //     }, 100);
                
            //     // Run again after DOM is fully ready
            //     setTimeout(() => {
            //         document.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
            //     }, 500);
            // }
            
            // Run immediately if DOM is ready
            // if (document.readyState === 'loading') {
            //     document.addEventListener('DOMContentLoaded', initGradeOrder);
            // } else {
            //     initGradeOrder();
            // }
            
            // Also run when page is fully loaded
            window.addEventListener('load', function() {
                setTimeout(() => {
                    document.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
                }, 100);
            });

            // Re-enforce order after adding new rows
            if (addGradeRangeBtn && gradePricingRangesContainer) {
                addGradeRangeBtn.addEventListener('click', function() {
                    setTimeout(() => {
                        gradePricingRangesContainer.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
                    }, 10);
                    setTimeout(() => {
                        gradePricingRangesContainer.querySelectorAll('.grade-from-select, .grade-to-select').forEach(enforceGradeOrder);
                    }, 100);
                }, true);
            }
            
            // Also enforce order when dropdowns are opened (focus event)
            document.addEventListener('focus', function(e) {
                if (e.target && (e.target.classList.contains('grade-from-select') || e.target.classList.contains('grade-to-select'))) {
                    enforceGradeOrder(e.target);
                }
            }, true);
            
            // Use MutationObserver to catch any dynamically added selects
            if (typeof MutationObserver !== 'undefined') {
                const observer = new MutationObserver(function(mutations) {
                    mutations.forEach(function(mutation) {
                        mutation.addedNodes.forEach(function(node) {
                            if (node.nodeType === 1) { // Element node
                                if (node.classList && (node.classList.contains('grade-from-select') || node.classList.contains('grade-to-select'))) {
                                    enforceGradeOrder(node);
                                }
                                // Check for selects within added nodes
                                const selects = node.querySelectorAll ? node.querySelectorAll('.grade-from-select, .grade-to-select') : [];
                                selects.forEach(enforceGradeOrder);
                            }
                        });
                    });
                });
                
                // Observe the grade pricing container
                if (gradePricingRangesContainer) {
                    observer.observe(gradePricingRangesContainer, {
                        childList: true,
                        subtree: true
                    });
                }
                
                // Also observe the entire document for any grade selects
                observer.observe(document.body, {
                    childList: true,
                    subtree: true
                });
            }

            if (addBtn) {
                addBtn.addEventListener('click', function() {
                    const index = container.querySelectorAll('.variant-row').length;
                    const isVariantPricing = variantPricingToggle ? variantPricingToggle.checked : false;
                    // Use the top-level gradePricingToggle declared below
                    const isGradePricing = gradePricingToggle ? gradePricingToggle.checked : false;
                const showPriceField = isVariantPricing && !isGradePricing; // Hide price if grade pricing is enabled
                const showWeightField = true; // Always show weight for all variants
                
                const row = document.createElement('div');
                row.className = 'variant-row';
                
                // Determine grid columns based on what fields are shown
                // Weight is always visible, so adjust columns accordingly
                let gridColumns;
                if (isVariantPricing && showPriceField) {
                    gridColumns = '1fr 1fr 1fr 1fr 1fr auto'; // Size, Price, Weight, Stock, Low Stock, Remove
                } else {
                    gridColumns = '1fr 1fr 1fr 1fr auto'; // Size, Weight, Stock, Low Stock, Remove
                }
                
                row.style.cssText = `display:grid;grid-template-columns:${gridColumns};gap:12px;margin-bottom:12px;align-items:end;`;
                
                row.innerHTML = `
                    <label>
                        <span style="font-size:12px;">Size / Option</span>
                        <input type="text" name="variants[${index}][option]" placeholder="e.g. S, M, 10" required>
                    </label>
                        ${showPriceField ? `
                        <label class="variant-price-label" style="display:block;">
                            <span class="variant-price-label-text" style="font-size:12px;">Price of Fabric</span>
                            <input type="number" name="variants[${index}][price]" min="0" step="0.01" placeholder="0.00" class="variant-price-input" required>
                        </label>
                        ` : ''}
                        <label class="variant-weight-label" style="display:block;">
                            <span class="variant-weight-label-text" style="font-size:12px;">Weight (kg)</span>
                            <input type="number" name="variants[${index}][weight]" min="0" step="0.01" placeholder="0.00" class="variant-weight-input">
                        </label>
                    <label>
                            <span class="variant-stock-label-text" style="font-size:12px;">${isVariantPricing ? 'Stock of Fabric' : 'Stock'}</span>
                        <input type="number" name="variants[${index}][stock]" placeholder="Qty" min="0" class="variant-stock">
                    </label>
                    <label>
                            <span class="variant-low-stock-label-text" style="font-size:12px;">${isVariantPricing ? 'Qty of Fabric' : 'Low Stock Alert'}</span>
                        <input type="number" name="variants[${index}][low_stock_threshold]" placeholder="Alert Qty" min="0" value="5">
                    </label>
                    <div style="display:flex;align-items:end;padding-bottom:10px;">
                        <button type="button" class="btn-remove-variant" style="color:#b42318;background:none;border:none;cursor:pointer;">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                `;
                container.appendChild(row);
                
                // After appending, apply the same logic as toggleVariantPricing to hide price if grade pricing is enabled
                if (isGradePricing && isVariantPricing) {
                    const priceLabel = row.querySelector('.variant-price-label');
                    if (priceLabel) {
                        priceLabel.style.display = 'none';
                        const priceInput = priceLabel.querySelector('.variant-price-input');
                        if (priceInput) {
                            priceInput.required = false;
                            priceInput.removeAttribute('required');
                        }
                    }
                }
                
                updateMainStock();
                });
            }

            container.addEventListener('click', function(e) {
                if (e.target.closest('.btn-remove-variant')) {
                    e.target.closest('.variant-row').remove();
                    updateMainStock();
                }
            });

            container.addEventListener('input', function(e) {
                if (e.target.classList.contains('variant-stock')) {
                    updateMainStock();
                }
            });
            
            // Apply Weight to All Sizes functionality
            const applyWeightAllBtn = document.getElementById('apply-weight-all-btn');
            if (applyWeightAllBtn) {
                // Show button when there are variants
                function toggleApplyWeightButton() {
                    const variantRows = container.querySelectorAll('.variant-row');
                    applyWeightAllBtn.style.display = variantRows.length > 0 ? 'block' : 'none';
                }
                
                applyWeightAllBtn.addEventListener('click', function() {
                    const variantRows = container.querySelectorAll('.variant-row');
                    if (variantRows.length === 0) {
                        alert('No variants to apply weight to.');
                        return;
                    }
                    
                    // Get weight from first variant row
                    const firstWeightInput = container.querySelector('.variant-weight-input');
                    if (!firstWeightInput) {
                        alert('Please add at least one variant first.');
                        return;
                    }
                    
                    // Get the weight value from the first variant
                    const weightValue = firstWeightInput.value;
                    
                    // Apply first variant's weight to all other variants
                    const allWeightInputs = container.querySelectorAll('.variant-weight-input');
                    allWeightInputs.forEach(input => {
                        // Skip the first input (it already has the value)
                        if (input !== firstWeightInput) {
                            input.value = weightValue;
                        }
                    });
                });
                
                // Toggle button visibility on page load and when variants change
                toggleApplyWeightButton();
                const observer = new MutationObserver(toggleApplyWeightButton);
                observer.observe(container, { childList: true, subtree: true });
            }
        });
    </script>

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
    
    .card:hover {
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
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
        overflow: hidden;
        display: inline-block;
        width: 100%;
    }
    
    .file-input-wrapper input[type="file"] {
        position: absolute;
        left: -9999px;
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
    document.querySelector('form')?.addEventListener('submit', function() {
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