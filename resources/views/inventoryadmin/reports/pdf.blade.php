<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Inventory Report</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 8px; border: 1px solid #ddd; text-align: left; font-size: 12px; }
        th { background-color: #f4f4f4; font-weight: bold; }
        h1 { margin-bottom: 5px; font-size: 24px; color: #333; }
        .meta { margin-bottom: 20px; font-size: 12px; color: #666; }
        .status { padding: 2px 6px; border-radius: 4px; font-size: 10px; font-weight: bold; }
        .success { background: #d1fae5; color: #065f46; }
        .warning { background: #fef3c7; color: #92400e; }
        .danger { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>
    <h1>Inventory Report</h1>
    <div class="meta">
        Generated on: {{ now()->format('d M Y, h:i A') }} <br>
        Filters applied: 
        @if(empty(array_filter($filters)))
            None
        @else
            @foreach($filters as $key => $value)
                @if($value) <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> {{ $value }} @endif
            @endforeach
        @endif
    </div>

    <table>
        <thead>
            <tr>
                <th>Product Name</th>
                <th>School</th>
                <th>Grade</th>
                <th>Category</th>
                <th style="text-align:right;">Stock</th>
                <th style="text-align:right;">Threshold</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($exportProducts as $product)
            <tr>
                <td>{{ $product->product_name }}</td>
                <td>{{ $product->school?->name ?? '—' }}</td>
                <td>{{ $product->grade?->name ?? '—' }}</td>
                <td>{{ $product->category ?? '—' }}</td>
                <td style="text-align:right;">{{ $product->inventory_stock }}</td>
                <td style="text-align:right;">{{ $product->low_stock_threshold }}</td>
                <td>
                    @if($product->inventory_stock <= 0)
                        <span class="status danger">Out of Stock</span>
                    @elseif($product->inventory_stock <= $product->low_stock_threshold)
                        <span class="status warning">Low Stock</span>
                    @else
                        <span class="status success">In Stock</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
