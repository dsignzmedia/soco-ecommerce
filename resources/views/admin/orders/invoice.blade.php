@extends('admin.layouts.base')

@section('title', 'Invoice ' . $order->order_number)
@section('page_heading', 'Invoice ' . $order->order_number)
@section('page_subheading', 'Formal breakdown suitable for download & print')

@section('content')
    <section class="card" style="max-width:960px;margin:auto;">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:24px;">
            <div>
                <h2 style="margin:0;color:#111827;">The Skool Store</h2>
                <p style="margin:4px 0 0;color:#475467;">Uniform procurement & fulfilment</p>
            </div>
            <div style="text-align:right;">
                <p style="margin:0;color:#475467;">Invoice #: {{ $order->order_number }}</p>
                <p style="margin:0;color:#475467;">Date: {{ optional($order->order_date)->format('d M Y') }}</p>
            </div>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:24px;margin-bottom:24px;">
            <div>
                <h4 style="margin:0 0 6px;color:#111827;">Bill To</h4>
                <p style="margin:0;color:#475467;">
                    {{ $order->customer_name }}<br>
                    {{ $order->customer_address }}<br>
                    {{ $order->customer_phone }}<br>
                    {{ $order->customer_email ?? 'No email' }}
                </p>
            </div>
            <div>
                <h4 style="margin:0 0 6px;color:#111827;">School / Student</h4>
                <p style="margin:0;color:#475467;">
                    {{ $order->school?->name ?? '—' }}<br>
                    {{ $order->student_name ?? '—' }} (Grade {{ $order->grade ?? '—' }})
                </p>
            </div>
        </div>

        <table style="width:100%;border-collapse:collapse;margin-bottom:24px;">
            <thead>
                <tr>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Item</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Category</th>
                    <th style="text-align:left;padding:10px;border-bottom:1px solid #e5e7eb;">Size</th>
                    <th style="text-align:center;padding:10px;border-bottom:1px solid #e5e7eb;">Qty</th>
                    <th style="text-align:right;padding:10px;border-bottom:1px solid #e5e7eb;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding:10px;">{{ $order->item_name }}</td>
                    <td style="padding:10px;">{{ $order->category }}</td>
                    <td style="padding:10px;">{{ $order->size }}</td>
                    <td style="padding:10px;text-align:center;">{{ $order->quantity }}</td>
                    <td style="padding:10px;text-align:right;">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div style="display:flex;justify-content:flex-end;">
            <table style="min-width:280px;border-collapse:collapse;">
                <tr>
                    <td style="padding:6px 0;color:#475467;">Tax</td>
                    <td style="padding:6px 0;text-align:right;">₹{{ number_format($order->tax_amount, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;color:#475467;">Shipping</td>
                    <td style="padding:6px 0;text-align:right;">₹{{ number_format($order->shipping_cost, 2) }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0;font-weight:600;color:#111827;">Total</td>
                    <td style="padding:6px 0;text-align:right;font-weight:600;color:#111827;">₹{{ number_format($order->total_amount, 2) }}</td>
                </tr>
            </table>
        </div>

        <p style="margin-top:32px;color:#98a2b3;font-size:12px;">Payment status: {{ ucfirst($order->payment_status) }} • Order status: {{ ucfirst($order->order_status) }}</p>
    </section>
@endsection

