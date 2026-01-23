@extends('admin.layouts.back_to_school')

@section('title', $product->product_name . ' | Products & Catalog')
@section('page_heading', $product->product_name)
@section('page_subheading', 'Detailed listing overview')

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;gap:12px;">
        <a href="{{ route('admin.back_to_school.products.edit', $product) }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;background:#fff;color:#490d59;font-weight:600;text-decoration:none;display:flex;align-items:center;gap:8px;">
            <i class="fas fa-edit"></i> Edit Product
        </a>
        <a href="{{ route('admin.back_to_school.products.index') }}" class="btn-back-outline">
            <i class="fas fa-arrow-left"></i> Back to catalog
        </a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <!-- Left Column -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            
            <!-- Basic Info -->
            <div class="card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-info-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Product Information
                </h3>
                <div style="margin-bottom:16px;">
                    <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Product Name</span>
                    <div style="font-size:16px;color:#111827;font-weight:600;">{{ $product->product_name }}</div>
                </div>
                
                <div>
                    <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Description</span>
                    <div style="color:#475467;line-height:1.5;">
                        @if($product->description)
                            {!! nl2br(e($product->description)) !!}
                        @else
                            <span style="color:#9ca3af;font-style:italic;">No description provided.</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Pricing -->
            <div class="card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-tag" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Pricing
                </h3>
                
                <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:16px;">
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Regular Price</span>
                        <div style="font-size:18px;font-weight:600;color:#111827;">₹{{ number_format($product->price_regular, 2) }}</div>
                    </div>
                    @if($product->price_sale)
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Sale Price</span>
                        <div style="font-size:18px;font-weight:600;color:#b42318;">₹{{ number_format($product->price_sale, 2) }}</div>
                    </div>
                    @endif
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Tax</span>
                        <div style="color:#111827;">{{ $product->price_tax ?? 0 }}% <span style="color:#6b7280;">({{ $product->tax_profile ?? 'No Profile' }})</span></div>
                    </div>
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:4px;">Tax Inclusive?</span>
                        <div style="color:#111827;">{{ $product->price_inclusive_tax ? 'Yes' : 'No' }}</div>
                    </div>
                </div>
            </div>
            
            <!-- Grade Pricing (Condition to check if exists) -->
            @if($product->gradePricing && $product->gradePricing->count() > 0)
            <div class="card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-layer-group" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Grade-wise Pricing
                </h3>
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;font-size:14px;">
                        <thead>
                            <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                <th style="padding:10px;text-align:left;font-weight:600;color:#374151;">Grade</th>
                                <th style="padding:10px;text-align:left;font-weight:600;color:#374151;">Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                                $sortedPricing = $product->gradePricing->sortBy(function($gp) use ($gradeOrder) {
                                    $key = array_search($gp->grade, $gradeOrder);
                                    return $key === false ? 999 : $key;
                                });
                            @endphp
                            @foreach($sortedPricing as $gp)
                            <tr style="border-bottom:1px solid #f3f4f6;">
                                <td style="padding:10px;color:#111827;">{{ $gp->grade }}</td>
                                <td style="padding:10px;color:#111827;font-weight:500;">₹{{ number_format($gp->price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endif

            <!-- Variants -->
            <div class="card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-list" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Product Variants
                </h3>

                @if($product->variants && $product->variants->count() > 0)
                    <div style="overflow-x:auto;border:1px solid #e5e7eb;border-radius:8px;">
                        <table style="width:100%;border-collapse:collapse;font-size:14px;">
                            <thead>
                                <tr style="background:#f9fafb;border-bottom:1px solid #e5e7eb;">
                                    <th style="padding:12px;text-align:left;font-weight:600;color:#374151;">Option / Size</th>
                                    <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Price</th>
                                    <th style="padding:12px;text-align:right;font-weight:600;color:#374151;">Weight (kg)</th>
                                    <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Stock</th>
                                    <th style="padding:12px;text-align:center;font-weight:600;color:#374151;">Low Stock Alert</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($product->variants as $variant)
                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        <td style="padding:12px;color:#111827;font-weight:500;">{{ $variant->option }}</td>
                                        <td style="padding:12px;text-align:right;color:#6b7280;">
                                            @if($variant->price)
                                                ₹{{ number_format($variant->price, 2) }}
                                            @else
                                                <span style="color:#9ca3af;">—</span>
                                            @endif
                                        </td>
                                        <td style="padding:12px;text-align:right;color:#6b7280;">{{ $variant->weight ?? '—' }}</td>
                                        <td style="padding:12px;text-align:center;">
                                            <span style="padding:2px 8px;border-radius:999px;font-size:12px;font-weight:500;background:{{ $variant->stock > 0 ? '#ecfdf3' : '#fef3f2' }};color:{{ $variant->stock > 0 ? '#027a48' : '#b42318' }};">
                                                {{ $variant->stock }}
                                            </span>
                                        </td>
                                        <td style="padding:12px;text-align:center;color:#6b7280;">{{ $variant->low_stock_threshold }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div style="text-align:center;padding:24px;color:#6b7280;background:#f9fafb;border-radius:8px;border:1px dashed #d1d5db;">
                        No variants configured for this product.
                    </div>
                @endif
                
                <div style="margin-top:20px;padding-top:20px;border-top:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center;">
                    <div>
                        <span style="font-size:13px;color:#6b7280;">Total Inventory Stock</span>
                        <div style="font-size:18px;font-weight:600;color:#111827;">{{ $product->inventory_stock }}</div>
                    </div>
                    <div>
                        <span style="font-size:13px;color:#6b7280;">Low Stock Alert (Global)</span>
                        <div style="font-size:18px;font-weight:600;color:#111827;">{{ $product->low_stock_threshold }}</div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Column -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Organization -->
            <div class="card organization-card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-sliders-h" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Organization
                </h3>
                
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">School</span>
                        <div style="color:#111827;font-weight:500;">{{ $product->school?->name ?? 'Global / All Schools' }}</div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Grade</span>
                        <div style="color:#111827;">{{ $product->getAttribute('grade') ?? 'All grades' }}</div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Category</span>
                        <div style="color:#111827;">{{ $product->category ?? '—' }}</div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Product Type</span>
                        <div style="color:#111827;">{{ ucwords(str_replace('_', ' ', $product->product_type)) ?? '—' }}</div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Gender</span>
                        <div style="color:#111827;">{{ ucfirst($product->gender) }}</div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Tag</span>
                        <div style="color:#111827;">
                            @if($product->tag_name)
                                <span style="background:#f0f9ff;color:#026aa2;padding:2px 8px;border-radius:12px;font-size:12px;border:1px solid #b9e6fe;">{{ $product->tag_name }}</span>
                            @else
                                —
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Publish Status -->
            <div class="card publish-card">
                <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
                    <i class="fas fa-check-circle" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
                    Publish Status
                </h3>
                
                <div style="display:flex;flex-direction:column;gap:16px;">
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Status</span>
                        <div>
                            <span class="status-pill status-{{ $product->status }}" style="padding:4px 12px;border-radius:999px;font-size:13px;font-weight:600;text-transform:capitalize;background:{{ $product->status === 'live' ? '#ecfdf3' : ($product->status === 'draft' ? '#f2f4f7' : '#fef3f2') }};color:{{ $product->status === 'live' ? '#027a48' : ($product->status === 'draft' ? '#344054' : '#b42318') }};border:1px solid {{ $product->status === 'live' ? '#abefc6' : ($product->status === 'draft' ? '#d0d5dd' : '#fecdca') }};">
                                {{ ucfirst($product->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Stock Status</span>
                        <div style="color:{{ $product->stock_status === 'in_stock' ? '#027a48' : '#b42318' }};font-weight:500;">
                            {{ $product->stock_status === 'in_stock' ? 'In Stock' : 'Out of Stock' }}
                        </div>
                    </div>
                    <div>
                        <span style="display:block;font-size:12px;font-weight:500;color:#6b7280;margin-bottom:2px;">Delivery Duration</span>
                        <div style="color:#111827;">{{ $product->delivery_duration ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Media -->
    <div class="card" style="margin-top:24px;">
        <h3 style="margin:0 0 20px;color:#111827;display:flex;align-items:center;gap:10px;">
            <i class="fas fa-images" style="color:#490d59;background:#f7f2fb;padding:8px;border-radius:8px;"></i>
            Media
        </h3>
        
        <div style="display:flex;gap:24px;flex-wrap:wrap;">
            @if($product->featured_image)
            <div>
                <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Featured Image</span>
                <img src="{{ Str::startsWith($product->featured_image, 'http') ? $product->featured_image : asset('storage/' . $product->featured_image) }}" alt="Featured" style="width:160px;height:160px;object-fit:cover;border-radius:12px;border:1px solid #e5e7eb;box-shadow:0 1px 3px rgba(0,0,0,0.1);">
            </div>
            @endif
            
            <div style="flex:1;">
                <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Gallery</span>
                @if($product->media_gallery && count($product->media_gallery) > 0)
                    <div style="display:flex;gap:12px;flex-wrap:wrap;">
                        @foreach($product->media_gallery as $image)
                            <img src="{{ Str::startsWith($image, 'http') ? $image : asset('storage/' . $image) }}" alt="" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                        @endforeach
                    </div>
                @else
                    <div style="color:#9ca3af;font-style:italic;">No gallery images.</div>
                @endif
            </div>
        </div>

        <div style="margin-top:24px;border-top:1px solid #e5e7eb;padding-top:16px;">
            <div style="display:flex;gap:32px;flex-wrap:wrap;">
                @if($product->size_chart_path)
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Size Chart</span>
                        <a href="{{ asset('storage/' . $product->size_chart_path) }}" target="_blank">
                            <img src="{{ asset('storage/' . $product->size_chart_path) }}" alt="Size Chart" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    </div>
                @endif
                
                @if($product->size_measurement_image)
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Measurement Image</span>
                        <a href="{{ asset('storage/' . $product->size_measurement_image) }}" target="_blank">
                            <img src="{{ asset('storage/' . $product->size_measurement_image) }}" alt="Measurement" style="width:100px;height:100px;object-fit:cover;border-radius:8px;border:1px solid #e5e7eb;">
                        </a>
                    </div>
                @endif
                
                @if($product->video_url)
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Video URL</span>
                        <a href="{{ $product->video_url }}" target="_blank" style="color:#490d59;text-decoration:underline;">
                            <i class="fas fa-video"></i> Watch Video
                        </a>
                    </div>
                @endif
                
                @if($product->video_file)
                    <div>
                        <span style="display:block;font-size:13px;font-weight:500;color:#374151;margin-bottom:8px;">Video File</span>
                        <a href="{{ asset('storage/' . $product->video_file) }}" target="_blank" style="color:#490d59;text-decoration:underline;">
                            <i class="fas fa-video"></i> View/Download Local Video
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Additional Styles to match form.blade.php -->
    <style>
        .card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        /* Responsive Grid */
        @media (max-width: 1024px) {
            div[style*="grid-template-columns:2fr 1fr"] {
                grid-template-columns: 1fr !important;
            }
        }
    </style>
@endsection

