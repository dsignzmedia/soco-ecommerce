<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>School Report</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 14px; color: #333; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #490D59; padding-bottom: 10px; }
        .school-name { font-size: 24px; font-weight: bold; color: #490D59; }
        .report-title { font-size: 18px; margin-top: 5px; }
        .meta-info { margin-bottom: 20px; font-size: 12px; color: #666; }
        .summary-grid { width: 100%; margin-bottom: 30px; }
        .summary-card { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 5px; }
        .summary-label { font-size: 12px; color: #666; text-transform: uppercase; font-weight: bold; }
        .summary-value { font-size: 20px; font-weight: bold; color: #333; margin-top: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #490D59; color: white; }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-name">{{ Auth::user()->school->name ?? 'School Report' }}</div>
        <div class="report-title">Sales & Orders Report</div>
        <div class="meta-info">
            Generated on: {{ date('d M Y, H:i') }} <br> 
            @if(!empty($filters['start_date'])) Start Date: {{ $filters['start_date'] }} @endif
            @if(!empty($filters['end_date'])) End Date: {{ $filters['end_date'] }} @endif
            @if(!empty($filters['grade'])) Grade: {{ $filters['grade'] }} @endif
            @if(!empty($filters['product'])) Product: {{ $filters['product'] }} @endif
        </div>
    </div>

    <h3>Summary</h3>
    <table style="margin-bottom: 30px;">
        <tr>
            <td width="25%">
                <div class="summary-label">Total Sales</div>
                <div class="summary-value" style="color: #490D59;">₹{{ number_format($summary['total_sales'] ?? 0) }}</div>
            </td>
            <td width="25%">
                <div class="summary-label">Total Orders</div>
                <div class="summary-value" style="color: #28a745;">{{ number_format($summary['total_orders'] ?? 0) }}</div>
            </td>
            <td width="25%">
                <div class="summary-label">Avg. Order Value</div>
                <div class="summary-value" style="color: #17a2b8;">₹{{ number_format($summary['average_order_value'] ?? 0) }}</div>
            </td>
            <td width="25%">
                <div class="summary-label">Top Product</div>
                <div class="summary-value" style="color: #ffc107;">{{ $summary['top_product'] ?? 'N/A' }}</div>
            </td>
        </tr>
    </table>

    <h3>Sales Data</h3>
    <table>
        <thead>
            <tr>
                <th>Period / Item</th>
                <th>Sales Amount</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($chart_data['labels']) && count($chart_data['labels']) > 0)
                @foreach($chart_data['labels'] as $index => $label)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>₹{{ number_format($chart_data['sales'][$index] ?? 0) }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="2" style="text-align: center;">No data available for the selected period.</td>
                </tr>
            @endif
        </tbody>
    </table>

    <div style="margin-top: 50px; text-align: center; color: #999; font-size: 12px;">
        &copy; {{ date('Y') }} Soco Uniforms. All rights reserved.
    </div>
</body>
</html>
