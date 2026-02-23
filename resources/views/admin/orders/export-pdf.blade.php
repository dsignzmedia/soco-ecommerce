<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Orders Export</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Inter', 'Roboto', Arial, sans-serif;
            font-size: 9px;
            color: #1f2937;
            background: #ffffff;
            padding: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e5e7eb;
        }
        h1 {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .subtitle {
            font-size: 9px;
            color: #6b7280;
            font-weight: 400;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        thead {
            background-color: #f9fafb;
            border-top: 1px solid #d1d5db;
            border-bottom: 1px solid #d1d5db;
        }
        th {
            padding: 8px 5px;
            text-align: left;
            font-size: 8px;
            font-weight: 700;
            color: #374151;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        td {
            padding: 7px 5px;
            border-bottom: 1px solid #f3f4f6;
            font-size: 8px;
            color: #1f2937;
            vertical-align: top;
        }
        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }
        tbody tr:hover {
            background-color: #f9fafb;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 7px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .badge-paid {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-pending {
            background-color: #fed7aa;
            color: #92400e;
        }
        .badge-delivered {
            background-color: #d1fae5;
            color: #065f46;
        }
        .badge-processing {
            background-color: #dbeafe;
            color: #1e40af;
        }
        .badge-shipped {
            background-color: #e9d5ff;
            color: #6b21a8;
        }
        .badge-order-placed {
            background-color: #e5e7eb;
            color: #374151;
        }
        .footer {
            margin-top: 15px;
            padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 7px;
            color: #9ca3af;
        }
        .footer-stats {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 5px;
        }
        .footer-stat {
            font-weight: 600;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Orders Export Report</h1>
        <div class="subtitle">Generated on {{ date('F d, Y \a\t H:i:s') }}</div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 9%;">Order #</th>
                <th style="width: 8%;">Date</th>
                <th style="width: 13%;">Customer</th>
                <th style="width: 16%;">Item</th>
                <th style="width: 6%;">Size</th>
                <th style="width: 4%;" class="text-center">Qty</th>
                <th style="width: 9%;" class="text-right">Amount</th>
                <th style="width: 9%;" class="text-center">Payment</th>
                <th style="width: 10%;" class="text-center">Status</th>
                <th style="width: 11%;">School</th>
                <th style="width: 5%;">Grade</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $order)
                <tr>
                    <td style="font-weight: 600;">{{ $order->order_number }}</td>
                    <td>{{ $order->order_date ? $order->order_date->format('Y-m-d') : ($order->created_at ? $order->created_at->format('Y-m-d') : '—') }}</td>
                    <td>
                        <div style="font-weight: 600;">{{ Str::limit($order->customer_name, 18) }}</div>
                        <div style="color: #6b7280; font-size: 7px;">{{ $order->customer_phone }}</div>
                    </td>
                    <td>{{ Str::limit($order->item_name, 25) }}</td>
                    <td>{{ $order->size }}</td>
                    <td class="text-center">{{ $order->quantity }}</td>
                    <td class="text-right" style="font-weight: 600;">Rs {{ number_format($order->total_amount, 2) }}</td>
                    <td class="text-center">
                        @php
                            $paymentClass = match($order->payment_status) {
                                'paid' => 'badge-paid',
                                'pending' => 'badge-pending',
                                default => 'badge-pending'
                            };
                        @endphp
                        <span class="badge {{ $paymentClass }}">{{ ucfirst($order->payment_status) }}</span>
                    </td>
                    <td class="text-center">
                        @php
                            $statusClass = match($order->order_status) {
                                'delivered' => 'badge-delivered',
                                'processing' => 'badge-processing',
                                'shipped' => 'badge-shipped',
                                'order_placed' => 'badge-order-placed',
                                default => 'badge-order-placed'
                            };
                        @endphp
                        <span class="badge {{ $statusClass }}">{{ ucfirst(str_replace('_', ' ', $order->order_status)) }}</span>
                    </td>
                    <td>{{ $order->school ? Str::limit($order->school->name, 15) : '—' }}</td>
                    <td>{{ $order->grade ?? '—' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center" style="padding: 20px; color: #9ca3af;">No orders found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} SOCO Uniforms. All rights reserved.</p>
    </div>
</body>
</html>
