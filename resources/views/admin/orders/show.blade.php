@extends('admin.layouts.base')

@section('title', 'Order ' . $order->order_number)
@section('page_heading', 'Order ' . $order->order_number)
@section('page_subheading', 'Full order detail, payment audit and fulfilment trail')

@section('content')
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="margin:0;color:#475467;">Placed on {{ optional($order->order_date)->format('d M Y') }} • {{ $order->school?->name ?? 'Unoffiliated school' }}</p>
        </div>
        <a href="{{ route('master.admin.orders.index') }}" style="color:#490d59;font-weight:600;">← Back to orders</a>
    </section>

    <section class="card" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:16px;">
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Customer</h4>
            <p style="margin:0;color:#475467;">
                {{ $order->customer_name }}<br>
                {{ $order->customer_phone }}<br>
                {{ $order->customer_email ?? 'No email' }}<br>
                {{ $order->customer_address }}
            </p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Student / School</h4>
            <p style="margin:0;color:#475467;">
                {{ $order->student_name ?? '—' }}<br>
                Grade {{ $order->grade ?? '—' }}<br>
                {{ $order->school?->name ?? '—' }}
            </p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Status</h4>
            <p style="margin:0;color:#475467;">
                Order: {{ ucfirst($order->order_status) }}<br>
                Payment: {{ ucfirst($order->payment_status) }}<br>
                Tracking: {{ $order->tracking_number ?? 'Pending' }}<br>
                Returns/Exchange: {{ $order->return_exchange_status ?? '—' }}
            </p>
        </div>
    </section>

    <section class="card">
        <h4 style="margin:0 0 12px;color:#111827;">Items</h4>
        <table style="width:100%;border-collapse:collapse;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Item</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Qty</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Size</th>
                    <th style="text-align:left;padding:8px;border-bottom:1px solid #e5e7eb;">Category</th>
                    <th style="text-align:right;padding:8px;border-bottom:1px solid #e5e7eb;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:8px;">{{ $order->item_name }}</td>
                    <td style="padding:8px;">{{ $order->quantity }}</td>
                    <td style="padding:8px;">{{ $order->size }}</td>
                    <td style="padding:8px;">{{ $order->category }}</td>
                    <td style="padding:8px;text-align:right;">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </section>

    <section class="card" style="display:flex;flex-wrap:wrap;gap:24px;">
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Financials</h4>
            <p style="margin:0;color:#475467;">
                Subtotal (inc tax): ₹{{ number_format($order->total_amount, 2) }}<br>
                Tax: ₹{{ number_format($order->tax_amount, 2) }}<br>
                Shipping: ₹{{ number_format($order->shipping_cost, 2) }}
            </p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Update status</h4>
            <form method="POST" action="{{ route('master.admin.orders.status', $order) }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">
                @csrf
                <select name="order_status">
                    @foreach(['processing','packed','shipped','delivered','returned','cancelled'] as $status)
                        <option value="{{ $status }}" @selected($order->order_status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <select name="payment_status">
                    <option value="">Payment unchanged</option>
                    @foreach(['pending','paid','failed','refunded'] as $status)
                        <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
                <input type="text" name="tracking_number" placeholder="Tracking number" value="{{ $order->tracking_number }}">
                <button type="submit" style="border:none;background:#490d59;color:#fff;border-radius:12px;padding:10px 16px;font-weight:600;">Update</button>
            </form>
        </div>
    </section>

    <section class="card" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('master.admin.orders.invoice', $order) }}" style="padding:10px 16px;border:1px solid #d0d5dd;border-radius:12px;color:#490d59;font-weight:600;">View invoice</a>
        <a href="{{ route('master.admin.orders.invoice.download', $order) }}" style="padding:10px 16px;border:1px solid #d0d5dd;border-radius:12px;color:#490d59;font-weight:600;">Download invoice</a>
    </section>
@endsection

