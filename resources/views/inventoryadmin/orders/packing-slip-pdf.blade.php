<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Packing Slip - {{ $order->order_number }}</title>
    <style>
        @page { margin: 20px; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 10px; line-height: 1.4; color: #333; }
        .header { display: flex; justify-content: space-between; margin-bottom: 20px; border-bottom: 2px solid #444; padding-bottom: 10px; }
        .logo { font-size: 20px; font-weight: bold; color: #490d59; }
        .title { font-size: 18px; font-weight: bold; text-align: right; }
        .info-grid { display: table; width: 100%; margin-bottom: 20px; }
        .info-col { display: table-cell; width: 50%; vertical-align: top; }
        .section-title { font-weight: bold; text-transform: uppercase; font-size: 9px; color: #666; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th { background: #f3f4f6; text-align: left; padding: 8px; border: 1px solid #e5e7eb; font-weight: bold; }
        td { padding: 8px; border: 1px solid #e5e7eb; }
        .footer { margin-top: 50px; border-top: 1px solid #e5e7eb; padding-top: 10px; font-size: 9px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <table style="width: 100%; border: none;">
            <tr style="border: none;">
                <td style="border: none;"><div class="logo">THE SKOOL STORE</div></td>
                <td style="border: none; text-align: right;"><div class="title">PACKING SLIP</div></td>
            </tr>
        </table>
    </div>

    <div class="info-grid">
        <div class="info-col">
            <div class="section-title">Ship To:</div>
            <div style="font-size: 12px; font-weight: bold;">{{ $order->customer_name }}</div>
            <div>{{ $order->customer_address }}</div>
            <div>Phone: {{ $order->customer_phone }}</div>
            <div>Email: {{ $order->customer_email }}</div>
        </div>
        <div class="info-col" style="text-align: right;">
            <div class="section-title">Order Details:</div>
            <div><strong>Order Number:</strong> {{ $order->order_number }}</div>
            <div><strong>Order Date:</strong> {{ optional($order->order_date)->format('d M Y') }}</div>
            <div><strong>School:</strong> {{ $order->school?->name ?? '—' }}</div>
            <div><strong>Student:</strong> {{ $order->student_name }} ({{ $order->grade }})</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 40px;">#</th>
                <th>Item Description</th>
                <th>Size</th>
                <th style="text-align: center; width: 60px;">Qty</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div style="font-weight: bold;">{{ $item->item_name }}</div>
                        <div style="font-size: 9px; color: #666;">Type: {{ ucfirst($item->product_type) }}</div>
                    </td>
                    <td>{{ $item->size }}</td>
                    <td style="text-align: center;">{{ $item->quantity }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <div class="section-title">Notes:</div>
        <div style="padding: 10px; background: #f9fafb; border: 1px solid #e5e7eb; min-height: 40px;">
            {{ $order->notes ?? 'No special instructions.' }}
        </div>
    </div>

    <div class="footer">
        Thank you for shopping with The Skool Store! For support, contact us at support@theskoolstore.com
    </div>
</body>
</html>
