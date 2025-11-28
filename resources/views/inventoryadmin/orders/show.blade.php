@extends('inventoryadmin.layouts.base')

@section('title', 'Order ' . $order->order_number . ' | Inventory Admin')
@section('page_heading', 'Order ' . $order->order_number)
@section('page_subheading', 'Fulfillment details and status updates')

@section('content')
    <div style="margin-bottom:24px;">
        <a href="{{ route('inventory.admin.orders.index') }}" style="color:#4f46e5;font-weight:600;text-decoration:none;">← Back to Orders List</a>
    </div>

    <div style="display:grid;grid-template-columns:2fr 1fr;gap:24px;">
        <!-- Left Column: Order Details -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Items -->
            <div class="card">
                <h4 style="margin:0 0 16px;color:#0f172a;">Items to Fulfill</h4>
                <table style="width:100%;border-collapse:collapse;">
                    <thead>
                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Item</th>
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Size</th>
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Qty</th>
                            <th style="text-align:left;padding:8px;font-size:12px;color:#64748b;">Category</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="padding:12px 8px;font-weight:500;">{{ $order->item_name }}</td>
                            <td style="padding:12px 8px;">{{ $order->size }}</td>
                            <td style="padding:12px 8px;">{{ $order->quantity }}</td>
                            <td style="padding:12px 8px;">{{ $order->category }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Shipping Info -->
            <div class="card">
                <h4 style="margin:0 0 16px;color:#0f172a;">Shipping Information</h4>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;">
                    <div>
                        <p style="margin:0 0 4px;font-size:12px;color:#64748b;text-transform:uppercase;">Customer</p>
                        <p style="margin:0;font-weight:500;">{{ $order->customer_name }}</p>
                        <p style="margin:4px 0 0;font-size:14px;color:#475467;">{{ $order->customer_phone }}</p>
                    </div>
                    <div>
                        <p style="margin:0 0 4px;font-size:12px;color:#64748b;text-transform:uppercase;">Address</p>
                        <p style="margin:0;line-height:1.5;">{{ $order->customer_address }}</p>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="card">
                <h4 style="margin:0 0 16px;color:#0f172a;">Order Notes</h4>
                <p style="margin:0;color:#475467;font-style:italic;">{{ $order->notes ?? 'No notes provided.' }}</p>
            </div>
        </div>

        <!-- Right Column: Actions -->
        <div style="display:flex;flex-direction:column;gap:24px;">
            <!-- Status Update -->
            <div class="card" style="background:#f8fafc;border:1px solid #e2e8f0;">
                <h4 style="margin:0 0 16px;color:#0f172a;">Update Status</h4>
                <form method="POST" action="{{ route('inventory.admin.orders.status', $order) }}">
                    @csrf
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Current Status</label>
                        <select name="order_status" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
                            <option value="pending" @selected($order->order_status == 'pending')>Pending (To Pick)</option>
                            <option value="processing" @selected($order->order_status == 'processing')>Processing (To Pack)</option>
                            <option value="ready_to_ship" @selected($order->order_status == 'ready_to_ship')>Ready to Ship</option>
                            <option value="shipped" @selected($order->order_status == 'shipped')>Shipped</option>
                            <option value="delivered" @selected($order->order_status == 'delivered')>Delivered</option>
                        </select>
                    </div>
                    
                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Tracking Number</label>
                        <input type="text" name="tracking_number" value="{{ $order->tracking_number }}" placeholder="Enter tracking ID" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Courier Name</label>
                        <input type="text" name="courier_name" value="{{ $order->courier_name }}" placeholder="e.g. FedEx, DHL" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">
                    </div>

                    <div style="margin-bottom:16px;">
                        <label style="display:block;font-size:13px;font-weight:500;margin-bottom:6px;">Internal Notes</label>
                        <textarea name="notes" rows="3" style="width:100%;padding:8px;border:1px solid #cbd5e1;border-radius:6px;">{{ $order->notes }}</textarea>
                    </div>

                    <button type="submit" style="width:100%;background:#4f46e5;color:#fff;border:none;padding:10px;border-radius:6px;font-weight:600;cursor:pointer;">Update Order</button>
                </form>
            </div>

            <!-- Documents -->
            <div class="card">
                <h4 style="margin:0 0 16px;color:#0f172a;">Documents</h4>
                <div style="display:flex;flex-direction:column;gap:12px;">
                    <a href="{{ route('inventory.admin.orders.packing-slip', $order) }}" target="_blank" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:#475467;padding:8px;border:1px solid #e2e8f0;border-radius:6px;transition:background 0.2s;">
                        <span style="font-size:20px;">📄</span>
                        <div>
                            <span style="display:block;font-weight:500;color:#0f172a;">Packing Slip</span>
                            <span style="font-size:12px;">Download PDF</span>
                        </div>
                    </a>
                    <a href="{{ route('inventory.admin.orders.print-label', $order) }}" target="_blank" style="display:flex;align-items:center;gap:10px;text-decoration:none;color:#475467;padding:8px;border:1px solid #e2e8f0;border-radius:6px;transition:background 0.2s;">
                        <span style="font-size:20px;">🏷️</span>
                        <div>
                            <span style="display:block;font-weight:500;color:#0f172a;">Shipping Label</span>
                            <span style="font-size:12px;">Print Label</span>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
