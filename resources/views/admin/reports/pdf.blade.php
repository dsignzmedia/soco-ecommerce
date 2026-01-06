<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Admin Reports</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #111827; }
        .header { margin-bottom: 20px; border-bottom: 2px solid #4f46e5; padding-bottom: 10px; }
        .header h1 { margin: 0; font-size: 24px; color: #4f46e5; }
        .header p { margin: 4px 0 0; color: #6b7280; font-size: 12px; }
        
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { padding: 8px 10px; text-align: left; border-bottom: 1px solid #e5e7eb; }
        th { background-color: #f9fafb; font-weight: 600; font-size: 11px; text-transform: uppercase; color: #374151; }
        tr:nth-child(even) { background-color: #f9fafb; }
        
        .status-badge { 
            padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: 600; text-transform: uppercase; display: inline-block;
        }
        .status-paid { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-failed { background: #fee2e2; color: #991b1b; }
        
        .summary { margin-top: 30px; page-break-inside: avoid; }
        .summary-box { background: #f3f4f6; padding: 15px; border-radius: 8px; width: 300px; }
        .summary-item { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .summary-item.total { border-top: 1px solid #d1d5db; margin-top: 5px; padding-top: 5px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Detailed Order Report</h1>
        <p>Generated on {{ now()->format('d M Y, h:i A') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Order ID</th>
                <th>School</th>
                <th>Grade</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ optional($order->order_date)->format('d M Y') }}</td>
                <td>{{ $order->order_number }}</td>
                <td>{{ optional($order->school)->name ?? 'N/A' }}</td>
                <td>{{ $order->grade ?? '-' }}</td>
                <td>
                    {{ $order->item_name }}
                    <div style="font-size: 10px; color: #6b7280;">{{ $order->category }}</div>
                </td>
                <td>{{ $order->quantity }}</td>
                <td>Rs. {{ number_format($order->total_amount, 2) }}</td>
                <td>{{ ucfirst($order->order_status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <div class="summary-box">
            <h3 style="margin: 0 0 10px; font-size: 14px;">Summary</h3>
            <div class="summary-item">
                <span>Total Orders:</span>
                <span>{{ $orders->count() }}</span>
            </div>
            <div class="summary-item">
                <span>Total Items:</span>
                <span>{{ $orders->sum('quantity') }}</span>
            </div>
            <div class="summary-item total">
                <span>Total Revenue:</span>
                <span>Rs. {{ number_format($orders->sum('total_amount'), 2) }}</span>
            </div>
        </div>
    </div>
</body>
</html>
