@extends('admin.layouts.base')

@section('title', 'Master Admin Portal | The Skool Store')
@section('page_heading', 'Master Admin Dashboard')
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
        .kpi-red .kpi-icon-wrapper { background: #ef4444; }
        
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
            overflow: hidden; /* Prevent content from overflowing container */
            box-sizing: border-box;
        }

        /* Filter Section Styling */
        .filters-container {
            display: flex;
            flex-direction: column;
            gap: 16px;
            margin-bottom: 8px;
            padding: 0;
            background: transparent;
            border: none;
            box-shadow: none;
        }

        .filters-title {
            font-size: 18px;
            font-weight: 700;
            color: #0f172a;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 12px;
            
        }

        .filter-select {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            padding: 10px 16px;
            border-radius: 12px;
            font-size: 14px;
            outline: none;
            min-width: 160px;
            cursor: pointer;
            height: 44px;
        }

        .btn-apply {
            background: #490d59;
            color: white;
            border: none;
            padding: 0 32px;
            height: 44px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .btn-apply:hover { background: #3b0a48; }

        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            padding: 0 32px;
            height: 44px;
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
    </style>
@endpush

@section('content')
<div class="dashboard-container">

    <!-- Filters -->
    <!-- Filters -->
    <div class="filters-card">
        <h3 class="filters-title">Filters</h3>
        <form method="GET" class="filter-form-grid">
            <!-- Row 1 -->
            <div class="filter-row">
                <select name="school_id">
                    <option value="">School (All)</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>

                <select name="category">
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
                    <button type="button" onclick="window.location.href='{{ route('master.admin.dashboard') }}'" class="btn-reset" style="flex: 1;">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle shadow per image */
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
            border-radius: 12px; /* Standard rounded box */
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px; /* Match Select height */
            font-family: inherit;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1); /* Consistent focus ring */
        }
        .btn-purple-solid {
            background-color: #4c1d95; /* Deep purple */
            color: #ffffff;
            border: none;
            border-radius: 12px; /* Standard rounded box */
            padding: 0 24px;
            height: 46px; /* Match Select height */
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%; /* Fill grid cell */
        }
        .btn-purple-solid:hover {
            background-color: #3b0a48;
        }
    </style>

    <!-- KPI Grid -->
    <div class="kpi-grid">
        @php
            // Helper for new styles. 
            // We want specific colors: Green (Sales), Purple (Orders), Yellow (Pending), Blue (Delivered/Stock)
            function getKpiStyleV2($label, $kpi = []) {
                // Use color from KPI array if provided
                if (!empty($kpi['color'])) {
                    $icon = stripos($label, 'refund') !== false ? 'fa-arrow-trend-down' : (stripos($label, 'revenue') !== false ? 'fa-indian-rupee-sign' : 'fa-chart-pie');
                    return ['icon' => $icon, 'color' => $kpi['color']];
                }
                if (stripos($label, 'refund') !== false) return ['icon' => 'fa-arrow-trend-down', 'color' => 'kpi-red'];
                if (stripos($label, 'sales') !== false || stripos($label, 'revenue') !== false) return ['icon' => 'fa-indian-rupee-sign', 'color' => 'kpi-green'];
                if (stripos($label, 'order') !== false) return ['icon' => 'fa-shopping-bag', 'color' => 'kpi-purple'];
                if (stripos($label, 'student') !== false) return ['icon' => 'fa-user-graduate', 'color' => 'kpi-blue']; 
                if (stripos($label, 'stock') !== false || stripos($label, 'inventory') !== false) return ['icon' => 'fa-box', 'color' => 'kpi-yellow']; 
                return ['icon' => 'fa-chart-pie', 'color' => 'kpi-blue'];
            }
        @endphp

        @foreach($kpis as $kpi)
            @php $style = getKpiStyleV2($kpi['label'], $kpi); @endphp
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
                <a href="{{ route('master.admin.orders.index') }}" class="action-item">
                    <div class="action-icon bg-purple-light">
                        <i class="fa-solid fa-box-open"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Manage Orders</span>
                        <span class="action-desc">View & process orders</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('master.admin.reports.index') }}" class="action-item">
                    <div class="action-icon bg-green-light">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Analytics Report</span>
                        <span class="action-desc">Sales & performance</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>

                <a href="{{ route('master.admin.schools.index') }}" class="action-item">
                    <div class="action-icon bg-blue-light">
                        <i class="fa-solid fa-user-graduate"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">School Directory</span>
                        <span class="action-desc">Manage school data</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
                
                <a href="{{ route('master.admin.catalog.index') }}" class="action-item">
                    <div class="action-icon bg-orange-light">
                        <i class="fa-solid fa-shirt"></i>
                    </div>
                    <div class="action-info">
                        <span class="action-title">Product Catalog</span>
                        <span class="action-desc">Inventory & Prices</span>
                    </div>
                    <i class="fa-solid fa-chevron-right action-arrow"></i>
                </a>
            </div>
        </div>

        <!-- Admin Workflow Overview -->
        <div class="admin-tips-card">
            <div class="tips-title">Master Admin Workflow</div>
            <div class="tips-text">
                <strong>1. Manage Schools & Products:</strong> Onboard schools and map catalog items.<br>
                <strong>2. Inventory & Pricing:</strong> Update stock levels and set school-specific pricing.<br>
                <strong>3. Order Fulfillment:</strong> Monitor and process incoming orders and returns.<br>
                <strong>4. Analytics:</strong> Track sales trends and performance reports.
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="charts-section">
        <!-- Main Sales Chart -->
        <div class="chart-container" style="margin-bottom: 16px;">
            <div class="chart-header">
                <div class="section-title">Sales Overview</div>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <!-- Secondary: Orders by Status or School -->
        <div class="chart-container">
            <div class="chart-header">
                <div class="section-title">Orders by Status</div>
            </div>
             <div style="height: 350px; position: relative; display: flex; align-items: center; gap: 32px; padding: 20px 0;">
                <div style="flex: 0 0 auto; width: 300px; height: 300px; display: flex; justify-content: center; align-items: center;">
                    <canvas id="ordersChart"></canvas>
                </div>
                <div id="ordersChartLegend" style="flex: 1; display: flex; flex-direction: column; gap: 10px; padding-left: 20px; overflow-y: auto; overflow-x: hidden; max-height: 350px; min-width: 0;"></div>
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

        // --- Orders Chart (Doughnut) ---
        // Using "Orders by Category" data for variety if available, else generic mock or School data
        const ordersCtx = document.getElementById('ordersChart').getContext('2d');
        const categoryData = @json($charts['ordersByCategory']['data'] ?? []);
        
        const labels = categoryData.map(item => item.label);
        const dataPoints = categoryData.map(item => item.value);
        // Extended color palette for doughnut chart
        const backgroundColors = [
            '#490d59', '#d946ef', '#f97316', '#22c55e', '#3b82f6',
            '#8b5cf6', '#ec4899', '#14b8a6', '#f59e0b', '#ef4444',
            '#6366f1', '#10b981', '#f43f5e', '#06b6d4', '#84cc16',
            '#a855f7', '#06b6d4', '#fbbf24', '#34d399'
        ];

        const ordersChart = new Chart(ordersCtx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Orders by Category',
                    data: dataPoints,
                    backgroundColor: backgroundColors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff',
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false // Hide default legend, we'll use custom one
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                                return `${label}: ${value} (${percentage}%)`;
                            }
                        }
                    }
                },
                cutout: '65%',
            }
        });
        
        // Create custom legend on the right side
        const legendContainer = document.getElementById('ordersChartLegend');
        if (legendContainer && ordersChart) {
            const total = ordersChart.data.datasets[0].data.reduce((a, b) => a + b, 0);
            const legendItems = ordersChart.data.labels.map((label, index) => {
                const value = ordersChart.data.datasets[0].data[index];
                const color = ordersChart.data.datasets[0].backgroundColor[index];
                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : '0.0';
                
                return `
                    <div style="display: flex; align-items: center; gap: 12px; padding: 6px 0; min-width: 0; width: 100%; box-sizing: border-box;">
                        <div style="width: 14px; height: 14px; border-radius: 3px; background-color: ${color}; flex-shrink: 0; border: 1px solid rgba(0,0,0,0.1);"></div>
                        <div style="flex: 1; display: flex; justify-content: space-between; align-items: center; min-width: 0; gap: 12px; overflow: hidden;">
                            <span style="font-size: 13px; color: #475467; font-weight: 500; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 100%;">${label}</span>
                            <div style="display: flex; align-items: center; gap: 8px; flex-shrink: 0;">
                                <span style="font-size: 13px; color: #111827; font-weight: 600; white-space: nowrap;">${value}</span>
                                <span style="font-size: 12px; color: #64748b; white-space: nowrap;">(${percentage}%)</span>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
            legendContainer.innerHTML = legendItems;
        }
    });
</script>
@endpush
