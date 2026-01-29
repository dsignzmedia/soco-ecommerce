@extends('admin.layouts.merchandise')

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
        .btn-vs-sm:hover { background-color: #f3e8f5; border-color: #490d59; text-decoration: none; color: #490d59; }
        
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

@section('content')
    <section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Product Filters</h3>
        </div>
        <form class="filters" method="GET">
            <select name="school_id">
                <option value="">School (All)</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade">
                <option value="">Grade (All)</option>
                @foreach($grades as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['grade'] ?? '') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">Category (All)</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['category'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="stock_status">
                <option value="">Stock status</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <select name="status">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="gender">
                <option value="">Gender</option>
                @foreach(['male','female','unisex'] as $gender)
                    <option value="{{ $gender }}" @selected(($filters['gender'] ?? '') === $gender)>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" placeholder="Product Name / SKU" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Apply Filter</button>
            <a href="{{ route('admin.merchandise.products.index') }}" class="reset">Reset</a>
        </form>
    </section>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'csv'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export CSV</a>
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'excel'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export Excel</a>
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'pdf'] + request()->query()) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export PDF</a>
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
            <a href="{{ route('admin.merchandise.products.create') }}" style="padding:10px 20px;border-radius:12px;background:#490d59;color:#fff;font-weight:600;font-size:14px;text-decoration:none;display:inline-flex;align-items:center;gap:6px;">
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
                        <th>School / Grade</th>
                        <th>Category</th>
                        <th style="text-align:right;">Price</th>
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
                                            <img src="{{ Str::startsWith($mapping->featured_image, 'http') ? $mapping->featured_image : asset('storage/' . $mapping->featured_image) }}" alt="{{ $mapping->product_name }}" class="product-image" onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}'; this.style.width='48px'; this.style.height='48px'; this.style.objectFit='cover'; this.style.borderRadius='8px';">
                                        @else
                                            <img src="{{ asset('assets/img/no image/no_image.png') }}" alt="{{ $mapping->product_name }}" style="width:48px;height:48px;object-fit:cover;border-radius:8px;">
                                        @endif
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:#111827;margin-bottom:4px;">
                                            <a href="{{ route('admin.merchandise.products.show', $mapping) }}" style="color:#490d59;text-decoration:none;">{{ Str::limit($mapping->product_name, 40) }}</a>
                                        </div>
                                        <small>SKU: {{ $mapping->sku ?? $mapping->id }}</small>
                                        @if(isset($mapping->variants_count) && $mapping->variants_count > 0)
                                            <small>Variants: {{ $mapping->variants_count }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:500;color:#111827;">{{ $mapping->school?->name ?? 'General / No School' }}</div>
                                <small>{{ $mapping->grade ?? 'All grades' }}</small>
                                @if($mapping->gender)
                                    <small>{{ ucfirst($mapping->gender) }}</small>
                                @endif
                            </td>
                            <td>
                                <div style="color:#111827;font-weight:500;">{{ $mapping->category ?? '—' }}</div>
                            </td>
                            <td style="text-align:right;">
                                <div style="font-weight:600;color:#111827;">₹{{ number_format($mapping->price_regular ?? 0, 2) }}</div>
                                @if($mapping->price_sale && $mapping->price_sale > 0)
                                    <small style="color:#b42318;">Sale: ₹{{ number_format($mapping->price_sale, 2) }}</small>
                                @endif
                                @if($mapping->price_inclusive_tax)
                                    <small style="color:#027a48;">Incl. tax</small>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <div style="font-weight:500;color:{{ $mapping->stock_status === 'in_stock' ? '#027a48' : '#b42318' }};">
                                    {{ $mapping->inventory_stock }}
                                </div>
                                @if(isset($mapping->variants_count) && $mapping->variants_count > 0)
                                    <small>(Variants)</small>
                                @else
                                    <small style="color:#6b7280;">{{ $mapping->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="status-pill status-{{ $mapping->status }}">{{ ucfirst($mapping->status) }}</span>
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex;justify-content:flex-end;gap:6px;">
                                    <a href="{{ route('admin.merchandise.products.edit', $mapping) }}" class="btn-vs-sm" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('admin.merchandise.products.show', $mapping) }}" class="btn-vs-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.merchandise.products.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="display:inline;margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-vs-sm" style="background:#fff;color:#b42318;border:1px solid #fedf89;" title="Delete">
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

        <div class="pagination-container" style="padding:12px 20px;border-top:1px solid #e5e7eb;background:#fff;">
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
                                <span style="font-size: 11px; color: #344054; background: #f2f4f7; padding: 2px 10px; border-radius: 999px; border: 1px solid #e4e7ec;">{{ $mapping->grade ?? 'All grades' }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="background: #fcfcfd; border-radius: 12px; padding: 12px; border: 1px solid #f2f4f7; font-size: 13px; color: #475467; display: flex; flex-direction: column; gap: 8px; flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Price:</span>
                            <span style="font-weight: 600; color: #101828;">
                                ₹{{ number_format($mapping->price_regular ?? 0, 2) }}
                                @if($mapping->price_sale)
                                    <span style="color:#b42318; margin-left: 4px; font-size: 12px;">(₹{{ number_format($mapping->price_sale, 2) }})</span>
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>School:</span>
                            <span style="text-align: right; max-width: 60%; color: #344054;">{{ $mapping->school?->name ?? 'General / No School' }}</span>
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
                        <a href="{{ route('admin.merchandise.products.edit', $mapping) }}" class="btn-vs-sm" style="flex: 1; text-align: center; justify-content: center; background: #490d59; color: white !important; border-radius: 8px; padding: 8px; font-weight: 500; font-size: 13px; border: none;">Edit</a>
                        <a href="{{ route('admin.merchandise.products.show', $mapping) }}" class="btn-vs-sm" style="background:#fff; color:#344054; padding: 8px 12px; border: 1px solid #d0d5dd; border-radius: 8px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.merchandise.products.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="margin:0;">
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
                    <a href="{{ route('admin.merchandise.products.index') }}" class="btn-reset">Clear all filters</a>
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
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }
    
    .pagination-container nav > div:first-child {
        display: none !important;
    }

    .pagination-container nav > div:last-child {
        display: flex !important;
        justify-content: space-between;
        width: 100%;
        align-items: center;
    }

    .pagination-container p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    .pagination-container nav span[class*="shadow-sm"],
    .pagination-container nav div[class*="shadow-sm"] {
        box-shadow: none !important;
        display: inline-flex;
        gap: 4px;
    }

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

    .pagination-container nav span[aria-current="page"] > span {
        background-color: #490d59 !important;
        border-color: #490d59 !important;
        color: white !important;
    }

    .pagination-container nav span[aria-disabled] {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f9fafb;
    }

    .pagination-container nav a:hover {
        background-color: #f9fafb;
        border-color: #d1d5db !important;
        color: #111827;
    }
    
    .pagination-container nav svg {
        width: 16px;
        height: 16px;
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
        const savedView = localStorage.getItem('merchCatalogView') || 'table';
        
        // Function to switch views
        function switchView(view) {
            if (view === 'table') {
                tableView.classList.remove('hidden');
                tableView.style.display = 'block';
                gridView.classList.add('hidden');
                gridView.style.display = 'none';
                tableBtn.classList.add('active');
                gridBtn.classList.remove('active');
                localStorage.setItem('merchCatalogView', 'table');
            } else {
                tableView.classList.add('hidden');
                tableView.style.display = 'none';
                gridView.classList.remove('hidden');
                gridView.style.display = 'block';
                tableBtn.classList.remove('active');
                gridBtn.classList.add('active');
                localStorage.setItem('merchCatalogView', 'grid');
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
    });
</script>
@endpush

