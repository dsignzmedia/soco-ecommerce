@extends('admin.layouts.base')

@php $routePrefix = $routePrefix ?? 'master.admin'; @endphp

@section('title', 'Payments | The Skool Store')
@section('page_heading', 'Payments')
@section('page_subheading', 'Monitor and manage all payment transactions')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; vertical-align: middle; white-space: nowrap; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; background-color: #f9fafb; font-weight: 600; }
        td small { color:#6b7280; display:block; font-size: 11px; margin-top: 2px; }
        tr:hover td { background-color: #f9fafb; }
        
        .filters { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 16px; 
            margin-bottom: 24px; 
            align-items: start; 
        }
        
        /* Common style for Inputs and Date wrappers */
        .filters input, .filters select, .filters .date-group input { 
            width: 100%; 
            height: 46px !important; 
            padding: 0 16px; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px !important; 
            font-size: 14px; 
            color: #374151; 
            background-color: #fff; 
            box-sizing: border-box; 
            outline: none;
            font-family: inherit;
            appearance: none; /* For selects */
        }
        /* Add custom arrow for selects */
        .filters select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%236B7280' stroke-width='2'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            padding-right: 40px;
        }

        .filters input:focus, .filters select:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }

        /* Buttons matching the inputs */
        .filters button, .filters a.reset { 
            width: 100%; 
            height: 46px !important; 
            border-radius: 12px !important; 
            font-weight: 600; 
            text-align: center; 
            padding: 0 16px; 
            font-size: 14px; 
            transition: all 0.2s; 
            box-sizing: border-box; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            cursor: pointer;
        }
        .filters button { 
            border: none; 
            background: #490d59; 
            color: #fff; 
            box-shadow: 0 1px 2px rgba(16, 24, 40, 0.05); 
        }
        .filters button:hover { background: #370a43; }
        
        .filters a.reset { 
            border: 1px solid #e5e7eb; 
            color: #374151; 
            text-decoration: none; 
            background: #fff; 
        }
        .filters a.reset:hover { 
            background: #f9fafb; 
            border-color: #d1d5db; 
            color: #111827;
        }

        .btn-vs-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; border: 1px solid #d0d5dd; background: white; color: #490d59; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; }
        .btn-vs-sm:hover { background-color: #f3e8f5; border-color: #490d59; text-decoration: none; color: #490d59; }

        /* Custom Pagination Styling */
        .pagination-container {
            padding: 12px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        .pagination-container nav > div:first-child { display: none !important; }
        .pagination-container nav > div:last-child {
            display: flex !important;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }
        .pagination-container p { font-size: 13px; color: #6b7280; margin: 0; }
        .pagination-container nav span[class*="shadow-sm"], .pagination-container nav div[class*="shadow-sm"] {
            box-shadow: none !important;
            display: inline-flex;
            gap: 4px;
        }
        .pagination-container nav a, .pagination-container nav span[aria-disabled], .pagination-container nav span[aria-current="page"] > span {
            display: inline-flex; align-items: center; justify-content: center; padding: 0 !important;
            border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff; color: #374151;
            font-size: 13px; font-weight: 500; text-decoration: none; width: 36px !important; height: 36px !important;
            margin: 0 !important; cursor: pointer; box-sizing: border-box !important;
        }
        .pagination-container nav span[aria-current="page"] > span { background-color: #490d59 !important; border-color: #490d59 !important; color: white !important; }
        .pagination-container nav span[aria-disabled] { opacity: 0.6; cursor: not-allowed; background: #f9fafb; }
        .pagination-container nav a:hover { background-color: #f9fafb; border-color: #d1d5db !important; color: #111827; }
        .pagination-container nav svg { width: 16px; height: 16px; }
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Payment Filters</h3>
        </div>
        <form class="filters" method="GET" action="{{ route($routePrefix . '.payments.index') }}">
             <!-- Search -->
             <input type="text" name="search" placeholder="Order No / Payment ID" value="{{ request('search') }}">

             <!-- Status -->
             <select name="status">
                <option value="">Payment Status (All)</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                <option value="refunded" {{ request('status') == 'refunded' ? 'selected' : '' }}>Refunded</option>
             </select>

             <!-- Method -->
             <select name="payment_method">
                <option value="">Payment Method (All)</option>
                <option value="razorpay" {{ request('payment_method') == 'razorpay' ? 'selected' : '' }}>Razorpay</option>
                <option value="cod" {{ request('payment_method') == 'cod' ? 'selected' : '' }}>COD</option>
             </select>

             <!-- Date From -->
             <input type="date" name="date_from" placeholder="From Date" onclick="this.showPicker()" value="{{ request('date_from') }}">
             
             <!-- Date To -->
             <input type="date" name="date_to" placeholder="To Date" onclick="this.showPicker()" value="{{ request('date_to') }}">

             <button type="submit">Apply Filter</button>
             <a href="{{ route('master.admin.payments.index') }}" class="reset">Reset</a>
        </form>
    </section>

    <section class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Type</th>
                        <th>Product Type</th>
                        <th>Order</th>
                        <th>Gateway ID</th>
                        <th>Method</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:right;">Tax</th>
                        <th style="text-align:right;">Ship</th>
                        <th style="text-align:right;">Paid</th>
                        <th style="text-align:center;">Status</th>
                        <th>Date</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                    <tr>
                        <td style="text-align: center;">{{ $payments->firstItem() + $loop->index }}</td>
                        <!-- Payment For (Type) -->
                        <td style="text-align:center;">
                            @if(($payment->payment_for ?? 'order') == 'order')
                                <span style="background:#f3e8ff; color:#6b21a8; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Order</span>
                            @elseif(($payment->payment_for ?? '') == 'refund')
                                <span style="background:#fee2e2; color:#b91c1c; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Refund</span>
                            @else
                                <span style="background:#f3f4f6; color:#374151; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ ucfirst($payment->payment_for ?? 'Order') }}</span>
                            @endif
                        </td>
                        <td>
                            @if($payment->product_type)
                                @if($payment->product_type == 'merchandised')
                                    <span style="background:#e0f2fe; color:#0369a1; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Merchandise</span>
                                @elseif($payment->product_type == 'back_to_school')
                                    <span style="background:#dcfce7; color:#166534; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Back to School</span>
                                @else
                                    <span style="background:#f3f4f6; color:#374151; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ ucfirst(str_replace('_', ' ', $payment->product_type)) }}</span>
                                @endif
                            @else
                                <span style="color:#9ca3af; font-size: 11px;">—</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $orderShowRoute = match($payment->product_type) {
                                    'merchandised' => 'admin.merchandise.orders.show',
                                    'back_to_school' => 'admin.back_to_school.orders.show',
                                    default => 'master.admin.orders.show'
                                };
                            @endphp
                            @if($payment->order)
                                <a href="{{ route($orderShowRoute, $payment->order->id) }}" style="color:#490d59; font-weight:600; text-decoration:none;">
                                    {{ $payment->order->order_number }}
                                </a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td style="font-family: monospace; color: #555;">{{ $payment->payment_id ?? 'N/A' }}</td>
                        <!-- Removed payment_type column to match new header layout -->
                        <td>{{ ucfirst($payment->payment_method) }}</td>
                        <td style="text-align:right;">₹{{ number_format($payment->total_amount, 2) }}</td>
                        <td style="text-align:right;">₹{{ number_format($payment->tax_amount, 2) }}</td>
                        <td style="text-align:right;">₹{{ number_format($payment->shipping_cost, 2) }}</td>
                        <td style="text-align:right;">
                             <span class="text-success" style="font-weight:600;">₹{{ number_format($payment->amount_paid, 2) }}</span>
                        </td>
                        <td style="text-align:center;">
                            @if($payment->payment_status == 'paid')
                                <span style="background:#ecfdf5; color:#047857; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Paid</span>
                            @elseif($payment->payment_status == 'pending')
                                <span style="background:#fff7ed; color:#c2410c; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Pending</span>
                            @elseif($payment->payment_status == 'failed')
                                <span style="background:#fef2f2; color:#b91c1c; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Failed</span>
                            @elseif($payment->payment_status == 'refunded')
                                <span style="background:#eff6ff; color:#1d4ed8; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">Refunded</span>
                            @else
                                <span style="background:#f3f4f6; color:#374151; padding: 2px 8px; border-radius: 12px; font-size: 11px; font-weight: 600;">{{ ucfirst($payment->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            <div style="color:#111827;">{{ $payment->created_at->format('d M Y') }}</div>
                            <small>{{ $payment->created_at->format('h:i A') }}</small>
                        </td>
                        <td style="text-align:right;">
                            <a href="{{ route($routePrefix . '.payments.show', $payment->id) }}" class="btn-vs-sm" title="View Details">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="12" style="text-align:center; padding: 40px; color: #6b7280;">
                            <div style="margin-bottom: 8px; font-size: 24px; color: #d1d5db;"><i class="fas fa-search"></i></div>
                            No payment records found matching your filters.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pagination-container">
            {{ $payments->appends(request()->all())->onEachSide(1)->links() }}
        </div>
    </section>
@endsection
