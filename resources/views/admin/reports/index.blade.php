@extends('admin.layouts.base')

@section('title', 'Reports & Analytics | The Skool Store')
@section('page_heading', 'Reports & Analytics')
@section('page_subheading', 'Central place for master admin to drill into orders, revenue, school/grade/category performance and returns.')


@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('styles')
    <style>
        .report-metrics { 
            margin-top:20px;
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(110px,1fr));
            gap:16px;
        }
        
        .metric-item { 
            background:#f9fafb;
            padding:16px 12px;
            border-radius:12px;
            transition:all 0.2s;
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
        /* Keep original filter styles */
        .filters { display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; }
        .filters button, .filters a.reset { border-radius:12px;font-weight:600;text-align:center; }
        .filters button { border:none;background:#490d59;color:#fff;padding:10px 16px;cursor:pointer;transition:all 0.2s; }
        .filters button:hover { background:#5e1170; }
        .filters a.reset { border:1px solid #d0d5dd;color:#475467;padding:10px 16px;text-decoration:none;display:inline-block; }
        .filters a.reset:hover { background:#f9fafb; }
        .filters input, .filters select { 
            border:1px solid #d0d5dd;
            border-radius:12px;
            padding:10px 16px;
            font-size:14px;
            color:#111827;
            background:#fff;
        }
        .filters input:focus, .filters select:focus { 
            outline:none;
            border-color:#490d59;
            box-shadow:0 0 0 3px rgba(73,13,89,0.1);
        }
        
        /* Modern Report Grid */
        .report-grid { 
            display:grid;
            grid-template-columns:repeat(auto-fill,minmax(320px,1fr));
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
        }
        
        .report-card::before {
            content:'';
            position:absolute;
            top:0;
            left:0;
            right:0;
            height:4px;
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
        
        .report-card:hover::before {
            transform:scaleX(1);
        }
        
        .report-card.active { 
            border-color:#490d59;
            background:linear-gradient(135deg, #faf5ff 0%, #fff 100%);
            box-shadow:0 20px 50px rgba(73,13,89,0.15);
        }
        
        .report-card.active::before {
            transform:scaleX(1);
        }
        
        .report-card-header { 
            display:flex;
            justify-content:space-between;
            align-items:flex-start;
            margin-bottom:20px;
        }
        
        .report-card h4 { 
            margin:0 0 6px;
            color:#111827;
            font-size:18px;
            font-weight:700;
            letter-spacing:-0.02em;
        }
        
        .report-card p { 
            margin:0;
            color:#6b7280;
            font-size:13px;
            line-height:1.5;
        }
        
        .report-icon {
            width:36px;
            height:36px;
            background:linear-gradient(135deg, #490d59, #7c3aed);
            border-radius:10px;
            display:flex;
            align-items:center;
            justify-content:center;
            color:#fff;
            font-size:18px;
            transition:transform 0.3s ease;
            flex-shrink:0;
        }
        
        .report-card:hover .report-icon {
            transform:rotate(5deg) scale(1.1);
        }
        
        .report-card.active .report-icon {
            transform:rotate(360deg);
        }
        
        
        /* Modal Metric Cards - NO wrapping */
        .modal-metrics-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
            gap:20px;
            margin-bottom:32px;
        }
        
        .modal-metric-card {
            background:linear-gradient(135deg, #f9fafb 0%, #fff 100%);
            padding:24px;
            border-radius:16px;
            border:2px solid #e5e7eb;
            transition:all 0.2s;
        }
        
        .modal-metric-card:hover {
            border-color:#490d59;
            box-shadow:0 8px 24px rgba(73,13,89,0.12);
            transform:translateY(-2px);
        }
        
        .modal-metric-label {
            font-size:12px;
            font-weight:700;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:0.5px;
            margin-bottom:8px;
        }
        
        .modal-metric-value {
            font-size:32px;
            font-weight:900;
            color:#490d59;
            line-height:1.1;
            white-space:nowrap;
            overflow:hidden;
            text-overflow:ellipsis;
        }
        
        .modal-metric-sublabel {
            font-size:11px;
            color:#9ca3af;
            margin-top:4px;
        }
        
        
        /* Modal Styles */
        .report-modal {
            display:none;
            position:fixed;
            top:0;
            left:0;
            width:100%;
            height:100%;
            background:rgba(0,0,0,0.7);
            z-index:10000;
            animation:fadeIn 0.3s ease;
            overflow-y:auto;
        }
        
        .report-modal.active {
            display:flex;
            align-items:flex-start;
            justify-content:center;
            padding:40px 20px;
        }
        
        @keyframes fadeIn {
            from { opacity:0; }
            to { opacity:1; }
        }
        
        .modal-content {
            background:#fff;
            border-radius:24px;
            max-width:1200px;
            width:100%;
            max-height:90vh;
            overflow-y:auto;
            box-shadow:0 25px 50px rgba(0,0,0,0.3);
            animation:slideUp 0.3s ease;
            position:relative;
        }
        
        @keyframes slideUp {
            from { opacity:0; transform:translateY(30px); }
            to { opacity:1; transform:translateY(0); }
        }
        
        .modal-header {
            padding:32px 40px;
            border-bottom:2px solid #f3f4f6;
            display:flex;
            justify-content:space-between;
            align-items:center;
            position:sticky;
            top:0;
            background:#fff;
            z-index:10;
            border-radius:24px 24px 0 0;
        }
        
        .modal-header h2 {
            margin:0;
            font-size:28px;
            font-weight:800;
            color:#111827;
            display:flex;
            align-items:center;
            gap:12px;
        }
        
        .modal-close {
            width:40px;
            height:40px;
            border-radius:50%;
            background:#f3f4f6;
            border:none;
            cursor:pointer;
            display:flex;
            align-items:center;
            justify-content:center;
            font-size:24px;
            color:#6b7280;
            transition:all 0.2s;
        }
        
        .modal-close:hover {
            background:#490d59;
            color:#fff;
            transform:rotate(90deg);
        }
        
        
        .modal-body {
            padding:40px;
        }
        
        /* Status Grid for Orders */
        .status-grid {
            display:grid;
            grid-template-columns:repeat(auto-fit, minmax(150px, 1fr));
            gap:16px;
            margin-top:24px;
        }
        
        .status-card {
            background:#fff;
            border:2px solid #e5e7eb;
            border-radius:12px;
            padding:20px;
            text-align:center;
            transition:all 0.2s;
        }
        
        .status-card:hover {
            border-color:#490d59;
            box-shadow:0 4px 16px rgba(73,13,89,0.1);
        }
        
        .status-card .status-count {
            font-size:36px;
            font-weight:900;
            color:#490d59;
            margin-bottom:8px;
        }
        
        .status-card .status-label {
            font-size:12px;
            font-weight:600;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        
        /* Progress Bar */
        .progress-bar-container {
            background:#f3f4f6;
            height:8px;
            border-radius:20px;
            overflow:hidden;
            margin-top:8px;
        }
        
        .progress-bar {
            height:100%;
            background:linear-gradient(90deg, #490d59, #7c3aed);
            transition:width 0.5s ease;
            border-radius:20px;
        }
        
        
        .report-details { 
            display:none;
        }
        
        .report-details h4 {
            margin:0 0 16px;
            font-size:15px;
            font-weight:700;
            color:#111827;
            letter-spacing:-0.01em;
        }
        
        .report-details table { 
            width:100%;
            border-collapse:separate;
            border-spacing:0;
            margin-top:12px;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 1px 3px rgba(0,0,0,0.04);
        }
        
        .report-details th,
        .report-details td { 
            padding:12px 16px;
            text-align:left;
            font-size:13px;
        }
        
        .report-details th { 
            text-transform:uppercase;
            letter-spacing:.05em;
            color:#6b7280;
            font-size:11px;
            font-weight:700;
            background:#f9fafb;
            border-bottom:2px solid #e5e7eb;
        }
        
        .report-details td {
            color:#374151;
            border-bottom:1px solid #f3f4f6;
        }
        
        .report-details tbody tr:last-child td {
            border-bottom:none;
        }
        
        
        .report-details tbody tr:hover {
            background:#fafbfc;
        }
        
        /* Enhanced Modal Table Styles */
        .modal-data-table {
            width:100%;
            border-collapse:separate;
            border-spacing:0;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
            box-shadow:0 2px 8px rgba(0,0,0,0.06);
            margin-top:20px;
        }
        
        .modal-data-table th,
        .modal-data-table td {
            padding:14px 18px;
            text-align:left;
            font-size:13px;
        }
        
        .modal-data-table th {
            background:linear-gradient(135deg, #490d59, #5e1170);
            color:#fff;
            font-weight:700;
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:0.5px;
            border-bottom:3px solid #7c3aed;
        }
        
        .modal-data-table td {
            color:#374151;
            border-bottom:1px solid #f3f4f6;
            font-weight:500;
        }
        
        .modal-data-table tbody tr:last-child td {
            border-bottom:none;
        }
        
        .modal-data-table tbody tr:hover {
            background:linear-gradient(135deg, #faf5ff 0%, #fff 100%);
        }
        
        .modal-data-table tbody tr:nth-child(odd) {
            background:#fafbfc;
        }
        
        .rank-badge {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            width:28px;
            height:28px;
            border-radius:50%;
            background:linear-gradient(135deg, #490d59, #7c3aed);
            color:#fff;
            font-weight:800;
            font-size:12px;
            margin-right:8px;
        }
        
        
        
        
        /* Scrollable Table Container */
        .table-scroll-container {
            max-height:500px;
            overflow-y:auto;
            overflow-x:hidden;
            scrollbar-width:none; /* Firefox */
            -ms-overflow-style:none; /* IE and Edge */
        }
        
        .table-scroll-container::-webkit-scrollbar {
            width:0px;
            display:none;
        }
        
        
        .report-details .stat-grid { 
            display:grid;
            grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
            gap:16px;
            margin-bottom:20px;
        }
        
        .stat-box { 
            margin:10px;
            padding:10px;
            background:linear-gradient(135deg, #fafafa 0%, #fff 100%);
            border-radius:16px;
            border:1px solid #e5e7eb;
            transition:all 0.2s;
        }
        
        .stat-box:hover {
            border-color:#490d59;
            box-shadow:0 4px 12px rgba(73,13,89,0.08);
            transform:translateY(-2px);
        }
        
        .stat-box .label { 
            font-size:11px;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:0.5px;
            margin-bottom:8px;
            font-weight:600;
        }
        
        .stat-box .value { 
            font-size:28px;
            font-weight:800;
            color:#111827;
            letter-spacing:-0.02em;
        }
        
        .export-section {
            display:flex;
            justify-content:space-between;
            align-items:center;
            margin-bottom:24px;
            padding:20px 24px;
            background:linear-gradient(135deg, #faf5ff 0%, #fff 100%);
            border-radius:16px;
            border:1px solid #e9d5ff;
        }
        
        .export-section h3 {
            margin:0;
            color:#111827;
            font-size:20px;
            font-weight:700;
            letter-spacing:-0.02em;
        }
        
        .export-links { 
            display:flex;
            gap:10px;
        }
        
        .export-links a { 
            border:1px solid #e5e7eb;
            border-radius:12px;
            padding:10px 18px;
            font-size:13px;
            font-weight:600;
            color:#490d59;
            text-decoration:none;
            transition:all 0.2s;
            background:#fff;
            display:flex;
            align-items:center;
            gap:6px;
        }
        
        .export-links a:hover { 
            background:#490d59;
            color:#fff;
            border-color:#490d59;
            transform:translateY(-2px);
            box-shadow:0 4px 12px rgba(73,13,89,0.2);
        }
        
        /* Latest Orders Section */
        .orders-section {
            background:#fff;
            border-radius:20px;
            padding:28px;
            border:1px solid #e5e7eb;
            box-shadow:0 1px 3px rgba(0,0,0,0.02);
        }
        
        .orders-section h3 {
            margin:0 0 20px;
            color:#111827;
            font-size:20px;
            font-weight:700;
            letter-spacing:-0.02em;
        }
        
        .orders-section table { 
            width:100%;
            border-collapse:separate;
            border-spacing:0;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
        }
        
        .orders-section th,
        .orders-section td { 
            padding:14px 16px;
            text-align:left;
            font-size:13px;
        }
        
        .orders-section th { 
            text-transform:uppercase;
            letter-spacing:.05em;
            color:#6b7280;
            font-size:11px;
            font-weight:700;
            background:#f9fafb;
            border-bottom:2px solid #e5e7eb;
        }
        
        .orders-section td {
            color:#374151;
            border-bottom:1px solid #f3f4f6;
        }
        
        .orders-section tbody tr:last-child td {
            border-bottom:none;
        }
        
        .orders-section tbody tr:hover {
            background:#fafbfc;
        }
        
        .badge { 
            display:inline-block;
            padding:6px 12px;
            border-radius:8px;
            font-size:11px;
            font-weight:700;
            text-transform:uppercase;
            letter-spacing:0.5px;
        }
        
        .badge-success { 
            background:linear-gradient(135deg, #d1fae5, #a7f3d0);
            color:#065f46;
        }
        
        .badge-warning { 
            background:linear-gradient(135deg, #fef3c7, #fde68a);
            color:#92400e;
        }
        
        .badge-danger { 
            background:linear-gradient(135deg, #fee2e2, #fecaca);
            color:#991b1b;
        }
        
        
        .badge-info { 
            background:linear-gradient(135deg, #dbeafe, #bfdbfe);
            color:#1e40af;
        }
        
        /* View Toggle Button */
        .view-toggle {
            display:inline-flex;
            background:#f3f4f6;
            border-radius:12px;
            padding:4px;
            gap:4px;
        }
        
        .view-toggle button {
            padding:10px 20px;
            border:none;
            background:transparent;
            border-radius:8px;
            font-weight:600;
            font-size:14px;
            color:#6b7280;
            cursor:pointer;
            transition:all 0.2s;
            display:flex;
            align-items:center;
            gap:8px;
        }
        
        .view-toggle button.active {
            background:linear-gradient(135deg, #490d59, #5e1170);
            color:#fff;
            box-shadow:0 2px 8px rgba(73,13,89,0.3);
        }
        
        .view-toggle button:hover:not(.active) {
            background:#e5e7eb;
            color:#374151;
        }
        
        
        #tableView {
            display:none;
        }
        
        /* Simple Table View Styles (like Orders page) */
        #tableView table {
            width:100%;
            border-collapse:collapse;
            background:#fff;
            border-radius:12px;
            overflow:hidden;
        }
        
        #tableView th {
            background:#f9fafb;
            padding:14px;
            text-align:left;
            font-size:11px;
            font-weight:700;
            color:#6b7280;
            text-transform:uppercase;
            letter-spacing:0.5px;
            border-bottom:2px solid #e5e7eb;
        }
        
        #tableView td {
            padding:14px;
            color:#374151;
            border-bottom:1px solid #f3f4f6;
            font-size:13px;
        }
        
        #tableView tbody tr:hover {
            background:#f9fafb;
        }
        
        #tableView tbody tr:last-child td {
            border-bottom:none;
        }
        
        /* Custom Pagination Styling (from Orders page) */
        .pagination-container {
            padding: 20px;
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

        .pagination {
            display: flex !important;
            align-items: center !important;
            justify-content: center !important;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 0;
            padding: 0;
            list-style: none;
            margin-left: 0;
            margin-right: 0;
        }
        
        .pagination > * {
            margin: 0 !important;
        }
        
        .pagination-container nav a, 
        .pagination-container nav span[aria-disabled], 
        .pagination-container nav span[aria-current="page"] > span,
        .pagination a,
        .pagination span,
        .pagination .page-link {
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            min-width: 42px !important;
            height: 42px !important;
            padding: 0 14px !important;
            border-radius: 8px !important;
            text-decoration: none !important;
            font-size: 14px !important;
            font-weight: 500 !important;
            transition: all 0.2s ease !important;
            border: 1px solid #e5e7eb !important;
            background-color: #ffffff !important;
            color: #6b7280 !important;
            margin: 0 2px !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }
        
        .pagination-container nav a:hover,
        .pagination a:hover,
        .pagination .page-link:hover {
            background-color: #f9fafb !important;
            border-color: #490d59 !important;
            color: #490d59 !important;
            transform: translateY(-1px);
            box-shadow: 0 2px 4px rgba(73, 13, 89, 0.15) !important;
        }

        .pagination-container nav span[aria-current="page"] > span,
        .pagination span[aria-current="page"],
        .pagination .active span,
        .pagination .page-item.active .page-link,
        .pagination .page-link.active {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color: #ffffff !important;
            font-weight: 600 !important;
        }

        .pagination-container nav span[aria-disabled],
        .pagination span[aria-disabled="true"],
        .pagination .page-item.disabled .page-link,
        .pagination .page-link.disabled {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
            cursor: not-allowed !important;
            opacity: 0.6 !important;
            pointer-events: none;
        }
        
        .pagination-container nav a[rel="prev"],
        .pagination-container nav a[rel="next"],
        .pagination a[rel="prev"],
        .pagination a[rel="next"],
        .pagination .page-link[rel="prev"],
        .pagination .page-link[rel="next"] {
            background-color: #ffffff !important;
            border: 1px solid #d1d5db !important;
            color: #490d59 !important;
            font-weight: 600 !important;
            padding: 0 16px !important;
            min-width: auto !important;
        }
        
        .pagination-container nav a[rel="prev"]:hover,
        .pagination-container nav a[rel="next"]:hover,
        .pagination a[rel="prev"]:hover,
        .pagination a[rel="next"]:hover {
            background-color: #490d59 !important;
            color: #ffffff !important;
            border-color: #490d59 !important;
        }
        
        .pagination-container nav svg,
        .pagination svg {
            width: 16px;
            height: 16px;
        }
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:20px;">
        <form method="GET" class="filters">
            <select name="school_id[]" multiple placeholder="Select School">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(in_array($school->id, (array)($filters['school_id'] ?? [])))>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade[]" multiple placeholder="Select Grade">
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(in_array($grade, (array)($filters['grade'] ?? [])))>{{ $grade }}</option>
                @endforeach
            </select>
            <select name="category[]" multiple placeholder="Select Category">
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(in_array($category, (array)($filters['category'] ?? [])))>{{ $category }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" placeholder="From Date">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" placeholder="To Date">
            <input type="text" name="product_name" placeholder="Product name" value="{{ $filters['product_name'] ?? '' }}">
            <select name="status[]" multiple placeholder="Order Status">
                @foreach(['processing','shipped','delivered','returned','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(in_array($status, (array)($filters['status'] ?? [])))>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit">Apply Filters</button>
            <a href="{{ route('master.admin.reports.index') }}" class="reset">Reset</a>
        </form>
    </section>

    <section class="card" style="margin-bottom:24px;">
        <div class="export-section">
            <h3>📊 Reports Dashboard</h3>
            <div style="display:flex;align-items:center;gap:16px;">
                <div class="view-toggle">
                    <button onclick="switchView('card')" id="cardViewBtn" class="active">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <rect x="1" y="1" width="6" height="6" rx="1"/>
                            <rect x="9" y="1" width="6" height="6" rx="1"/>
                            <rect x="1" y="9" width="6" height="6" rx="1"/>
                            <rect x="9" y="9" width="6" height="6" rx="1"/>
                        </svg>
                        Grid
                    </button>
                    <button onclick="switchView('table')" id="tableViewBtn">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                            <rect x="1" y="2" width="14" height="2" rx="1"/>
                            <rect x="1" y="6" width="14" height="2" rx="1"/>
                            <rect x="1" y="10" width="14" height="2" rx="1"/>
                        </svg>
                        Table
                    </button>
                </div>
                <div class="export-links">
                    <a href="{{ route('master.admin.reports.export', ['type' => 'csv'] + request()->query()) }}">📄 CSV</a>
                    <a href="{{ route('master.admin.reports.export', ['type' => 'excel'] + request()->query()) }}">📊 Excel</a>
                    <a href="{{ route('master.admin.reports.export', ['type' => 'pdf'] + request()->query()) }}">📑 PDF</a>
                </div>
            </div>
        </div>
        
        <div id="cardView" class="report-grid">
            @foreach($reportTypes as $index => $report)
                @php
                    $key = $report['key'];
                    $data = $reportData[$key] ?? [];
                    $icons = [
                        'orders' => '📦',
                        'revenue' => '💰',
                        'product_performance' => '⭐',
                        'stock' => '📊',
                        'shipping_cost' => '🚚',
                        'tax' => '💵',
                        'school_wise' => '🏫',
                        'grade_wise' => '🎓',
                        'category_wise' => '📂',
                        'return_exchange' => '↩️'
                    ];
                @endphp
                <article class="report-card" onclick="toggleReport('{{ $key }}')" id="report-{{ $key }}">
                    <div class="report-card-header">
                        <div style="flex:1;">
                            <h4>{{ $report['label'] }}</h4>
                            <p>{{ $report['description'] }}</p>
                        </div>
                        <div class="report-icon">{{ $icons[$key] ?? '📈' }}</div>
                    </div>
                    
                    @if($key === 'orders')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ number_format($data['total'] ?? 0) }}</div>
                                <div class="metric-label">Total Orders</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['avg_value'] ?? 0, 0) }}</div>
                                <div class="metric-label">Avg Value</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['fulfilment_sla'] ?? 0 }}d</div>
                                <div class="metric-label">Avg SLA</div>
                            </div>
                        </div>
                        <div class="report-details">
                            <div class="stat-grid">
                                @foreach($data['by_status'] ?? [] as $status => $count)
                                    <div class="stat-box">
                                        <div class="label">{{ ucfirst($status) }}</div>
                                        <div class="value">{{ number_format($count) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @elseif($key === 'revenue')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['gross'] ?? 0, 0) }}</div>
                                <div class="metric-label">Gross Revenue</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['net'] ?? 0, 0) }}</div>
                                <div class="metric-label">Net Revenue</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['refunds'] ?? 0, 0) }}</div>
                                <div class="metric-label">Refunds</div>
                            </div>
                        </div>
                        <div class="report-details">
                            <div class="stat-grid">
                                <div class="stat-box">
                                    <div class="label">Tax Collected</div>
                                    <div class="value">₹{{ number_format($data['tax'] ?? 0, 2) }}</div>
                                </div>
                                <div class="stat-box">
                                    <div class="label">Shipping Costs</div>
                                    <div class="value">₹{{ number_format($data['shipping'] ?? 0, 2) }}</div>
                                </div>
                            </div>
                        </div>
                    @elseif($key === 'product_performance')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ count($data['best_sellers'] ?? []) }}</div>
                                <div class="metric-label">Top Products</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['return_rate'] ?? 0 }}%</div>
                                <div class="metric-label">Return Rate</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ number_format($data['total_sold'] ?? 0) }}</div>
                                <div class="metric-label">Total Sold</div>
                            </div>
                        </div>
                        <div class="report-details">
                            <h4>🏆 Best Sellers</h4>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Quantity</th>
                                        <th>Revenue</th>
                                        <th>Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($data['best_sellers'] ?? [] as $product)
                                        <tr>
                                            <td>{{ $product['name'] }}</td>
                                            <td>{{ number_format($product['quantity']) }}</td>
                                            <td>₹{{ number_format($product['revenue'], 2) }}</td>
                                            <td>{{ $product['orders'] }}</td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="4" style="text-align:center;padding:24px;color:#9ca3af;">No data available</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    @elseif($key === 'stock')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ number_format($data['total_stock'] ?? 0) }}</div>
                                <div class="metric-label">Total Stock</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['in_stock_count'] ?? 0 }}</div>
                                <div class="metric-label">In Stock</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['out_of_stock_count'] ?? 0 }}</div>
                                <div class="metric-label">Out of Stock</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['low_stock_count'] ?? 0 }}</div>
                                <div class="metric-label">Low Stock</div>
                            </div>
                        </div>
                        <div class="report-details">
                            @if(count($data['aging'] ?? []) > 0)
                                <h4>⏰ Stock Aging (Top 10)</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Stock</th>
                                            <th>Days Since Update</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['aging'] as $item)
                                            <tr>
                                                <td>{{ $item['name'] }}</td>
                                                <td>{{ $item['stock'] }}</td>
                                                <td>{{ $item['days'] }} days</td>
                                                <td>{{ $item['category'] ?? '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($key === 'shipping_cost')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['total'] ?? 0, 0) }}</div>
                                <div class="metric-label">Total Shipping</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['average'] ?? 0, 0) }}</div>
                                <div class="metric-label">Avg per Order</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ number_format($data['orders_with_shipping'] ?? 0) }}</div>
                                <div class="metric-label">Orders</div>
                            </div>
                        </div>
                    @elseif($key === 'tax')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">₹{{ number_format($data['total'] ?? 0, 0) }}</div>
                                <div class="metric-label">Total Tax</div>
                            </div>
                        </div>
                        <div class="report-details">
                            @if(count($data['by_category'] ?? []) > 0)
                                <h4>📋 Tax by Category</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Tax Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['by_category'] as $category => $amount)
                                            <tr>
                                                <td>{{ $category }}</td>
                                                <td>₹{{ number_format($amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($key === 'school_wise')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['total_schools'] ?? 0 }}</div>
                                <div class="metric-label">Schools</div>
                            </div>
                        </div>
                        <div class="report-details">
                            @if(count($data['data'] ?? []) > 0)
                                <h4>🏫 Performance by School</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>School</th>
                                            <th>Orders</th>
                                            <th>Revenue</th>
                                            <th>Avg Order Value</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['data'] as $item)
                                            <tr>
                                                <td>{{ $item['school'] }}</td>
                                                <td>{{ number_format($item['orders']) }}</td>
                                                <td>₹{{ number_format($item['revenue'], 2) }}</td>
                                                <td>₹{{ number_format($item['avg_order_value'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($key === 'grade_wise')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['total_grades'] ?? 0 }}</div>
                                <div class="metric-label">Grades</div>
                            </div>
                        </div>
                        <div class="report-details">
                            @if(count($data['data'] ?? []) > 0)
                                <h4>🎓 Demand by Grade</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Grade</th>
                                            <th>Orders</th>
                                            <th>Quantity</th>
                                            <th>Revenue</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['data'] as $item)
                                            <tr>
                                                <td>{{ $item['grade'] }}</td>
                                                <td>{{ number_format($item['orders']) }}</td>
                                                <td>{{ number_format($item['quantity']) }}</td>
                                                <td>₹{{ number_format($item['revenue'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($key === 'category_wise')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['total_categories'] ?? 0 }}</div>
                                <div class="metric-label">Categories</div>
                            </div>
                        </div>
                        <div class="report-details">
                            @if(count($data['data'] ?? []) > 0)
                                <h4>📂 Performance by Category</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Category</th>
                                            <th>Orders</th>
                                            <th>Quantity</th>
                                            <th>Revenue</th>
                                            <th>Avg Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['data'] as $item)
                                            <tr>
                                                <td>{{ $item['category'] }}</td>
                                                <td>{{ number_format($item['orders']) }}</td>
                                                <td>{{ number_format($item['quantity']) }}</td>
                                                <td>₹{{ number_format($item['revenue'], 2) }}</td>
                                                <td>₹{{ number_format($item['avg_price'], 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @elseif($key === 'return_exchange')
                        <div class="report-metrics">
                            <div class="metric-item">
                                <div class="metric-value">{{ number_format($data['total'] ?? 0) }}</div>
                                <div class="metric-label">Total Requests</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['return_rate'] ?? 0 }}%</div>
                                <div class="metric-label">Return Rate</div>
                            </div>
                            <div class="metric-item">
                                <div class="metric-value">{{ $data['avg_processing_time'] ?? 0 }}d</div>
                                <div class="metric-label">Avg Processing</div>
                            </div>
                        </div>
                        <div class="report-details">
                            <div class="stat-grid">
                                <div class="stat-box">
                                    <div class="label">Total Refunds</div>
                                    <div class="value">₹{{ number_format($data['total_refunds'] ?? 0, 2) }}</div>
                                </div>
                            </div>
                            @if(count($data['by_status'] ?? []) > 0)
                                <h4>📊 By Status</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['by_status'] as $status => $count)
                                            <tr>
                                                <td><span class="badge badge-info">{{ ucfirst($status) }}</span></td>
                                                <td>{{ number_format($count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                            @if(count($data['by_reason'] ?? []) > 0)
                                <h4>📝 By Reason</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Reason</th>
                                            <th>Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($data['by_reason'] as $reason => $count)
                                            <tr>
                                                <td>{{ $reason }}</td>
                                                <td>{{ number_format($count) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        </div>
                    @endif
                </article>
            @endforeach
        </div>
        
        <div id="tableView" style="margin-top:24px;">
            <table>
                <thead>
                    <tr>
                        <th style="width:50px;">#</th>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>School</th>
                        <th>Student</th>
                        <th>Grade</th>
                        <th>Category</th>
                        <th>Item</th>
                        <th style="text-align:center;">Qty</th>
                        <th style="text-align:right;">Amount</th>
                        <th style="text-align:right;">Tax</th>
                        <th style="text-align:right;">Shipping</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $index => $order)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td style="font-weight:600;">{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('Y-m-d') }}</td>
                            <td>{{ $order->school?->name ?? '—' }}</td>
                            <td>{{ $order->student_name ?? '—' }}</td>
                            <td style="text-align:center;">{{ $order->grade ?? '—' }}</td>
                            <td>{{ $order->category ?? '—' }}</td>
                            <td>{{ $order->item_name }}</td>
                            <td style="text-align:center;">{{ $order->quantity }}</td>
                            <td style="text-align:right;font-weight:600;">₹{{ number_format($order->total_amount, 2) }}</td>
                            <td style="text-align:right;">₹{{ number_format($order->tax ?? 0, 2) }}</td>
                            <td style="text-align:right;">₹{{ number_format($order->shipping ?? 0, 2) }}</td>
                            <td>
                                <span class="badge badge-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : 'info') }}">
                                    {{ ucfirst($order->order_status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="13" style="text-align:center;padding:40px;color:#9ca3af;">
                                No orders found matching the filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            
            @if(method_exists($orders, 'links'))
                <div class="pagination-container">
                    {{ $orders->onEachSide(1)->links() }}
                </div>
            @endif
        </div>
    </section>

    <section class="orders-section">
        <h3>📋 Latest Orders Snapshot</h3>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->school?->name ?? '—' }}</td>
                        <td>{{ $order->grade ?? '—' }}</td>
                        <td>{{ $order->item_name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge badge-{{ $order->order_status === 'delivered' ? 'success' : ($order->order_status === 'cancelled' ? 'danger' : 'info') }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="text-align:center;padding:24px;color:#9ca3af;">No orders found matching the filters.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection


@push('scripts')
    <script>
        function switchView(view) {
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');
            const cardBtn = document.getElementById('cardViewBtn');
            const tableBtn = document.getElementById('tableViewBtn');
            
            if (view === 'card') {
                cardView.style.display = 'grid';
                tableView.style.display = 'none';
                cardBtn.classList.add('active');
                tableBtn.classList.remove('active');
            } else {
                cardView.style.display = 'none';
                tableView.style.display = 'block';
                cardBtn.classList.remove('active');
                tableBtn.classList.add('active');
            }
            
            // Save view preference in localStorage
            localStorage.setItem('reportsView', view);
            
            // Update URL to include view parameter
            const url = new URL(window.location);
            url.searchParams.set('view', view);
            window.history.replaceState({}, '', url);
        }
        
        // Restore view on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Check URL parameter first, then localStorage
            const urlParams = new URLSearchParams(window.location.search);
            const urlView = urlParams.get('view');
            const savedView = urlView || localStorage.getItem('reportsView') || 'card';
            
            if (savedView === 'table') {
                switchView('table');
            }
            
            // Update all pagination links to include current view
            updatePaginationLinks();
        });
        
        function updatePaginationLinks() {
            const currentView = localStorage.getItem('reportsView') || 'card';
            const paginationLinks = document.querySelectorAll('.pagination-container a');
            
            paginationLinks.forEach(link => {
                const url = new URL(link.href);
                url.searchParams.set('view', currentView);
                link.href = url.toString();
            });
        }
        
        function toggleReport(key) {
            const card = document.getElementById('report-' + key);
            if (!card) return;
            
            const detailsEl = card.querySelector('.report-details');
            if (!detailsEl) return;
            
            const title = card.querySelector('h4').textContent;
            const icon = card.querySelector('.report-icon').textContent;
            
            let modal = document.getElementById('report-modal');
            if (!modal) {
                modal = document.createElement('div');
                modal.id = 'report-modal';
                modal.className = 'report-modal';
                modal.innerHTML = `
                    <div class="modal-content">
                        <div class="modal-header">
                            <h2><span class="report-icon" style="width:48px;height:48px;font-size:24px;background:linear-gradient(135deg,#490d59,#7c3aed);border-radius:12px;display:inline-flex;align-items:center;justify-content:center;color:#fff;"></span> <span></span></h2>
                            <button class="modal-close" onclick="closeReportModal()">&times;</button>
                        </div>
                        <div class="modal-body"></div>
                    </div>
                `;
                document.body.appendChild(modal);
                
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) closeReportModal();
                });
                
                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape' && modal.classList.contains('active')) closeReportModal();
                });
            }
            
            
            modal.querySelector('.modal-header .report-icon').textContent = icon;
            modal.querySelector('.modal-header h2 span:last-child').textContent = title;
            
            const modalBody = modal.querySelector('.modal-body');
            
            // Destroy previous chart if exists
            if (window.currentChart) {
                window.currentChart.destroy();
            }
            
            if (key === 'orders') {
                const statBoxes = detailsEl.querySelectorAll('.stat-box');
                const statusData = {};
                statBoxes.forEach(box => {
                    const label = box.querySelector('.label').textContent.trim();
                    const value = parseInt(box.querySelector('.value').textContent.replace(/,/g, ''));
                    statusData[label] = value;
                });
                
                const total = Object.values(statusData).reduce((a, b) => a + b, 0);
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📊 Orders Distribution</h3>
                            <canvas id="chartCanvas" style="max-height:300px;"></canvas>
                        </div>
                        <div>
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📋 Status Breakdown</h3>
                            <div class="status-grid">
                                ${Object.entries(statusData).map(([status, count]) => `
                                    <div class="status-card">
                                        <div class="status-count">${count.toLocaleString()}</div>
                                        <div class="status-label">${status}</div>
                                        <div class="progress-bar-container">
                                            <div class="progress-bar" style="width:${total > 0 ? (count / total * 100).toFixed(1) : 0}%"></div>
                                        </div>
                                    </div>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('doughnut', Object.keys(statusData), Object.values(statusData), 'Orders by Status'), 50);
                
            } else if (key === 'revenue') {
                const statBoxes = detailsEl.querySelectorAll('.stat-box');
                const revenueData = [];
                const labels = [], values = [];
                statBoxes.forEach(box => {
                    const label = box.querySelector('.label').textContent.trim();
                    const value = box.querySelector('.value').textContent.trim();
                    revenueData.push({ label, value });
                    labels.push(label);
                    values.push(parseFloat(value.replace(/[₹,]/g, '')));
                });
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">💰 Revenue Distribution</h3>
                            <canvas id="chartCanvas" style="max-height:300px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📊 Revenue Breakdown</h3>
                            <table class="modal-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Component</th>
                                        <th style="text-align:right;">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${revenueData.map((item, idx) => `
                                        <tr>
                                            <td><span class="rank-badge">${idx + 1}</span></td>
                                            <td style="font-weight:600;color:#111827;">${item.label}</td>
                                            <td style="text-align:right;font-weight:700;color:#490d59;">${item.value}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('bar', labels, values, 'Revenue Components (₹)'), 50);
                
            } else if (key === 'product_performance') {
                const table = detailsEl.querySelector('table tbody');
                const products = [];
                const labels = [], revenues = [];
                
                if (table) {
                    const rows = table.querySelectorAll('tr');
                    rows.forEach((row, idx) => {
                        if (row.cells.length >= 4) {
                            const product = {
                                name: row.cells[0].textContent.trim(),
                                quantity: row.cells[1].textContent.trim(),
                                revenue: row.cells[2].textContent.trim(),
                                orders: row.cells[3].textContent.trim()
                            };
                            products.push(product);
                            
                            if (idx < 5) {
                                labels.push(product.name);
                                revenues.push(parseFloat(product.revenue.replace(/[₹,]/g, '')));
                            }
                        }
                    });
                }
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1.2fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">⭐ Top 5 Products</h3>
                            <canvas id="chartCanvas" style="max-height:280px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">🏆 Best Sellers Ranking</h3>
                            <table class="modal-data-table">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">Rank</th>
                                        <th>Product</th>
                                        <th style="text-align:center;">Qty</th>
                                        <th style="text-align:right;">Revenue</th>
                                        <th style="text-align:center;">Orders</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${products.map((p, idx) => `
                                        <tr>
                                            <td><span class="rank-badge">${idx + 1}</span></td>
                                            <td style="font-weight:600;color:#111827;">${p.name}</td>
                                            <td style="text-align:center;">${p.quantity}</td>
                                            <td style="text-align:right;font-weight:700;color:#490d59;">${p.revenue}</td>
                                            <td style="text-align:center;">${p.orders}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('horizontalBar', labels, revenues, 'Revenue (₹)'), 50);
                
            } else if (key === 'stock') {
                const labels = ['In Stock', 'Out of Stock', 'Low Stock'];
                const values = [];
                const metrics = card.querySelectorAll('.metric-item');
                metrics.forEach((m, idx) => {
                    if (idx > 0 && idx <= 3) {
                        values.push(parseInt(m.querySelector('.metric-value').textContent.replace(/,/g, '')));
                    }
                });
                
                // Get aging data
                const table = detailsEl.querySelector('table tbody');
                const agingData = [];
                if (table) {
                    table.querySelectorAll('tr').forEach(row => {
                        if (row.cells.length >= 4) {
                            agingData.push({
                                name: row.cells[0].textContent.trim(),
                                stock: row.cells[1].textContent.trim(),
                                days: Math.floor(parseFloat(row.cells[2].textContent.trim())),
                                category: row.cells[3].textContent.trim()
                            });
                        }
                    });
                }
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📊 Stock Distribution</h3>
                            <canvas id="chartCanvas" style="max-height:300px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">⏰ Stock Aging Analysis</h3>
                            ${agingData.length > 0 ? `
                                <table class="modal-data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product</th>
                                            <th style="text-align:center;">Stock</th>
                                            <th style="text-align:center;">Days Since Update</th>
                                            <th>Category</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${agingData.map((item, idx) => `
                                            <tr>
                                                <td><span class="rank-badge">${idx + 1}</span></td>
                                                <td style="font-weight:600;color:#111827;">${item.name}</td>
                                                <td style="text-align:center;">${item.stock}</td>
                                                <td style="text-align:center;font-weight:700;color:#dc2626;">${item.days}</td>
                                                <td>${item.category}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            ` : '<p style="text-align:center;color:#9ca3af;padding:40px;">No aging data available</p>'}
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('doughnut', labels, values, 'Stock Distribution'), 50);
                
            } else if (key === 'tax') {
                const table = detailsEl.querySelector('table tbody');
                const taxData = [];
                const labels = [], values = [];
                if (table) {
                    table.querySelectorAll('tr').forEach(row => {
                        if (row.cells.length >= 2) {
                            const category = row.cells[0].textContent.trim();
                            const amount = row.cells[1].textContent.trim();
                            taxData.push({ category, amount });
                            labels.push(category);
                            values.push(parseFloat(amount.replace(/[₹,]/g, '')));
                        }
                    });
                }
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">💵 Tax Distribution</h3>
                            <canvas id="chartCanvas" style="max-height:350px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📋 Tax Breakdown</h3>
                            <table class="modal-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Category</th>
                                        <th style="text-align:right;">Tax Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${taxData.map((item, idx) => `
                                        <tr>
                                            <td><span class="rank-badge">${idx + 1}</span></td>
                                            <td style="font-weight:600;color:#111827;">${item.category}</td>
                                            <td style="text-align:right;font-weight:700;color:#490d59;">${item.amount}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('bar', labels, values, 'Tax Amount (₹)'), 50);
                
            } else if (key === 'school_wise' || key === 'grade_wise' || key === 'category_wise') {
                const table = detailsEl.querySelector('table tbody');
                const data = [];
                const labels = [], revenues = [];
                
                if (table) {
                    const rows = table.querySelectorAll('tr');
                    rows.forEach((row, idx) => {
                        if (row.cells.length >= 3) {
                            const item = {
                                name: row.cells[0].textContent.trim(),
                                orders: row.cells[1].textContent.trim(),
                                revenue: row.cells[key === 'school_wise' ? 2 : 3].textContent.trim(),
                                avg: key === 'school_wise' ? row.cells[3].textContent.trim() : row.cells[4]?.textContent.trim() || '-'
                            };
                            if (key !== 'school_wise') {
                                item.quantity = row.cells[2].textContent.trim();
                            }
                            data.push(item);
                            
                            if (idx < 10) {
                                labels.push(item.name);
                                revenues.push(parseFloat(item.revenue.replace(/[₹,]/g, '')));
                            }
                        }
                    });
                }
                
                const icon = key === 'school_wise' ? '🏫' : key === 'grade_wise' ? '🎓' : '📂';
                const label = key === 'school_wise' ? 'School' : key === 'grade_wise' ? 'Grade' : 'Category';
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1.5fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">${icon} Top 10 by Revenue</h3>
                            <canvas id="chartCanvas" style="max-height:400px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📊 Performance by ${label}</h3>
                            <table class="modal-data-table">
                                <thead>
                                    <tr>
                                        <th style="width:50px;">Rank</th>
                                        <th>${label}</th>
                                        <th style="text-align:center;">Orders</th>
                                        ${key !== 'school_wise' ? '<th style="text-align:center;">Qty</th>' : ''}
                                        <th style="text-align:right;">Revenue</th>
                                        ${key === 'school_wise' ? '<th style="text-align:right;">Avg Order</th>' : ''}
                                        ${key === 'category_wise' ? '<th style="text-align:right;">Avg Price</th>' : ''}
                                    </tr>
                                </thead>
                                <tbody>
                                    ${data.map((item, idx) => `
                                        <tr>
                                            <td><span class="rank-badge">${idx + 1}</span></td>
                                            <td style="font-weight:600;color:#111827;">${item.name}</td>
                                            <td style="text-align:center;">${item.orders}</td>
                                            ${key !== 'school_wise' ? `<td style="text-align:center;">${item.quantity}</td>` : ''}
                                            <td style="text-align:right;font-weight:700;color:#490d59;">${item.revenue}</td>
                                            ${key === 'school_wise' || key === 'category_wise' ? `<td style="text-align:right;">${item.avg}</td>` : ''}
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('horizontalBar', labels, revenues, 'Revenue (₹)'), 50);
                
            } else if (key === 'return_exchange') {
                const tables = detailsEl.querySelectorAll('table');
                const statusData = [];
                const reasonData = [];
                const labels = [], values = [];
                
                if (tables[0]) {
                    tables[0].querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells.length >= 2) {
                            const statusText = row.cells[0].querySelector('.badge')?.textContent || row.cells[0].textContent;
                            const count = row.cells[1].textContent.trim();
                            statusData.push({ status: statusText.trim(), count });
                            labels.push(statusText.trim());
                            values.push(parseInt(count.replace(/,/g, '')));
                        }
                    });
                }
                
                if (tables[1]) {
                    tables[1].querySelectorAll('tbody tr').forEach(row => {
                        if (row.cells.length >= 2) {
                            reasonData.push({
                                reason: row.cells[0].textContent.trim(),
                                count: row.cells[1].textContent.trim()
                            });
                        }
                    });
                }
                
                modalBody.innerHTML = `
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:32px;">
                        <div style="background:#fff;padding:24px;border-radius:16px;border:2px solid #e5e7eb;">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">↩️ Returns by Status</h3>
                            <canvas id="chartCanvas" style="max-height:300px;"></canvas>
                        </div>
                        <div class="table-scroll-container">
                            <h3 style="margin:0 0 20px;font-size:18px;font-weight:700;color:#111827;">📊 Status Breakdown</h3>
                            <table class="modal-data-table">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Status</th>
                                        <th style="text-align:right;">Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${statusData.map((item, idx) => `
                                        <tr>
                                            <td><span class="rank-badge">${idx + 1}</span></td>
                                            <td style="font-weight:600;color:#111827;">${item.status}</td>
                                            <td style="text-align:right;font-weight:700;color:#490d59;">${item.count}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>
                            ${reasonData.length > 0 ? `
                                <h3 style="margin:32px 0 20px;font-size:18px;font-weight:700;color:#111827;">📝 Top Return Reasons</h3>
                                <table class="modal-data-table">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Reason</th>
                                            <th style="text-align:right;">Count</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${reasonData.map((item, idx) => `
                                            <tr>
                                                <td><span class="rank-badge">${idx + 1}</span></td>
                                                <td style="font-weight:600;color:#111827;">${item.reason}</td>
                                                <td style="text-align:right;font-weight:700;color:#dc2626;">${item.count}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            ` : ''}
                        </div>
                    </div>
                `;
                
                setTimeout(() => createChart('pie', labels, values, 'Return Status'), 50);
                
            } else {
                modalBody.innerHTML = detailsEl.innerHTML;
            }
            
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function createChart(type, labels, data, label) {
            const ctx = document.getElementById('chartCanvas');
            if (!ctx) return;
            
            const colors = ['#490d59', '#7c3aed', '#a78bfa', '#c4b5fd', '#ddd6fe', '#e9d5ff', '#f3e8ff', '#faf5ff'];
            const isBar = type === 'bar' || type === 'horizontalBar';
            
            window.currentChart = new Chart(ctx.getContext('2d'), {
                type: type === 'horizontalBar' ? 'bar' : type,
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        backgroundColor: isBar ? '#490d59' : colors.slice(0, data.length),
                        borderWidth: 0,
                        hoverOffset: isBar ? 0 : 10,
                        borderRadius: isBar ? 8 : 0
                    }]
                },
                options: {
                    indexAxis: type === 'horizontalBar' ? 'y' : 'x',
                    responsive: true,
                    maintainAspectRatio: true,
                    plugins: {
                        legend: {
                            display: !isBar,
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                font: { size: 13, weight: '600' }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#1f2937',
                            padding: 12,
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            callbacks: {
                                label: function(context) {
                                    const value = context.parsed.y !== undefined ? context.parsed.y : context.parsed;
                                    if (type === 'doughnut' || type === 'pie') {
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                        return `${context.label}: ${value.toLocaleString()} (${percentage}%)`;
                                    }
                                    return `${label}: ${value.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: isBar ? {
                        x: { beginAtZero: true, ticks: { font: { size: 11 } } },
                        y: { ticks: { font: { size: 11 } } }
                    } : {}
                }
            });
        }
        
        
        function closeReportModal() {
            const modal = document.getElementById('report-modal');
            if (modal) {
                if (window.currentChart) {
                    window.currentChart.destroy();
                    window.currentChart = null;
                }
                modal.classList.remove('active');
                document.body.style.overflow = '';
            }
        }
    </script>
@endpush
