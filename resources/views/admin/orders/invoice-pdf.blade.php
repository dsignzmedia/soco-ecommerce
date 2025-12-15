<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 14px;
            color: #475467;
        }
        h2, h4 { color: #111827; margin: 0; }
        p { margin: 4px 0 0; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; padding: 10px; border-bottom: 1px solid #e5e7eb; color: #111827; font-size: 12px; text-transform: uppercase; }
        td { padding: 10px; border-bottom: 1px solid #f2f4f7; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .header-table td { border: none; padding: 0; }
        .info-table td { border: none; padding: 0; vertical-align: top; }
        .totals-table td { padding: 6px 0; border: none; }
        .grand-total { font-weight: bold; color: #111827; }
    </style>
</head>
<body>

    <!-- Header -->
    <table class="header-table" style="margin-bottom: 30px;">
        <tr>
            <td>
                <img src="{{ public_path('assets/img/logo.svg') }}" alt="The Skool Store" style="height:48px;margin-bottom:12px;">
                <h2>The Skool Store</h2>
                <p>Uniform procurement & fulfilment</p>
            </td>
            <td class="text-right">
                <p>Invoice #: {{ $order->order_number }}</p>
                <p>Date: {{ optional($order->order_date)->format('d M Y') }}</p>
            </td>
        </tr>
    </table>

    <!-- Billing Info -->
    <table class="info-table" style="margin-bottom: 30px;">
        <tr>
            <td style="width: 50%; padding-right: 15px;">
                <h4>Bill To</h4>
                <p>
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_address }}<br>
                    {{ $order->customer_phone }}<br>
                    {{ $order->customer_email ?? 'No email' }}
                </p>
            </td>
            <td style="width: 50%; padding-left: 15px;">
                <h4>School / Student</h4>
                <p>
                    {{ $order->school?->name ?? '—' }}<br>
                    {{ $order->student_name ?? '—' }} (Grade {{ $order->grade ?? '—' }})
                </p>
            </td>
        </tr>
    </table>

    <!-- Line Items -->
    <table style="margin-bottom: 30px;">
        <thead>
            <tr>
                <th>Item</th>
                <th>Category</th>
                <th>Size</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $order->item_name }}</td>
                <td>{{ $order->category }}</td>
                <td>{{ $order->size }}</td>
                <td class="text-center">{{ $order->quantity }}</td>
                <td class="text-right">&#8377;{{ number_format($order->total_amount - $order->tax_amount - $order->shipping_cost, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Totals -->
    <table>
        <tr>
            <td style="width: 60%; border: none;"></td>
            <td style="width: 40%; border: none;">
                <table class="totals-table">
                    <tr>
                        <td>Tax</td>
                        <td class="text-right">&#8377;{{ number_format($order->tax_amount, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Shipping</td>
                        <td class="text-right">&#8377;{{ number_format($order->shipping_cost, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="grand-total">Total</td>
                        <td class="text-right grand-total">&#8377;{{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer -->
    <div style="margin-top: 40px; border-top: 1px solid #e5e7eb; padding-top: 10px; font-size: 12px; color: #98a2b3;">
        Payment status: {{ ucfirst($order->payment_status) }} • Order status: {{ ucfirst($order->order_status) }}
    </div>

</body>
</html>
