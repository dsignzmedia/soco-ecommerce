@extends('admin.layouts.base')

@section('title', 'Products & Catalog | The Skool Store')
@section('page_heading', 'Products & Catalog')
@section('page_subheading', 'Monitor listings, pricing, inventory and exports')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; vertical-align: middle; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; background-color: #f9fafb; font-weight: 600; }
        td small { color:#6b7280; display:block; font-size: 12px; margin-top: 2px; }
        tr:hover td { background-color: #f9fafb; }
        
        .filters { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 16px; 
            margin-bottom: 24px; 
            align-items: start;
        }
        
        .filters input, .filters select { 
            width: 100%; 
            height: 46px !important;
            padding: 0 16px; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px !important; 
            font-size: 14px; 
            color: #374151; 
            background-color: #fff; 
            box-sizing: border-box; 
            outline: none;
            font-family: inherit;
        }
        .filters input:focus, .filters select:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }

        .filters button, .filters a.reset { 
            width: 100%; 
            height: 46px !important; 
            border-radius: 12px !important; 
            font-weight: 600; 
            text-align: center; 
            padding: 0 16px; 
            font-size: 14px; 
            transition: all 0.2s; 
            box-sizing: border-box; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
        }
        .filters button { 
            border: none; 
            background: #490d59; 
            color: #fff; 
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05); 
        }
        .filters button:hover { background: #370a43; }
        
        .filters a.reset { 
            border: 1px solid #e5e7eb; 
            color: #374151; 
            text-decoration: none; 
            background: #fff; 
        }
        .filters a.reset:hover { 
            background: #f9fafb; 
            border-color: #d1d5db; 
            color: #111827;
        }

        .btn-vs-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; border: 1px solid #d0d5dd; background: white; color: #490d59; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
        .btn-vs-sm:hover { background-color: #490d59 !important; border-color: #490d59 !important; text-decoration: none !important; color: #fff !important; }
        
        /* Tablet Responsive Styles (768px - 1024px) */
        @media (min-width: 768px) and (max-width: 1024px) {
            .filters {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            
            .filters input,
            .filters select,
            .filters button,
            .filters a.reset {
                height: 42px !important;
                font-size: 13px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
            
            th {
                font-size: 11px;
            }
            
            .card {
                padding: 18px;
            }
            
            /* Hide less important columns on tablet */
            th:nth-child(4), /* School */
            td:nth-child(4),
            th:nth-child(5), /* Grade */
            td:nth-child(5) {
                display: none;
            }
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 767px) {
            .filters {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
        
        .status-pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-live { background:#ecfdf3;color:#027a48; }
        .status-draft { background:#f2f4f7;color:#475467; }
        .status-archived { background:#fef3f2;color:#b42318; }
        
        .product-image {
            width: 48px;
            height: 48px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
        }
    </style>
@endpush

@php
    // Helper function to format grades display
    if (!function_exists('formatGradesDisplay')) {
        function formatGradesDisplay($mapping) {
            // Ensure relationship is loaded
            if (!$mapping->relationLoaded('gradePricing')) {
                $mapping->load('gradePricing');
            }
            
            // Check if grade pricing exists and has data
            if ($mapping->gradePricing && $mapping->gradePricing->count() > 0) {
                $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
                $grades = $mapping->gradePricing->pluck('grade')->toArray();
                
                if (empty($grades)) {
                    return $mapping->grade ?? 'All grades';
                }
                
                usort($grades, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a, $gradeOrder);
                    $bIndex = array_search($b, $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                // Format grades display: group consecutive grades into ranges
                $displayGrades = [];
                $currentRange = null;
                foreach ($grades as $i => $grade) {
                    $gradeIndex = array_search($grade, $gradeOrder);
                    if ($i === 0) {
                        $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                    } else {
                        $prevIndex = array_search($grades[$i-1], $gradeOrder);
                        if ($gradeIndex === $prevIndex + 1) {
                            // Consecutive grade
                            $currentRange['end'] = $grade;
                        } else {
                            // Not consecutive, save current range and start new
                            if ($currentRange['start'] === $currentRange['end']) {
                                $displayGrades[] = $currentRange['start'];
                            } else {
                                $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                            }
                            $currentRange = ['start' => $grade, 'end' => $grade, 'index' => $gradeIndex];
                        }
                    }
                }
                // Add last range
                if ($currentRange) {
                    if ($currentRange['start'] === $currentRange['end']) {
                        $displayGrades[] = $currentRange['start'];
                    } else {
                        $displayGrades[] = $currentRange['start'] . '-' . $currentRange['end'];
                    }
                }
                return implode(', ', $displayGrades);
            } else {
                return $mapping->grade ?? 'All grades';
            }
        }
    }

    // Helper function to format grade pricing with ranges
    if (!function_exists('formatGradePricingRanges')) {
        function formatGradePricingRanges($mapping) {
            if (!$mapping->gradePricing || $mapping->gradePricing->count() === 0) {
                return [];
            }
            
            $gradeOrder = ['Pre-KG', 'LKG', 'UKG', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'];
            
            // Group by price
            $pricingByPrice = [];
            foreach ($mapping->gradePricing as $gp) {
                if (!isset($pricingByPrice[$gp->price])) {
                    $pricingByPrice[$gp->price] = [];
                }
                $pricingByPrice[$gp->price][] = $gp->grade;
            }
            
            $ranges = [];
            foreach ($pricingByPrice as $price => $gradeList) {
                // Sort by grade order
                usort($gradeList, function($a, $b) use ($gradeOrder) {
                    $aIndex = array_search($a, $gradeOrder);
                    $bIndex = array_search($b, $gradeOrder);
                    if ($aIndex === false) $aIndex = 999;
                    if ($bIndex === false) $bIndex = 999;
                    return $aIndex <=> $bIndex;
                });
                
                // Group consecutive grades into ranges
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
                        $rangeStr = $currentRange['from'] === $currentRange['to'] 
                            ? $currentRange['from'] 
                            : $currentRange['from'] . '-' . $currentRange['to'];
                        $ranges[] = ['range' => $rangeStr, 'price' => $price];
                        $currentRange = ['from' => $grade, 'to' => $grade];
                    }
                }
                // Add last range for this price
                $rangeStr = $currentRange['from'] === $currentRange['to'] 
                    ? $currentRange['from'] 
                    : $currentRange['from'] . '-' . $currentRange['to'];
                $ranges[] = ['range' => $rangeStr, 'price' => $price];
            }
            
            return $ranges;
        }
    }
@endphp

@section('content')
    <section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Product Filters</h3>
        </div>
        <form class="filters" method="GET">
            <select name="school_id[]" multiple placeholder="Select School">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(in_array($school->id, (array)($filters['school_id'] ?? [])))>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade[]" multiple placeholder="Select Grade">
                @foreach($grades as $key => $label)
                    <option value="{{ $key }}" @selected(in_array($key, (array)($filters['grade'] ?? [])))>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category[]" multiple placeholder="Select Category">
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(in_array($key, (array)($filters['category'] ?? [])))>{{ $label }}</option>
                @endforeach
            </select>
            <select name="product_type[]" multiple placeholder="Product Type">
                @foreach($productTypes as $key => $label)
                    <option value="{{ $key }}" @selected(in_array($key, (array)($filters['product_type'] ?? [])))>{{ $label }}</option>
                @endforeach
            </select>
            <select name="stock_status[]" multiple placeholder="Stock Status">
                <option value="in_stock" @selected(in_array('in_stock', (array)($filters['stock_status'] ?? [])))>In stock</option>
                <option value="out_of_stock" @selected(in_array('out_of_stock', (array)($filters['stock_status'] ?? [])))>Out of stock</option>
            </select>
            <select name="status[]" multiple placeholder="Listing Status">
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(in_array($status, (array)($filters['status'] ?? [])))>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="gender[]" multiple placeholder="Gender">
                @foreach(['male','female','unisex'] as $gender)
                    <option value="{{ $gender }}" @selected(in_array($gender, (array)($filters['gender'] ?? [])))>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" placeholder="Product Name / SKU" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Apply Filter</button>
            <a href="{{ route('master.admin.catalog.index') }}" class="reset">Reset</a>
        </form>
    </section>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('master.admin.catalog.export', ['type' => 'csv'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export CSV</a>
            <a href="{{ route('master.admin.catalog.export', ['type' => 'excel'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export Excel</a>
            <a href="{{ route('master.admin.catalog.export', ['type' => 'pdf'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export PDF</a>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:8px;align-items:center;">
            <div style="display:flex;gap:4px;background:#f9fafb;padding:4px;border-radius:8px;border:1px solid #e5e7eb;">
                <button type="button" id="view-table-btn" class="view-toggle-btn active" title="Table View" style="padding:6px 12px;border:none;background:#490d59;color:#fff;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-table"></i> Table
                </button>
                <button type="button" id="view-grid-btn" class="view-toggle-btn" title="Grid View" style="padding:6px 12px;border:none;background:transparent;color:#6b7280;border-radius:6px;cursor:pointer;font-size:13px;font-weight:500;display:inline-flex;align-items:center;gap:6px;">
                    <i class="fas fa-th"></i> Grid
                </button>
            </div>
            <a href="{{ route('master.admin.catalog.create') }}" style="padding:10px 20px;border-radius:12px;background:#490d59;color:#fff;font-weight:600;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
                <i class="fas fa-plus"></i> Add Product
            </a>
        </div>
    </div>

    <!-- Table View -->
    <section id="table-view" class="card view-container" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Product Details</th>
                        <th>School</th>
                        <th>Category / Type</th>
                        <th style="text-align:left;min-width:180px;">Price</th>
                        <th style="text-align:center;">Stock</th>
                        <th style="min-width: 100px;">Status</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($mappings as $mapping)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="flex-shrink:0;">
                                        @if($mapping->featured_image)
                                            <img src="{{ Str::startsWith($mapping->featured_image, 'http') ? $mapping->featured_image : asset('storage/' . $mapping->featured_image) }}" alt="{{ $mapping->product_name }}" class="product-image">
                                        @else
                                            <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $mapping->product_name }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:#111827;margin-bottom:4px;">
                                            <a href="{{ route('master.admin.catalog.show', $mapping) }}" style="color:#490d59;text-decoration:none;">{{ Str::limit($mapping->product_name, 40) }}</a>
                                        </div>
                                        <small>SKU: {{ $mapping->sku ?? $mapping->id }}</small>
                                        @if($mapping->variants_count > 0)
                                            <small>Variants: {{ $mapping->variants_count }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:500;color:#111827;">{{ optional($mapping->school)->name ?? '—' }}</div>
                                @if($mapping->gender)
                                    <small style="color:#6b7280;">{{ ucfirst($mapping->gender) }}</small>
                                @endif
                            </td>
                            <td>
                                <div style="color:#111827;font-weight:500;">{{ $mapping->category ?? '—' }}</div>
                                <small>{{ ucfirst(str_replace('_', ' ', $mapping->product_type ?? '—')) }}</small>
                            </td>
                            <td style="text-align:left;">
                                @if($mapping->gradePricing && $mapping->gradePricing->count() > 0)
                                    @php $pricingRanges = formatGradePricingRanges($mapping); @endphp
                                    <div style="display:flex;flex-direction:column;gap:4px;min-width:160px;">
                                        @foreach($pricingRanges as $range)
                                            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;font-size:11px;line-height:1.5;">
                                                <span style="font-weight:600;color:#111827;text-align:left;min-width:50px;">{{ $range['range'] }}:</span>
                                                <span style="color:#490d59;font-weight:600;text-align:right;white-space:nowrap;margin-left:auto;">₹{{ number_format($range['price'], 2) }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div style="text-align:right;">
                                        <div style="font-weight:600;color:#111827;">₹{{ number_format($mapping->getDisplayPrice(), 2) }}</div>
                                        @if($mapping->price_sale && $mapping->price_sale > 0)
                                            <small style="color:#b42318;">Sale: ₹{{ number_format($mapping->price_sale, 2) }}</small>
                                        @endif
                                        @if($mapping->price_inclusive_tax)
                                            <small style="color:#027a48;">Incl. tax</small>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="font-weight:500;color:{{ $mapping->stock_status === 'in_stock' ? '#027a48' : '#b42318' }};">
                                    {{ $mapping->inventory_stock }}
                                </div>
                                @if($mapping->variants_count > 0)
                                    <small>(Variants)</small>
                                @else
                                    <small style="color:#6b7280;">{{ $mapping->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</small>
                                @endif
                            </td>
                            <td>
                                <select class="status-select" data-id="{{ $mapping->id }}" style="padding:4px 8px;border-radius:999px;font-size:12px;font-weight:600;border:none;background-color:{{ $mapping->status === 'live' ? '#ecfdf3' : ($mapping->status === 'draft' ? '#f2f4f7' : '#fef3f2') }};color:{{ $mapping->status === 'live' ? '#027a48' : ($mapping->status === 'draft' ? '#475467' : '#b42318') }};cursor:pointer;">
                                    <option value="live" {{ $mapping->status === 'live' ? 'selected' : '' }}>Live</option>
                                    <option value="draft" {{ $mapping->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="archived" {{ $mapping->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                </select>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;justify-content:flex-end;gap:6px;">
                                    <a href="{{ route('master.admin.catalog.edit', $mapping) }}" class="btn-vs-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('master.admin.catalog.show', $mapping) }}" class="btn-vs-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('master.admin.catalog.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="display:inline;margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-vs-sm" style="background:background-color:rgb(89, 13, 13) !important;;color:#b42318;border:1px solid #fedf89;" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align:center;padding:40px;color:#6b7280;">
                                <div style="margin-bottom:8px;font-size:24px;color:#d1d5db;"><i class="fas fa-box-open"></i></div>
                                No products found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container" style="padding:12px 20px;border-top:1px solid #ffffff;background:#fff;">
            {{ $mappings->onEachSide(1)->links() }}
        </div>
    </section>

    <!-- Grid View -->
    <div id="grid-view" class="view-container hidden" style="display:none;">
        <div class="product-grid-container" style="margin-top: 24px;">
            <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
                @forelse($mappings as $mapping)
                    <div class="product-card" style="background:#fff; border: 1px solid #eaecf0; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); transition: transform 0.2s;">
                        <div style="display: flex; gap: 16px; align-items: start;">
                            <div style="flex-shrink: 0; background: #f9fafb; padding: 8px; border-radius: 12px; border: 1px solid #f2f4f7;">
                                @if($mapping->featured_image)
                                    <img src="{{ Str::startsWith($mapping->featured_image, 'http') ? $mapping->featured_image : asset('storage/' . $mapping->featured_image) }}" alt="{{ $mapping->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}'; this.style.width='64px'; this.style.height='64px'; this.style.objectFit='cover'; this.style.borderRadius='8px';">
                                @else
                                    <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $mapping->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                                @endif
                            </div>
                            <div style="flex: 1; min-width: 0;">
                                <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 8px; color: #101828; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ $mapping->product_name }}
                                </h4>
                                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                    <span class="status-pill status-{{ $mapping->status }}" style="padding: 2px 10px; font-size: 11px;">{{ ucfirst($mapping->status) }}</span>
                                </div>
                            </div>
                        </div>

                        <div style="background: #fcfcfd; border-radius: 12px; padding: 12px; border: 1px solid #f2f4f7; font-size: 13px; color: #475467; display: flex; flex-direction: column; gap: 8px; flex-grow: 1;">
                            @if($mapping->gradePricing && $mapping->gradePricing->count() > 0)
                                @php $pricingRanges = formatGradePricingRanges($mapping); @endphp
                                <div style="display: flex; flex-direction: column; gap: 4px;">
                                    <span style="font-weight: 600; color: #101828; margin-bottom: 2px; font-size: 12px;">Grade Pricing:</span>
                                    @foreach($pricingRanges as $range)
                                        <div style="display: flex; justify-content: space-between; align-items: center; gap: 8px; padding: 2px 0;">
                                            <span style="font-size: 11px; color: #111827; font-weight: 600; text-align: left; flex-shrink: 0;">{{ $range['range'] }}:</span>
                                            <span style="font-weight: 600; color: #490d59; font-size: 12px; text-align: right; white-space: nowrap;">₹{{ number_format($range['price'], 2) }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div style="display: flex; justify-content: space-between;">
                                    <span>Price:</span>
                                    <span style="font-weight: 600; color: #101828;">
                                        ₹{{ number_format($mapping->getDisplayPrice(), 2) }}
                                        @if($mapping->price_sale && $mapping->price_sale > 0)
                                            <span style="color:#b42318; margin-left: 4px; font-size: 12px;">(₹{{ number_format($mapping->price_sale, 2) }})</span>
                                        @endif
                                    </span>
                                </div>
                            @endif
                            <div style="display: flex; justify-content: space-between;">
                                <span>School:</span>
                                <span style="text-align: right; max-width: 60%; color: #344054;">{{ optional($mapping->school)->name ?? 'N/A' }}</span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Stock:</span>
                                <span class="{{ $mapping->stock_status === 'in_stock' ? 'text-success' : 'text-danger' }}" style="font-weight: 500;">
                                    @if($mapping->variants_count > 0)
                                        Total: {{ $mapping->inventory_stock }} (Variants)
                                    @else
                                        {{ $mapping->inventory_stock }} ({{ $mapping->stock_status === 'in_stock' ? 'In' : 'Out' }})
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div style="padding-top: 4px; display: flex; gap: 10px;">
                            <a href="{{ route('master.admin.catalog.edit', $mapping) }}" class="btn-vs-sm" style="flex: 1; text-align: center; justify-content: center; background: #490d59; color: white !important; border-radius: 8px; padding: 8px; font-weight: 500; font-size: 13px; border: none;">Edit</a>
                            <a href="{{ route('master.admin.catalog.show', $mapping) }}" class="btn-vs-sm" style="background:#fff; color:#344054; padding: 8px 12px; border: 1px solid #d0d5dd; border-radius: 8px;">
                                <i class="fas fa-eye"></i>
                            </a>
                            <form method="POST" action="{{ route('master.admin.catalog.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="margin:0;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-vs-sm" style="background:#fff; color:#b42318; border: 1px solid #fedf89; padding: 8px 12px; border-radius: 8px;">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #667085; background: #fff; border-radius: 16px; border: 1px dashed #d0d5dd;">
                        <div style="background: #f2f4f7; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                            <i class="fas fa-box-open" style="font-size: 20px; color: #667085;"></i>
                        </div>
                        <h3 style="font-size: 16px; font-weight: 600; color: #101828; margin-bottom: 4px;">No products found</h3>
                        <p style="font-size: 14px; margin-bottom: 24px;">Your search did not return any results. Try different filters.</p>
                        <a href="{{ route('master.admin.catalog.index') }}" class="btn-reset">Clear all filters</a>
                    </div>
                @endforelse
            </div>
            <div style="margin-top: 24px;">
                {{ $mappings->onEachSide(1)->links() }}
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination-container {
        padding: 12px 20px;
        border-top: 1px solid #ffffff;
        background: #fff;
    }
    
    /* Hide the mobile view (the 'Previous' 'Next' text links on the left) */
    .pagination-container nav > div:first-child {
        display: none !important;
    }

    /* Ensure the desktop view takes full width */
    .pagination-container nav > div:last-child {
        display: flex !important;
        justify-content: space-between;
        width: 100%;
        align-items: center;
    }

    /* The "Showing x to y" text */
    .pagination-container p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* Pagination Buttons Container */
    .pagination-container nav span[class*="shadow-sm"],
    .pagination-container nav div[class*="shadow-sm"] {
        box-shadow: none !important;
        display: inline-flex;
        gap: 4px;
    }

    /* Common Button Styles */
    .pagination-container nav a, 
    .pagination-container nav span[aria-disabled], 
    .pagination-container nav span[aria-current="page"] > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        width: 36px !important;
        height: 36px !important;
        margin: 0 !important;
        cursor: pointer;
        box-sizing: border-box !important;
    }

    /* Active Page Styles */
    .pagination-container nav span[aria-current="page"] > span {
        background-color: #490d59 !important;
        border-color: #490d59 !important;
        color: white !important;
    }

    /* Disabled State */
    .pagination-container nav span[aria-disabled] {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f9fafb;
    }

    /* Hover State */
    .pagination-container nav a:hover {
        background-color: #f9fafb;
        border-color: #d1d5db !important;
        color: #111827;
    }
    
    /* Fix for arrows */
    .pagination-container nav svg {
        width: 16px;
        height: 16px;
    }
    
    /* View Toggle Button Styles */
    .view-toggle-btn.active {
        background: #490d59 !important;
        color: #fff !important;
    }
    
    .view-toggle-btn:not(.active) {
        background: transparent !important;
        color: #6b7280 !important;
    }
    
    .view-toggle-btn:not(.active):hover {
        background: #f3f4f6 !important;
        color: #111827 !important;
    }
    
    .view-container {
        display: block;
    }
    
    .view-container.hidden {
        display: none !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tableBtn = document.getElementById('view-table-btn');
        const gridBtn = document.getElementById('view-grid-btn');
        const tableView = document.getElementById('table-view');
        const gridView = document.getElementById('grid-view');
        
        // Get saved preference or default to 'table'
        const savedView = localStorage.getItem('catalogView') || 'table';
        
        // Function to switch views
        function switchView(view) {
            if (view === 'table') {
                tableView.classList.remove('hidden');
                tableView.style.display = 'block';
                gridView.classList.add('hidden');
                gridView.style.display = 'none';
                tableBtn.classList.add('active');
                gridBtn.classList.remove('active');
                localStorage.setItem('catalogView', 'table');
            } else {
                tableView.classList.add('hidden');
                tableView.style.display = 'none';
                gridView.classList.remove('hidden');
                gridView.style.display = 'block';
                tableBtn.classList.remove('active');
                gridBtn.classList.add('active');
                localStorage.setItem('catalogView', 'grid');
            }
        }
        
        // Set initial view based on saved preference
        switchView(savedView);
        
        // Add event listeners
        tableBtn.addEventListener('click', function() {
            switchView('table');
        });
        
        gridBtn.addEventListener('click', function() {
            switchView('grid');
        });

        // Status Update Logic
        const statusSelects = document.querySelectorAll('.status-select');
        statusSelects.forEach(select => {
            select.addEventListener('change', function() {
                const productId = this.getAttribute('data-id');
                const newStatus = this.value;
                const originalColor = this.style.color;
                const originalBg = this.style.backgroundColor;

                // Update styling immediately for feedback
                if (newStatus === 'live') {
                    this.style.backgroundColor = '#ecfdf3';
                    this.style.color = '#027a48';
                } else if (newStatus === 'draft') {
                    this.style.backgroundColor = '#f2f4f7';
                    this.style.color = '#475467';
                } else {
                    this.style.backgroundColor = '#fef3f2';
                    this.style.color = '#b42318';
                }

                // AJAX Request
                fetch('{{ route('master.admin.catalog.update-status') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        id: productId,
                        status: newStatus
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Status updated');
                    } else {
                        alert('Failed to update status.');
                        // Revert style
                        this.style.backgroundColor = originalBg;
                        this.style.color = originalColor;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred.');
                    this.style.backgroundColor = originalBg;
                    this.style.color = originalColor;
                });
            });
        });
    });
</script>
@endpush

