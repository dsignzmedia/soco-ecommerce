@extends('admin.layouts.back_to_school')

@section('title', 'Reports | Back To School Admin')
@section('page_heading', 'Reports & Analytics')
@section('page_subheading', 'Insights into your back-to-school sales performance.')

@push('head')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@push('styles')
    <style>
        .stat-card-modern {
            background: linear-gradient(135deg, #490d59, #6d28d9);
            border-radius: 16px;
            padding: 24px;
            color: #fff;
            box-shadow: 0 4px 6px rgba(73, 13, 89, 0.2);
        }
        
        .stat-card-modern.revenue {
            background: linear-gradient(135deg, #059669, #10b981);
        }
        
        .stat-card-modern h4 {
            margin: 0 0 8px;
            font-size: 13px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .stat-card-modern .value {
            font-size: 36px;
            font-weight: 800;
            margin: 0;
        }
        
        .chart-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            border: 2px solid #e5e7eb;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .chart-card h3 {
            margin: 0 0 20px;
            font-size: 18px;
            font-weight: 700;
            color: #111827;
        }
    </style>
@endpush

@section('content')
<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:32px;">
    <!-- Total Orders -->
    <div class="stat-card-modern">
        <h4>Total Orders</h4>
        <p class="value">{{ number_format($totalOrders) }}</p>
    </div>

    <!-- Revenue -->
    <div class="stat-card-modern revenue">
        <h4>Total Revenue</h4>
        <p class="value">₹{{ number_format($totalRevenue, 2) }}</p>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
    <!-- Product Sales Chart -->
    <div class="chart-card">
        <h3>📊 Top Products by Revenue</h3>
        <canvas id="productsChart" style="max-height:300px;"></canvas>
    </div>
    
    <!-- Product Table -->
    <div class="chart-card">
        <h3>🏆 Best Sellers</h3>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:2px solid #e5e7eb;">
                    <th style="text-align:left;padding:12px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">#</th>
                    <th style="text-align:left;padding:12px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Product</th>
                    <th style="text-align:right;padding:12px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Units</th>
                    <th style="text-align:right;padding:12px 8px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Revenue</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productSales as $index => $sale)
                <tr style="border-bottom:1px solid #f1f5f9;">
                    <td style="padding:12px 8px;">
                        <span style="display:inline-flex;align-items:center;justify-content:center;width:28px;height:28px;border-radius:50%;background:linear-gradient(135deg,#490d59,#6d28d9);color:#fff;font-weight:700;font-size:12px;">{{ $index + 1 }}</span>
                    </td>
                    <td style="padding:12px 8px;font-size:13px;font-weight:600;">{{ Str::limit($sale->item_name, 25) }}</td>
                    <td style="padding:12px 8px;font-size:13px;text-align:right;">{{ $sale->total_qty }}</td>
                    <td style="padding:12px 8px;font-size:14px;text-align:right;font-weight:700;color:#490d59;">₹{{ number_format($sale->total_revenue) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Orders -->
<div class="chart-card">
    <h3>📋 Recent Orders</h3>
    <table style="width:100%;border-collapse:collapse;">
        <thead>
            <tr style="border-bottom:2px solid #e5e7eb;">
                <th style="text-align:left;padding:12px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Order ID</th>
                <th style="text-align:left;padding:12px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Date</th>
                <th style="text-align:right;padding:12px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Amount</th>
                <th style="text-align:center;padding:12px;font-size:11px;color:#6b7280;text-transform:uppercase;font-weight:700;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
            <tr style="border-bottom:1px solid #f1f5f9;">
                <td style="padding:12px;font-size:13px;font-weight:600;color:#490d59;">{{ $order->order_number }}</td>
                <td style="padding:12px;font-size:13px;">{{ $order->created_at->format('d M Y') }}</td>
                <td style="padding:12px;font-size:14px;text-align:right;font-weight:700;">₹{{ number_format($order->total_amount, 2) }}</td>
                <td style="padding:12px;text-align:center;">
                    <span style="padding:4px 12px;border-radius:12px;font-size:11px;font-weight:600;background:#f0fdf4;color:#16a34a;">{{ ucfirst($order->order_status) }}</span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align:center;padding:40px;color:#9ca3af;">No orders found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
    <script>
        // Product Sales Horizontal Bar Chart
        const productsCtx = document.getElementById('productsChart');
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($productSales->pluck('item_name')->map(fn($n) => Str::limit($n, 20))) !!},
                datasets: [{
                    label: 'Revenue (₹)',
                    data: {!! json_encode($productSales->pluck('total_revenue')) !!},
                    backgroundColor: '#490d59',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1f2937',
                        padding: 12,
                        titleFont: { size: 14, weight: 'bold' },
                        bodyFont: { size: 13 },
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: ₹' + context.parsed.x.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        ticks: { font: { size: 11 } }
                    }
                }
            }
        });
    </script>
@endpush
