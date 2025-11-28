@extends('inventoryadmin.layouts.base')

@section('title', 'Orders | Inventory Admin')
@section('page_heading', 'Orders List')
@section('page_subheading', 'Manage warehouse fulfillment and dispatch')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; }
        td small { color:#98a2b3; display:block; }
        .filters { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin-bottom: 20px; }
        .filters button, .filters a.reset { border-radius:8px; font-weight:600; text-align:center; padding:10px 16px; font-size: 13px; }
        .filters button { border:none; background:#4f46e5; color:#fff; cursor: pointer; }
        .filters a.reset { border:1px solid #d0d5dd; color:#475467; display: inline-block; text-decoration: none; }
        .actions { display: flex; gap: 8px; }
        .actions a { font-weight:600; color:#4f46e5; font-size: 12px; }
        .status-pill { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; text-transform:capitalize; display: inline-block; }
        .status-pending { background:#fef3c7; color:#d97706; }
        .status-processing { background:#eff6ff; color:#1d4ed8; }
        .status-ready_to_ship { background:#f0fdf4; color:#15803d; }
        .status-shipped { background:#ecfdf3; color:#027a48; }
        .status-delivered { background:#f0fdfa; color:#0f766e; }
        .status-cancelled { background:#fef2f2; color:#b91c1c; }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px;">
        <form class="filters" method="GET">
            <select name="school_id" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="order_status" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
                <option value="">All Statuses</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['order_status'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="order_number" placeholder="Order ID" value="{{ $filters['order_number'] ?? '' }}" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
            <button type="submit">Filter</button>
            <a class="reset" href="{{ route('inventory.admin.orders.index') }}">Reset</a>
        </form>
    </div>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Customer / Address</th>
                    <th>School / Grade</th>
                    <th>Item / Qty</th>
                    <th>Status</th>
                    <th>Tracking</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                        </td>
                        <td>{{ optional($order->order_date)->format('d M Y') }}</td>
                        <td>
                            {{ $order->customer_name }}
                            <small>{{ Str::limit($order->customer_address, 40) }}</small>
                        </td>
                        <td>
                            {{ $order->school?->name ?? '—' }}
                            <small>Grade {{ $order->grade }}</small>
                        </td>
                        <td>
                            {{ $order->item_name }}
                            <small>Size: {{ $order->size }} | Qty: {{ $order->quantity }}</small>
                        </td>
                        <td>
                            <span class="status-pill status-{{ $order->order_status }}">
                                {{ $statuses[$order->order_status] ?? ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td>{{ $order->tracking_number ?? '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('inventory.admin.orders.show', $order) }}">Manage</a>
                            @if($order->order_status === 'pending')
                                <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}">
                                    @csrf
                                    <input type="hidden" name="order_status" value="processing">
                                    <button type="submit" style="background:none;border:none;color:#4f46e5;font-weight:600;font-size:12px;cursor:pointer;padding:0;">Pick</button>
                                </form>
                            @elseif($order->order_status === 'processing')
                                <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}">
                                    @csrf
                                    <input type="hidden" name="order_status" value="ready_to_ship">
                                    <button type="submit" style="background:none;border:none;color:#4f46e5;font-weight:600;font-size:12px;cursor:pointer;padding:0;">Pack</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center;padding:24px;color:#94a3b8;">No orders found matching your criteria.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
