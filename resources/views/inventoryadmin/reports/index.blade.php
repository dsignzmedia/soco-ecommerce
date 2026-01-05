@extends('inventoryadmin.layouts.base')

@section('title', 'Reports & Analytics | Inventory Admin')
@section('page_heading', 'Reports & Analytics')
@section('page_subheading', 'Detailed analysis of stock levels, distribution, and movements')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@push('styles')
    <style>
        /* TOP METRICS (Master Admin Style - Kept) */
        .report-metrics { 
            margin-top:20px;
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(110px,1fr));
            gap:16px;
        }
        .metric-item { 
            background:#ffffff;
            padding:16px 12px;
            border-radius:12px;
            transition:all 0.2s;
            border: 1px solid #e5e7eb;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        .metric-item:hover {
            background:#f3f4f6;
            transform:scale(1.02);
        }
        .metric-value { 
            font-size:20px;
            font-weight:800;
            color:#490d59;
            line-height:1.3;
            margin-bottom:6px;
            word-break:break-word;
        }
        .metric-label { 
            font-size:11px;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:0.5px;
            font-weight:600;
            line-height:1.3;
        }

        /* Unified Filter Styling (Matched to Inventory Orders) */
        .filters { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 16px; 
            margin-bottom: 24px; 
            align-items: start;
        }
        
        .filters input, .filters .date-group input, .filters select { 
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

        .filters .date-group { width: 100%; display: block; }

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

        /* REPORT GRID (Master Admin Style) */
        .report-grid { 
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(300px,1fr));
            gap:20px;
            margin-bottom:32px;
        }
        .report-card { 
            border:1px solid #e5e7eb;
            border-radius:20px;
            padding:24px;
            cursor:pointer;
            transition:all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background:#fff;
            position:relative;
            overflow:hidden;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 180px;
        }
        .report-card::before {
            content:'';
            position:absolute;
            top:0;left:0;right:0;height:4px;
            background:linear-gradient(90deg, #490d59, #7c3aed);
            transform:scaleX(0);
            transform-origin:left;
            transition:transform 0.3s ease;
        }
        .report-card:hover { 
            border-color:#490d59;
            box-shadow:0 10px 40px rgba(73,13,89,0.12);
            transform:translateY(-4px);
        }
        .report-card:hover::before { transform:scaleX(1); }
        
        .report-card-header { 
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:15px;
        }
        .report-card h4 { 
            margin:0 0 6px;
            color:#111827;
            font-size:18px;
            font-weight:700;
        }
        .report-card p { 
            margin:0;
            color:#6b7280;
            font-size:13px;
            line-height:1.5;
        }
        .report-icon {
            width:40px; height:40px;
            background:linear-gradient(135deg, #490d59, #7c3aed);
            border-radius:10px;
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:18px;
            transition:transform 0.3s ease;
        }
        .report-card:hover .report-icon { transform:rotate(5deg) scale(1.1); }

        /* MODAL STYLES */
        .report-modal {
            display:none;
            position:fixed; top:0; left:0; width:100%; height:100%;
            background:rgba(0,0,0,0.6);
            z-index:9000;
            animation:fadeIn 0.25s ease;
            overflow-y:auto;
        }
        .report-modal.active { display:flex; align-items:flex-start; justify-content:center; padding:40px 20px; }
        @keyframes fadeIn { from { opacity:0; } to { opacity:1; } }

        .modal-content {
            background:#fff;
            border-radius:20px;
            max-width:1100px; width:100%;
            margin-top: 20px;
            margin-bottom: 20px;
            box-shadow:0 25px 50px rgba(0,0,0,0.25);
            animation:slideUp 0.3s ease;
            position:relative;
            display: flex; flex-direction: column;
            max-height: 85vh;
        }
        @keyframes slideUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }

        .modal-header {
            padding:24px 32px;
            border-bottom:1px solid #e5e7eb;
            display:flex; justify-content:space-between; align-items:center;
            background: #fff;
            border-radius: 20px 20px 0 0;
        }
        .modal-header h2 { margin:0; font-size:24px; font-weight:800; color:#111827; }
        .modal-close {
            width:36px; height:36px;
            border-radius:50%; background:#f3f4f6; border:none;
            cursor:pointer; display:flex; align-items:center; justify-content:center;
            transition:all 0.2s;
        }
        .modal-close:hover { background:#e5e7eb; transform:rotate(90deg); }

        .modal-body {
            padding:32px;
            overflow-y:auto;
            flex:1;
        }

        /* Modal Internal Grid */
        .modal-split-view {
            display:grid; grid-template-columns:1fr 1fr; gap:32px;
            height:100%;
        }
        .chart-box {
            background:#f9fafb; border-radius:16px; padding:20px;
            display:flex; align-items:center; justify-content:center;
            min-height:300px;
        }
        .table-box {
            border:1px solid #e5e7eb; border-radius:16px; overflow:hidden;
        }
        .modal-data-table { width:100%; border-collapse:collapse; }
        .modal-data-table th {
            background:#f9fafb; padding:12px 16px; text-align:left;
            font-size:12px; font-weight:700; text-transform:uppercase; color:#6b7280;
            border-bottom:1px solid #e5e7eb; position:sticky; top:0;
        }
        .modal-data-table td {
            padding:12px 16px; font-size:13px; border-bottom:1px solid #e5e7eb;
            color:#374151;
        }
        .modal-data-table tr:last-child td { border-bottom:none; }

        @media(max-width:1024px) {
            .modal-split-view { grid-template-columns:1fr; }
            .chart-box { min-height:250px; }
        }

        /* Detailed Table below Grid */
        .detailed-table-container {
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            overflow: hidden;
            background: #fff;
            margin-top: 32px;
        }
        .detailed-table-header {
            padding: 20px;
            border-bottom: 1px solid #e5e7eb;
            display: flex; justify-content: space-between; align-items: center;
            background: #f9fafb;
        }
        .detailed-table-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #111827; }
        
        .main-table { width: 100%; border-collapse: collapse; }
        .main-table th {
            background: #f9fafb; padding: 12px 16px; text-align: left;
            font-size: 11px; font-weight: 700; text-transform: uppercase; color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
        }
        .main-table td {
            padding: 12px 16px; font-size: 13px; border-bottom: 1px solid #e5e7eb;
            color: #374151;
        }

    /* Pagination Containers and Buttons (Matched to Inventory Orders) */
    .pagination-container {
        padding: 12px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }
    .pagination-container nav > div:first-child { display: none !important; } /* Hide mobile text */
    .pagination-container nav > div:last-child {
        display: flex !important;
        justify-content: space-between;
        width: 100%;
        align-items: center;
    }
    .pagination-container p { font-size: 13px; color: #6b7280; margin: 0; }
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
        background-color: #f3e8f5;
        border-color: #490d59 !important;
        color: #490d59;
    }
    .pagination-container nav svg { width: 16px; height: 16px; }

    </style>
@endpush

@section('content')

    <!-- 1. Top Metrics Bar -->
    <div class="report-metrics">
        <div class="metric-item">
            <div class="metric-value">{{ number_format($totalProducts) }}</div>
            <div class="metric-label">Total SKUs</div>
        </div>
        <div class="metric-item">
            <div class="metric-value">{{ number_format($totalStock) }}</div>
            <div class="metric-label">Total Units</div>
        </div>
        <div class="metric-item">
            <div class="metric-value" style="color:{{ $lowStock->count() > 0 ? '#d97706' : '#059669' }}">
                {{ number_format($lowStock->count()) }}
            </div>
            <div class="metric-label">Low Stock</div>
        </div>
        <div class="metric-item">
            <div class="metric-value" style="color:{{ $outOfStock->count() > 0 ? '#dc2626' : '#059669' }}">
                {{ number_format($outOfStock->count()) }}
            </div>
            <div class="metric-label">Out of Stock</div>
        </div>
        <div class="metric-item">
            <div class="metric-value">{{ number_format($avgStock, 1) }}</div>
            <div class="metric-label">Avg. Stock/SKU</div>
        </div>
    </div>

    <!-- 2. Filters (Matched to Orders) -->
    <section class="card" style="margin:24px 0; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <form method="GET" action="{{ route('inventory.admin.reports.index') }}" class="filters">
            <select name="school_id">
                <option value="">All Schools</option>
                @foreach($schools as $s)
                    <option value="{{ $s->id }}" {{ ($filters['school_id'] ?? '') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
            <select name="grade_id">
                <option value="">All Grades</option>
                @foreach($grades as $g)
                    <option value="{{ $g->id }}" {{ ($filters['grade_id'] ?? '') == $g->id ? 'selected' : '' }}>{{ $g->name }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">All Categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c }}" {{ ($filters['category'] ?? '') == $c ? 'selected' : '' }}>{{ $c }}</option>
                @endforeach
            </select>
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search Product...">
            
            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" style="width: auto; min-width: 120px;">Apply Filters</button>
                <a href="{{ route('inventory.admin.reports.index') }}" class="reset" style="width: auto; min-width: 100px;">Reset</a>
            </div>
        </form>
    </section>

    <!-- 3. Report Grid -->
    <div class="report-grid">
        
        <!-- Card 1: Stock Overview -->
        <div class="report-card" onclick="toggleReport('stock_overview')">
            <div class="report-card-header">
                <div class="report-icon"><i class="fas fa-boxes"></i></div>
            </div>
            <h4>Stock Overview</h4>
            <p>Analysis of stock status distribution (In Stock vs Low/Out).</p>
        </div>

        <!-- Card 2: School Distribution -->
        <div class="report-card" onclick="toggleReport('school_stock')">
            <div class="report-card-header">
                <div class="report-icon"><i class="fas fa-school"></i></div>
            </div>
            <h4>By School</h4>
            <p>Stock unit distribution across different schools.</p>
        </div>

        <!-- Card 3: Category Distribution -->
        <div class="report-card" onclick="toggleReport('category_stock')">
            <div class="report-card-header">
                <div class="report-icon"><i class="fas fa-layer-group"></i></div>
            </div>
            <h4>By Category</h4>
            <p>Inventory breakdown by product categories.</p>
        </div>

        <!-- Card 4: Aging Analysis -->
        <div class="report-card" onclick="toggleReport('aging')">
            <div class="report-card-header">
                <div class="report-icon" style="background:linear-gradient(135deg, #d97706, #f59e0b);"><i class="fas fa-history"></i></div>
            </div>
            <h4>Aging Inventory</h4>
            <p>Products not updated in 30+ days. Potential stale stock.</p>
        </div>

        <!-- Card 5: Low Stock Alerts -->
        @if($lowStock->count() > 0 || $outOfStock->count() > 0)
        <div class="report-card" onclick="toggleReport('low_stock')">
            <div class="report-card-header">
                <div class="report-icon" style="background:linear-gradient(135deg, #dc2626, #ef4444);"><i class="fas fa-exclamation-triangle"></i></div>
            </div>
            <h4>Stock Alerts</h4>
            <p style="color:#dc2626;font-weight:600;">{{ $lowStock->count() + $outOfStock->count() }} Items require attention.</p>
        </div>
        @endif

    </div>

    <!-- 4. Detailed Inventory Table (Inline) -->
    <div class="detailed-table-container">
        <div class="detailed-table-header">
            <h3>Detailed Inventory List</h3>
            <div class="export-buttons" style="display:flex;gap:10px;">
                <a href="{{ route('inventory.admin.reports.index', array_merge($filters, ['export' => 'csv'])) }}"><i class="fas fa-file-csv"></i> CSV</a>
                <a href="{{ route('inventory.admin.reports.index', array_merge($filters, ['export' => 'pdf'])) }}"><i class="fas fa-file-pdf"></i> PDF</a>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="main-table">
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th style="width:60px;">Image</th>
                        <th>Product Name</th>
                        <th>School</th>
                        <th>Grade</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $p)
                        <tr>
                            <td>{{ $loop->iteration + ($products->currentPage()-1)*20 }}</td>
                            <td>
                                @if($p->featured_image)
                                    <img src="{{ asset('storage/' . $p->featured_image) }}" 
                                         alt="Img" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                @else
                                    <img src="{{ asset('assets/img/no image/no_image.png') }}" 
                                         alt="Default" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                                @endif
                            </td>
                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    @if($p->variants && $p->variants->count() > 0)
                                        <button type="button" class="toggle-report-variants" onclick="toggleReportVariants({{ $p->id }})" style="background:none; border:none; cursor:pointer; padding:4px; color:#490d59;">
                                            <i class="fas fa-chevron-down" id="report-icon-{{ $p->id }}"></i>
                                        </button>
                                    @endif
                                    <div style="font-weight:600;">{{ $p->product_name }}</div>
                                </div>
                                @if($p->variants && $p->variants->count() > 0)
                                    <div style="font-size: 11px; color: #6b7280; margin-top: 4px; margin-left:24px;">
                                        ({{ $p->variants->count() }} variant{{ $p->variants->count() > 1 ? 's' : '' }})
                                    </div>
                                @endif
                            </td>
                            <td>{{ $p->school->name ?? '-' }}</td>
                            <td>{{ $p->grade }}</td>
                            <td>{{ $p->category }}</td>
                            <td style="font-weight:700;">{{ $p->inventory_stock }}</td>
                            <td>
                                @php
                                    $statusColor = $p->inventory_stock <= 0 ? '#fee2e2' : ($p->inventory_stock < 10 ? '#fef3c7' : '#dcfce7');
                                    $textColor = $p->inventory_stock <= 0 ? '#991b1b' : ($p->inventory_stock < 10 ? '#92400e' : '#166534');
                                    $statusText = $p->inventory_stock <= 0 ? 'Out of Stock' : ($p->inventory_stock < 10 ? 'Low Stock' : 'In Stock');
                                @endphp
                                <span style="background:{{ $statusColor }};color:{{ $textColor }};padding:4px 10px;border-radius:12px;font-size:11px;font-weight:700;">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                        @if($p->variants && $p->variants->count() > 0)
                            <tr id="report-variants-{{ $p->id }}" style="display:none; background-color: #f9fafb;">
                                <td colspan="8" style="padding: 0;">
                                    <div style="padding: 16px; border-top: 1px solid #e5e7eb; margin-left: 60px;">
                                        <h4 style="margin: 0 0 12px; font-size: 13px; font-weight: 700; color: #111827; text-transform:uppercase;">Variant Breakdown</h4>
                                        <div style="display: grid; gap: 8px; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));">
                                            @foreach($p->variants as $variant)
                                            <div style="display: flex; align-items: center; justify-content: space-between; padding: 10px; background: white; border: 1px solid #e5e7eb; border-radius: 8px;">
                                                <div style="font-weight: 600; color: #374151; font-size:13px;">{{ $variant->option }}</div>
                                                <div style="font-size: 13px; color: #6b7280;">
                                                    <strong style="color: {{ $variant->stock <= 0 ? '#dc2626' : ($variant->stock <= 5 ? '#d97706' : '#166534') }};">{{ $variant->stock }}</strong> Units
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="7" style="text-align:center;padding:30px;">No products found matching filters.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            {{ $products->links() }}
        </div>
    </div>


    <!-- HIDDEN DATA CONTAINERS FOR MODALS -->
    
    <!-- 1. Stock Overview Data -->
    <div id="details-stock_overview" style="display:none;">
        <table data-chart-type="doughnut" data-chart-label="Stock Status">
            <tbody>
                <tr><td>In Stock</td><td>{{ $inStockCount }}</td></tr>
                <tr><td>Low Stock</td><td>{{ $lowStock->count() }}</td></tr>
                <tr><td>Out of Stock</td><td>{{ $outOfStock->count() }}</td></tr>
            </tbody>
        </table>
    </div>

    <!-- 2. School Stock Data -->
    <div id="details-school_stock" style="display:none;">
        <table data-chart-type="bar" data-chart-label="Units">
            <thead>
                <tr>
                    <th>School</th>
                    <th>Total Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockBySchool as $item)
                    <tr>
                        <td>{{ $item->school->name ?? 'Unknown' }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 3. Category Stock Data -->
    <div id="details-category_stock" style="display:none;">
        <table data-chart-type="pie" data-chart-label="Units">
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Stock</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockByCategory as $item)
                    <tr>
                        <td>{{ $item->category ?? 'Uncategorized' }}</td>
                        <td>{{ $item['total'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 4. Aging Data -->
    <div id="details-aging" style="display:none;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>School</th>
                    <th>Last Updated</th>
                    <th>Days Old</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stockAging as $ap)
                    <tr>
                        <td>{{ $ap['name'] }}</td>
                        <td>{{ $ap['school'] }}</td>
                        <td>{{ $ap['stock'] }} Units</td>
                        <td>{{ $ap['days_old'] }} days</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- 5. Low Stock Data (Custom, just list them) -->
    <div id="details-low_stock" style="display:none;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $p)
                    @if($p->inventory_stock < 10)
                    <tr>
                        <td>{{ $p->product_name }}</td>
                        <td>{{ $p->inventory_stock }}</td>
                        <td>{{ $p->inventory_stock == 0 ? 'Out' : 'Low' }}</td>
                    </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- MODAL TEMPLATE -->
    <div id="report-modal" class="report-modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">Report Title</h2>
                <button class="modal-close" onclick="closeReportModal()"><i class="fas fa-times"></i></button>
            </div>
            <div class="modal-body" id="modal-body">
                <!-- Content injected via JS -->
            </div>
        </div>
    </div>

    <script>
        function toggleReport(key) {
            const modal = document.getElementById('report-modal');
            const titleEl = document.getElementById('modal-title');
            const bodyEl = document.getElementById('modal-body');
            const sourceEl = document.getElementById('details-' + key);
            
            if (!sourceEl) return;

            // Set Title
            const titles = {
                'stock_overview': '📦 Stock Overview',
                'school_stock': '🏫 Stock by School',
                'category_stock': '📂 Stock by Category',
                'aging': '🕰️ Aging Inventory (>30 Days)',
                'low_stock': '⚠️ Low/Out of Stock Alerts'
            };
            titleEl.textContent = titles[key] || 'Report Details';

            // Parse Data
            const table = sourceEl.querySelector('table');
            const chartType = table.getAttribute('data-chart-type');
            
            // Build Content
            if (chartType) {
                // Split View: Chart + Table
                const labels = [];
                const values = [];
                // Extract data
                table.querySelectorAll('tbody tr').forEach(row => {
                    labels.push(row.cells[0].textContent);
                    values.push(parseInt(row.cells[1].textContent.replace(/,/g,'')));
                });

                bodyEl.innerHTML = `
                    <div class="modal-split-view">
                        <div class="chart-box">
                            <canvas id="modalChart"></canvas>
                        </div>
                        <div class="table-box">
                            <table class="modal-data-table">
                                ${table.innerHTML}
                            </table>
                        </div>
                    </div>
                `;

                // Render Chart
                setTimeout(() => {
                    new Chart(document.getElementById('modalChart'), {
                        type: chartType,
                        data: {
                            labels: labels,
                            datasets: [{
                                label: table.getAttribute('data-chart-label') || 'Data',
                                data: values,
                                backgroundColor: [
                                    '#490d59', '#7c3aed', '#a78bfa', '#c4b5fd', '#ddd6fe', '#e9d5ff'
                                ],
                                borderWidth: 0
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }, 100);

            } else {
                // Simple Table View
                bodyEl.innerHTML = `
                    <div class="table-box">
                        <table class="modal-data-table">
                            ${table.innerHTML}
                        </table>
                    </div>
                `;
            }

            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeReportModal() {
            document.getElementById('report-modal').classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close on click outside
        window.onclick = function(event) {
            const modal = document.getElementById('report-modal');
            if (event.target == modal) {
                closeReportModal();
            }
        }
    </script>

    <script>
        function toggleReportVariants(productId) {
            const row = document.getElementById('report-variants-' + productId);
            const icon = document.getElementById('report-icon-' + productId);
            if (row.style.display === 'none') {
                row.style.display = 'table-row';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                row.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    </script>
@endsection
