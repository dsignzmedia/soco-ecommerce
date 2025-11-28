@extends('inventoryadmin.layouts.base')

@section('title', 'Reports | Inventory Admin')
@section('page_heading', 'Stock Reports')
@section('page_subheading', 'Inventory health and distribution')

@section('content')
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;margin-bottom:24px;">
        <!-- Low Stock -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#d97706;">Low Stock Alerts</h4>
            @if($lowStock->isEmpty())
                <p style="color:#94a3b8;">No low stock items.</p>
            @else
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Product</th>
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">School</th>
                            <th style="text-align:right;padding:8px;font-size:12px;color:#64748b;">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lowStock as $item)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:8px;font-size:13px;">{{ $item->product_name }}</td>
                                <td style="padding:8px;font-size:13px;">{{ $item->school?->name }}</td>
                                <td style="padding:8px;font-size:13px;text-align:right;font-weight:600;color:#d97706;">{{ $item->inventory_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <!-- Out of Stock -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#b91c1c;">Out of Stock</h4>
            @if($outOfStock->isEmpty())
                <p style="color:#94a3b8;">No out of stock items.</p>
            @else
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Product</th>
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">School</th>
                            <th style="text-align:right;padding:8px;font-size:12px;color:#64748b;">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($outOfStock as $item)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:8px;font-size:13px;">{{ $item->product_name }}</td>
                                <td style="padding:8px;font-size:13px;">{{ $item->school?->name }}</td>
                                <td style="padding:8px;font-size:13px;text-align:right;font-weight:600;color:#b91c1c;">{{ $item->inventory_stock }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:24px;">
        <!-- By School -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#0f172a;">Stock by School</h4>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">School</th>
                        <th style="text-align:right;padding:8px;font-size:12px;color:#64748b;">Total Items</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockBySchool as $row)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 8px;font-size:13px;">{{ $row->school?->name ?? 'Unknown' }}</td>
                            <td style="padding:10px 8px;font-size:13px;text-align:right;font-weight:600;">{{ number_format($row->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- By Grade -->
        <div class="card">
            <h4 style="margin:0 0 16px;color:#0f172a;">Stock by Grade</h4>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb;">
                        <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Grade</th>
                        <th style="text-align:right;padding:8px;font-size:12px;color:#64748b;">Total Items</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($stockByGrade as $row)
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:10px 8px;font-size:13px;">{{ $row->grade?->name ?? 'Unknown' }}</td>
                            <td style="padding:10px 8px;font-size:13px;text-align:right;font-weight:600;">{{ number_format($row->total) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
