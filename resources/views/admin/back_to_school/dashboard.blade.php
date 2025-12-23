@extends('admin.layouts.back_to_school')

@section('title', 'Back to School Dashboard | The Skool Store')
@section('page_heading', 'Back to School Dashboard')
@section('page_subheading', 'Overview of performance and key metrics.')

@push('styles')
    <style>
        /* Dashboard Grid Layout */
        .dashboard-container {
            display: flex;
            flex-direction: column;
            gap: 28px;
            font-family: 'Inter', sans-serif;
        }

        /* KPI Cards Section */
        .kpi-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 24px;
        }

        .kpi-card {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 24px;
            display: flex;
            flex-direction: column;
            position: relative;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.025);
        }

        .kpi-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            margin-bottom: 20px;
            color: #ffffff;
        }

        /* Solid Color variants for KPI icons to match image */
        .kpi-green .kpi-icon-wrapper { background: #10b981; }
        .kpi-purple .kpi-icon-wrapper { background: #8b5cf6; }
        .kpi-yellow .kpi-icon-wrapper { background: #f59e0b; }
        .kpi-blue .kpi-icon-wrapper { background: #0ea5e9; }
        
        .kpi-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #64748b;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .kpi-value {
            font-size: 28px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 12px;
            line-height: 1;
        }

        .kpi-trend {
            font-size: 13px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .trend-up { color: #10b981; }
        .trend-down { color: #ef4444; }
        .trend-neutral { color: #64748b; }

        /* Section Layout for Actions & Tips */
        .dashboard-mid-section {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }
        @media (max-width: 1024px) {
            .dashboard-mid-section { grid-template-columns: 1fr; }
        }

        /* Quick Actions Grid */
        .quick-actions-card {
            background: #ffffff;
            border-radius: 16px;
            padding: 24px;
            border: 1px solid #f1f5f9;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .section-title {
            font-size: 16px;
            font-weight: 700;
            color: #334155;
            margin-bottom: 20px;
        }

        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 16px;
        }

        .action-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            transition: all 0.2s;
            text-decoration: none;
            color: inherit;
        }

        .action-item:hover {
            border-color: #8b5cf6;
            background: #f8fafc;
            transform: translateY(-1px);
        }

        .action-icon {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            flex-shrink: 0;
        }

        .action-info { display: flex; flex-direction: column; }
        .action-title { font-size: 14px; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .action-desc { font-size: 12px; color: #64748b; font-weight: 500; }
        
        .action-arrow { margin-left: auto; color: #cbd5e1; font-size: 14px; }
        .action-item:hover .action-arrow { color: #8b5cf6; }

        /* Icon colors for actions */
        .bg-purple-light { background: #f3e8f5; color: #490d59; }
        .bg-green-light { background: #dcfce7; color: #16a34a; }
        .bg-blue-light { background: #e0f2fe; color: #0284c7; }
        .bg-orange-light { background: #ffedd5; color: #ea580c; }

        /* Admin Tips Card */
        .admin-tips-card {
            background: #490d59;
            border-radius: 16px;
            padding: 32px;
            color: white;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: center;
            overflow: hidden;
        }
        
        .admin-tips-card::before {
            content: '\f0ae'; /* FontAwesome tasks icon */
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 20px;
            right: 24px;
            font-size: 60px;
            color: rgba(255,255,255,0.1);
        }

        .tips-title { font-size: 16px; font-weight: 700; margin-bottom: 12px; }
        .tips-text { font-size: 14px; line-height: 1.6; color: rgba(255,255,255,0.9); margin-bottom: 24px; }
        .tips-btn {
            background: #ffffff;
            color: #490d59;
            padding: 10px 20px;
            border-radius: 99px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            display: inline-flex;
            width: fit-content;
            transition: transform 0.2s;
        }
        .tips-btn:hover { transform: scale(1.05); }

        /* Charts & Filters Common */
        .chart-container {
            background: #ffffff;
            border: 1px solid #f1f5f9;
            border-radius: 16px;
            padding: 24px;
            height: 100%;
        }

        /* Filter Section Styling */
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 24px;
        }
        .filters-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }
        .filter-form-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        .filter-input-rounded {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px;
            font-family: inherit;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }
        .btn-purple-solid {
            background-color: #4c1d95;
            color: #ffffff;
            border: none;
            border-radius: 12px;
            padding: 0 24px;
            height: 46px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%;
        }
        .btn-purple-solid:hover {
            background-color: #3b0a48;
        }
        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            padding: 0 32px;
            height: 46px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s;
        }
        .btn-reset:hover {
            border-color: #d0d5dd;
            color: #0f172a;
            background: #f8fafc;
            text-decoration: none;
        }

        /* Recent Activity / Alerts */
        .activity-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        .activity-item {
            display: flex;
            gap: 12px;
            align-items: flex-start;
            padding-bottom: 16px;
            border-bottom: 1px solid var(--border);
        }

        .activity-item:last-child {
            border-bottom: none;
            padding-bottom: 0;
        }

        .activity-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: var(--bg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-muted);
            font-size: 14px;
            flex-shrink: 0;
        }

        .activity-content h5 {
            margin: 0 0 4px;
            font-size: 14px;
            color: var(--text-main);
            font-weight: 500;
        }

        .activity-content p {
            margin: 0;
            font-size: 13px;
            color: var(--text-muted);
            line-height: 1.4;
        }

        .charts-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }
        @media (max-width: 1024px) {
            .charts-section { grid-template-columns: 1fr; }
        }
    </style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">Filters</h3>
        <form method="GET" class="filter-form-grid">
            <div class="filter-row">
                <select name="category" >
                    <option value="">Category (All)</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>

                <div class="date-input-wrapper">
                    <input type="date" 
                        onclick="this.showPicker()" 
                        name="start_date" 
                        class="filter-input-rounded" 
                        placeholder="Start Date" 
                        value="{{ $filters['start_date'] ?? '' }}">
                </div>

                <div class="date-input-wrapper">
                    <input type="date" 
                        onclick="this.showPicker()" 
                        name="end_date" 
                        class="filter-input-rounded" 
                        placeholder="End Date" 
                        value="{{ $filters['end_date'] ?? '' }}">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-purple-solid" style="flex: 1;">Apply</button>
                    <button type="button" onclick="window.location.href='{{ route('admin.back_to_school.dashboard') }}'" class="btn-reset" style="flex: 1;">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Grid -->
    <div class="kpi-grid">
        @php
            function getKpiStyleV2($label) {
                if (stripos($label, 'sales') !== false || stripos($label, 'revenue') !== false) return ['icon' => 'fa-indian-rupee-sign', 'color' => 'kpi-green'];
                if (stripos($label, 'order') !== false) return ['icon' => 'fa-shopping-bag', 'color' => 'kpi-purple'];
                if (stripos($label, 'product') !== false || stripos($label, 'sku') !== false) return ['icon' => 'fa-box', 'color' => 'kpi-blue']; 
                if (stripos($label, 'stock') !== false || stripos($label, 'inventory') !== false) return ['icon' => 'fa-box', 'color' => 'kpi-yellow']; 
                return ['icon' => 'fa-chart-pie', 'color' => 'kpi-blue'];
            }
        @endphp

        @foreach($kpis as $kpi)
            @php $style = getKpiStyleV2($kpi['label']); @endphp
            <div class="kpi-card {{ $style['color'] }}">
                <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                    <div class="kpi-icon-wrapper">
                        <i class="fa-solid {{ $style['icon'] }}"></i>
                    </div>
                    <div style="color:#94a3b8; cursor:pointer;"><i class="fa-solid fa-ellipsis-vertical"></i></div>
                </div>
                
                <span class="kpi-label">{{ $kpi['label'] }}</span>
                <div class="kpi-value">{{ $kpi['prefix'] ?? '' }}{{ $kpi['value'] }}</div>
                
                <div class="kpi-trend trend-up">
                    <i class="fa-solid fa-arrow-trend-up"></i>
                    <span>12% vs last period</span>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Mid Section: Quick Actions & Administrator Tips -->
    <div class="dashboard-mid-section">
        <!-- Quick Actions -->
        <div class="quick-actions-card">
            <div class="section-title">Quick Actions</div>
            <div class="actions-grid">
                <a href="{{ route('admin.back_to_school.orders.index') }}" class="action-item">
                    <div class="action-icon bg-purple-light">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Manage Orders</span>
                        <span class="action-desc">View & process orders</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('admin.back_to_school.reports.index') }}" class="action-item">
                    <div class="action-icon bg-green-light">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Analytics Report</span>
                        <span class="action-desc">Sales & performance</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('admin.back_to_school.products.index') }}" class="action-item">
                    <div class="action-icon bg-blue-light">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Product Catalog</span>
                        <span class="action-desc">Manage products</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
                
                <a href="{{ route('admin.back_to_school.inventory.index') }}" class="action-item">
                    <div class="action-icon bg-orange-light">
                        <i class="fa-solid fa-warehouse"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Inventory</span>
                        <span class="action-desc">Stock management</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>

        <!-- Admin Workflow Overview -->
        <div class="admin-tips-card">
            <div class="tips-title">Back to School Workflow</div>
            <div class="tips-text">
                <strong>1. Product Management:</strong> Add and manage Back to School products.<br>
                <strong>2. Inventory Control:</strong> Monitor stock levels and update inventory.<br>
                <strong>3. Order Processing:</strong> Track and fulfill orders efficiently.<br>
                <strong>4. Analytics:</strong> Review sales performance and trends.
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <!-- Main Sales Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="section-title">Sales Overview</div>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Secondary: Orders by Category -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="section-title">Orders by Category</div>
            </div>
             <div style="height: 300px; position: relative; display: flex; justify-content: center;">
                <canvas id="ordersChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Alerts / Recent Activity -->
    <div class="chart-container">
        <div class="chart-header">
            <div class="section-title">System Alerts & Escalations</div>
        </div>
        <ul class="activity-list">
             @forelse($alerts as $alert)
                <li class="activity-item">
                    <div class="activity-icon">
                        <i class="fa-solid fa-bell"></i>
                    </div>
                    <div class="activity-content">
                        <h5>{{ $alert['type'] }}</h5>
                        <p>
                             @foreach($alert['items'] as $item)
                                {{ $item }}@if(!$loop->last), @endif
                            @endforeach
                        </p>
                    </div>
                </li>
            @empty
                <li class="activity-item" style="border:none;">
                    <p style="color:var(--text-muted); font-style:italic;">No active alerts at this time.</p>
                </li>
            @endforelse
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- Sales Chart ---
        const salesCtx = document.getElementById('salesChart').getContext('2d');
        const salesData = @json($charts['salesTrend']);
        
        new Chart(salesCtx, {
            type: 'line',
            data: {
                labels: salesData.labels,
                datasets: [{
                    label: 'Total Sales (₹)',
                    data: salesData.series,
                    borderColor: '#490d59',
                    backgroundColor: 'rgba(73, 13, 89, 0.1)',
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 3,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleColor: '#f8fafc',
                        bodyColor: '#f8fafc',
                        borderColor: '#334155',
                        borderWidth: 1
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148, 163, 184, 0.1)' },
                        ticks: { color: '#64748b' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: '#64748b' }
                    }
                }
            }
        });

        // --- Orders Chart (Donut) ---
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const categoryData = @json($charts['ordersByCategory']['data'] ?? []);
        
        const labels = categoryData.map(item => item.label);
        const dataPoints = categoryData.map(item => item.value);
        const backgroundColors = ['#490d59', '#d946ef', '#f97316', '#22c55e', '#3b82f6'];

        new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: dataPoints,
                    backgroundColor: backgroundColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { 
                            usePointStyle: true,
                            padding: 20,
                            font: { size: 12 }
                        }
                    }
                },
                cutout: '70%',
            }
        });
    });
</script>
@endpush
