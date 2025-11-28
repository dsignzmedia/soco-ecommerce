@extends('admin.layouts.base')

@section('title', 'Orders | The Skool Store')
@section('page_heading', 'Orders')
@section('page_subheading', 'Monitor every order, payment, fulfilment and alert in one grid')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; }
        td small { color:#98a2b3; display:block; }
        .filters { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:12px; }
        .filters button, .filters a.reset { border-radius:12px; font-weight:600; text-align:center; }
        .filters button { border:none; background:#490d59; color:#fff; padding:10px 16px; }
        .filters a.reset { border:1px solid #d0d5dd; color:#475467; padding:10px 16px; }
        .actions a { margin-right:8px; font-weight:600; color:#490d59; }
        .status-pill { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; text-transform:capitalize; }
        .status-processing { background:#eff6ff; color:#1d4ed8; }
        .status-shipped { background:#ecfdf3; color:#027a48; }
        .status-failed { background:#fef3f2; color:#b42318; }
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:20px;">
        <h3 style="margin:0 0 16px;color:#111827;">Filters</h3>
        <form class="filters" method="GET">
            <select name="school_id">
                <option value="">School (All)</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade">
                <option value="">Grade (All)</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(($filters['grade'] ?? '') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">Category (All)</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="order_status">
                <option value="">Order status</option>
                @foreach(['processing','packed','shipped','delivered','returned','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['order_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="payment_status">
                <option value="">Payment status</option>
                @foreach(['pending','paid','failed','refunded'] as $status)
                    <option value="{{ $status }}" @selected(($filters['payment_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input type="text" name="order_number" placeholder="Order ID" value="{{ $filters['order_number'] ?? '' }}">
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            <button type="submit">Apply</button>
            <a class="reset" href="{{ route('master.admin.orders.index') }}">Reset</a>
        </form>
    </section>

    <section class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Order ID / Date</th>
                    <th>School / Student</th>
                    <th>Grade / Category</th>
                    <th>Item</th>
                    <th>Customer</th>
                    <th>Amount (GST inc)</th>
                    <th>Payment</th>
                    <th>Order status</th>
                    <th>Shipping</th>
                    <th>Tax</th>
                    <th>Tracking</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong>{{ $order->order_number }}</strong>
                            <small>{{ optional($order->order_date)->format('d M Y') }}</small>
                        </td>
                        <td>
                            {{ $order->school?->name ?? '—' }}
                            <small>{{ $order->student_name }}</small>
                        </td>
                        <td>
                            {{ $order->grade ?? '—' }}
                            <small>{{ $order->category ?? '—' }}</small>
                        </td>
                        <td>
                            {{ $order->item_name }} ({{ $order->size }}) x{{ $order->quantity }}
                        </td>
                        <td>
                            {{ $order->customer_name }}
                            <small>{{ $order->customer_phone }} • {{ $order->customer_email ?? 'No email' }}</small>
                            <small style="color:#475467;">{{ Str::limit($order->customer_address, 50) }}</small>
                        </td>
                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ ucfirst($order->payment_status) }}</td>
                        <td>
                            <span class="status-pill status-{{ $order->order_status }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                            @if($order->return_exchange_status)
                                <small style="color:#b42318;">{{ $order->return_exchange_status }}</small>
                            @endif
                        </td>
                        <td>₹{{ number_format($order->shipping_cost, 2) }}</td>
                        <td>₹{{ number_format($order->tax_amount, 2) }}</td>
                        <td>{{ $order->tracking_number ?? '—' }}</td>
                        <td class="actions">
                            <a href="{{ route('master.admin.orders.show', $order) }}">View</a>
                            <a href="{{ route('master.admin.orders.invoice', $order) }}">Invoice</a>
                            <a href="{{ route('master.admin.orders.invoice.download', $order) }}">Download</a>
                            <form method="POST" action="{{ route('master.admin.orders.status', $order) }}" style="display:inline-block;">
                                @csrf
                                <input type="hidden" name="order_status" value="shipped">
                                <button type="submit" style="border:none;background:none;color:#490d59;font-weight:600;padding:0;">Mark shipped</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="12">No orders match this filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $orders->links() }}
        </div>
    </section>
@endsection

