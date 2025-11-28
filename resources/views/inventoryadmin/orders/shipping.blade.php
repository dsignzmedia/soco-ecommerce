@extends('inventoryadmin.layouts.base')

@section('title', 'Shipping | Inventory Admin')
@section('page_heading', 'Shipping')
@section('page_subheading', 'Orders ready for dispatch')

@section('content')
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
                        <td style="padding:16px 12px;">
                            <div style="display:flex;gap:8px;">
                                <a href="{{ route('inventory.admin.orders.show', $order) }}" style="color:#4f46e5;font-weight:600;font-size:13px;">Manage</a>
                                <span style="color:#cbd5e1;">|</span>
                                <a href="{{ route('inventory.admin.orders.print-label', $order) }}" target="_blank" style="color:#475467;font-weight:600;font-size:13px;">Print Label</a>
                                <span style="color:#cbd5e1;">|</span>
                                <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="order_status" value="shipped">
                                    <button type="submit" style="background:none;border:none;color:#059669;font-weight:600;font-size:13px;cursor:pointer;padding:0;">Mark Shipped</button>
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
