@extends('inventoryadmin.layouts.base')

@section('title', 'Order ' . $order->order_number . ' | Inventory Admin')
@section('page_heading', 'Order ' . $order->order_number)
@section('page_subheading', 'Fulfillment details and status updates')

@section('content')
<style>
    .card{
        margin-top:10px !important;
    }
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
    }
    .action-btn-primary {
        background: #111827;
        color: #ffffff;
    }
    .action-btn-primary:hover {
        background: #1f2937;
    }
    .action-btn-secondary {
        background: #ffffff;
        color: #111827;
        border: 2px solid #111827;
    }
    .action-btn-secondary:hover {
        background: #f9fafb;
    }
    .action-btn-success {
        background: #10b981;
        color: #ffffff;
    }
    .action-btn-success:hover {
        background: #059669;
    }
</style>

    <section class="card" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="margin:0;color:#475467;">Placed on {{ optional($order->order_date)->format('d M Y') }} • {{ $order->school?->name ?? 'Unoffiliated school' }}</p>
        </div>
        <a href="{{ route('inventory.admin.orders.index') }}" style="color:#111827;font-weight:600;text-decoration:none;">← Back to orders</a>
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
                Order: {{ ucfirst(str_replace('_', ' ', $order->order_status)) }}<br>
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

    <section class="card" style="display:grid;grid-template-columns: 1fr 2fr; gap:24px;">
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Financials</h4>
            <p style="margin:0;color:#475467;">
                Subtotal (inc tax): ₹{{ number_format($order->total_amount, 2) }}<br>
                Tax: ₹{{ number_format($order->tax_amount, 2) }}<br>
                Shipping: ₹{{ number_format($order->shipping_cost, 2) }}
            </p>
            @if($order->notes)
                <div style="margin-top:16px;">
                    <h4 style="margin:0 0 4px;color:#111827;">Internal Notes</h4>
                    <p style="margin:0;color:#475467;font-style:italic;">{{ $order->notes }}</p>
                </div>
            @endif
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Update status</h4>
            <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;">
                @csrf
                <select name="order_status" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:12px;">
                    @foreach(['order_placed' => 'Order Placed', 'processing' => 'Processing', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $value => $label)
                        <option value="{{ $value }}" @selected($order->order_status === $value)>{{ $label }}</option>
                    @endforeach
                    <option value="cancelled" @selected($order->order_status === 'cancelled')>Cancel Shipment</option>
                </select>
                <input type="text" name="tracking_number" placeholder="Tracking number" value="{{ $order->tracking_number }}" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:12px;">
                <input type="text" name="courier_name" placeholder="Courier Name" value="{{ $order->courier_name }}" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:12px;">
                <textarea name="notes" placeholder="Add internal notes..." rows="1" style="width:100%;padding:10px;border:1px solid #d1d5db;border-radius:12px;grid-column: 1 / -1;">{{ $order->notes }}</textarea>
                <button type="submit" style="grid-column: 1 / -1; border:none;background:#111827;color:#fff;border-radius:12px;padding:12px 16px;font-weight:600;cursor:pointer;">Update Order Details</button>
            </form>
        </div>
    </section>

    <section class="card" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('inventory.admin.orders.invoice', $order) }}" class="action-btn action-btn-primary">
            <i class="fas fa-file-invoice"></i>
            View invoice
        </a>
        <a href="{{ route('inventory.admin.orders.invoice-download', $order) }}" class="action-btn action-btn-secondary">
            <i class="fas fa-download"></i>
            Download PDF
        </a>
        <a href="{{ route('inventory.admin.orders.packing-slip', $order) }}" class="action-btn action-btn-secondary">
            <i class="fas fa-box-open"></i>
            Packing Slip
        </a>
        <a href="{{ route('inventory.admin.orders.print-label', $order) }}" class="action-btn action-btn-secondary">
            <i class="fas fa-barcode"></i>
            Print Label
        </a>
        <button type="button" class="action-btn action-btn-success" onclick="markAsShipped()">
            <i class="fas fa-shipping-fast"></i>
            Mark shipped
        </button>
    </section>

    <script>
        function markAsShipped() {
            if (confirm('Mark this order as shipped?')) {
                const form = document.querySelector('form[action*="status"]');
                if (form) {
                    const orderStatusSelect = form.querySelector('select[name="order_status"]');
                    if (orderStatusSelect) {
                        orderStatusSelect.value = 'shipped';
                        form.submit();
                    }
                }
            }
        }
    </script>
@endsection
