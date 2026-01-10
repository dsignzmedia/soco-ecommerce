<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Catalog Export</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #111827;
        }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { 
            text-align: left; 
            padding: 8px; 
            border-bottom: 2px solid #333; 
            font-weight: bold; 
            background-color: #f3f4f6;
        }
        td { 
            padding: 8px; 
            border-bottom: 1px solid #e5e7eb; 
            vertical-align: top;
        }
        tr:nth-child(even) { background-color: #f9fafb; }
        .header { margin-bottom: 30px; }
        .school-group { 
            margin-top: 30px; 
        }
        h2 { margin: 0; font-size: 18px; }
        h3 { margin: 15px 0 10px; font-size: 14px; color: #490d59; border-bottom: 1px solid #490d59; padding-bottom: 5px; }
        .meta { color: #6b7280; font-size: 10px; margin-top: 5px; }
        .text-right { text-align: right; }
        .badge {
            display: inline-block;
            padding: 2px 4px;
            font-size: 9px;
            border-radius: 4px;
            background: #e5e7eb;
        }
        .status-live { color: #027a48; background: #ecfdf3; }
        .status-draft { color: #344054; background: #f2f4f7; }
        .status-archived { color: #b42318; background: #fef3f2; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Products & Catalog Export</h2>
        <div class="meta">Generated: {{ now()->format('d M Y, h:i A') }} | Total Products: {{ $products->count() }}</div>
    </div>

    @php
        $grouped = $products->groupBy(function ($product) {
            return $product->school ? $product->school->name : 'Unassigned';
        });
    @endphp

    @foreach($grouped as $schoolName => $schoolProducts)
        <div class="school-group">
            <h3>{{ $schoolName }} ({{ $schoolProducts->count() }})</h3>
            <table>
                <thead>
                    <tr>
                        <th style="width: 25%;">Product Name</th>
                        <th style="width: 10%;">Grade</th>
                        <th style="width: 10%;">Category</th>
                        <th style="width: 10%;">Attributes</th>
                        <th style="width: 15%;">Prices (Reg/Sale)</th>
                        <th style="width: 10%;">Stock</th>
                        <th style="width: 10%;">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($schoolProducts as $product)
                        <tr>
                            <td>
                                <strong>{{ $product->product_name }}</strong><br>
                                <span style="color: #6b7280;">{{ Str::limit($product->product_type, 20) }}</span>
                            </td>
                            <td>{{ $product->grade ?? 'All' }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $product->category)) }}</td>
                            <td>
                                {{ ucfirst($product->gender) }}
                            </td>
                            <td>
                                ₹{{ number_format($product->price_regular, 2) }}
                                @if($product->price_sale)
                                    <br><span style="color: #b42318;">₹{{ number_format($product->price_sale, 2) }}</span>
                                @endif
                            </td>
                            <td>
                                {{ $product->inventory_stock }}
                                @if($product->inventory_stock <= $product->low_stock_threshold)
                                    <span style="color: #b42318; font-weight: bold;">!</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge status-{{ $product->status }}">
                                    {{ ucfirst($product->status) }}
                                </span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</body>
</html>
