@extends('admin.layouts.base')

@section('title', 'Coupon Details | The Skool Store')
@section('page_heading', 'Coupon Details: ' . $coupon->code)
@section('sub_page_heading')
    <div style="font-size: 14px; color: #666;">
        Displaying orders placed using this coupon.
        <span class="badge {{ $coupon->isValid() ? 'bg-success' : 'bg-secondary' }}" style="margin-left: 10px;">
            {{ $coupon->isValid() ? 'Active' : 'Expired/Inactive' }}
        </span>
    </div>
@endsection

@section('content')
<div class="card mb-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-1">Coupon Information</h5>
            <p class="mb-0 text-muted">Generated on: {{ $coupon->created_at->format('M d, Y h:i A') }}</p>
            <p class="mb-0 text-muted">Expires on: {{ $coupon->valid_to ? $coupon->valid_to->format('M d, Y h:i A') : 'Never' }}</p>
        </div>
        <div>
            <a href="{{ route('master.admin.coupons.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left me-2"></i> Back to Coupons
            </a>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header bg-light">
        <h6 class="mb-0">Orders ({{ $orders->total() }})</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Date</th>
                    <th>Customer</th>
                    <th>Total Amount</th>
                    <th>Status</th>
                    <th>Items</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <a href="{{ route('master.admin.orders.show', $order->id) }}" style="font-family: monospace; font-weight: 600; text-decoration: none;">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td>
                            <div>{{ $order->customer_name }}</div>
                            <small class="text-muted">{{ $order->customer_phone }}</small>
                        </td>
                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>
                            <span class="badge bg-{{ $order->order_status == 'delivered' ? 'success' : ($order->order_status == 'cancelled' ? 'danger' : 'warning') }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td>
                            {{ $order->item_name }} ({{ $order->quantity }})
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            No orders placed with this coupon yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
        <div class="card-footer">
            {{ $orders->links() }}
        </div>
    @endif
</div>
@endsection
