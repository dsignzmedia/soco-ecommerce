@extends('inventoryadmin.layouts.base')

@section('title', 'Shipping | Inventory Admin')
@section('page_heading', 'Shipping')
@section('page_subheading', 'Orders ready for dispatch')

@section('content')
    <style>
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .filter-form-grid {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-input-rounded {
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px;
            font-family: inherit;
            min-width: 160px;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }
        .btn-filter {
            background-color: #490d59;
            color: #ffffff;
            border: none;
            height: 46px;
            padding: 0 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-filter:hover {
            background-color: #3b0a48;
        }
        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            text-decoration: none;
            height: 46px;
            padding: 0 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-reset:hover {
            border-color: #d0d5dd;
            color: #0f172a;
            background: #f8fafc;
        }
    </style>

    <div class="filters-card">
        <form method="GET" action="{{ route('inventory.admin.orders.shipping') }}" class="filter-form-grid">
            <select name="school_id" class="filter-input-rounded no-tom">
                <option value="">School (All)</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ ($filters['school_id'] ?? '') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
            
            <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Search Order # or Customer" class="filter-input-rounded" style="flex: 1;">
            
            <button type="submit" class="btn-filter">Filter</button>
            <a href="{{ route('inventory.admin.orders.shipping') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    <div class="card" style="overflow-x:auto;">
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr style="border-bottom:1px solid #e5e7eb;">
                    <th style="text-align:left;padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;">Order</th>
                    <th style="text-align:left;padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;">Customer</th>
                    <th style="text-align:left;padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;">Courier</th>
                    <th style="text-align:left;padding:12px;font-size:12px;color:#64748b;text-transform:uppercase;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:16px 12px;">
                            <strong>{{ $order->order_number }}</strong>
                            <div style="font-size:13px;color:#64748b;margin-top:4px;">{{ optional($order->order_date)->format('d M Y') }}</div>
                        </td>
                        <td style="padding:16px 12px;">
                            {{ $order->customer_name }}
                            <div style="font-size:13px;color:#64748b;margin-top:4px;">{{ Str::limit($order->customer_address, 40) }}</div>
                        </td>
                        <td style="padding:16px 12px;">
                            @if($order->courier_name)
                                <span style="background:#f0fdf4;color:#15803d;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:600;">{{ $order->courier_name }}</span>
                            @else
                                <span style="color:#94a3b8;font-style:italic;">Not assigned</span>
                            @endif
                        </td>
                        <td class="actions">
                            <div class="d-flex flex-wrap gap-2" style="display:flex;gap:8px;">
                                <a href="{{ route('inventory.admin.orders.show', $order) }}" class="btn-vs-sm">Manage</a>
                                <a href="{{ route('inventory.admin.orders.print-label', $order) }}" target="_blank" class="btn-vs-sm">Print Label</a>
                                <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}" style="margin:0;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="shipped">
                                    <button type="submit" class="btn-vs-sm">Mark Shipped</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:32px;color:#94a3b8;">No orders currently ready to ship.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
