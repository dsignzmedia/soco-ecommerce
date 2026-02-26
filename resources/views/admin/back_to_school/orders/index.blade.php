@extends('admin.layouts.back_to_school')

@section('title', 'Orders | The Skool Store')
@section('page_heading', 'Orders')
@section('page_subheading', 'Monitor every order, payment, fulfilment and alert in one grid')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; vertical-align: middle; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; background-color: #f9fafb; font-weight: 600; }
        td small { color:#6b7280; display:block; font-size: 12px; margin-top: 2px; }
        tr:hover td { background-color: #f9fafb; }
        
        .filters { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 16px; 
            margin-bottom: 24px; 
            align-items: start; /* Align tops */
        }
        
        /* Common style for Inputs and Date wrappers */
        .filters input, .filters .date-group input { 
            width: 100%; 
            height: 46px !important; /* Match Tom Select height */
            padding: 0 16px; 
            border: 1px solid #e5e7eb; 
            border-radius: 12px !important; 
            font-size: 14px; 
            color: #374151; 
            background-color: #fff; 
            box-sizing: border-box; 
            outline: none;
            font-family: inherit;
        }
        .filters input:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }

        /* Remove old group styling if present */
        .filters .date-group { 
            width: 100%; 
            display: block; 
        }
        .filters .date-group label { display: none; } /* Hide labels if any */

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
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Order Filters</h3>
        </div>
        <form class="filters" method="GET">
            <select name="school_id[]" multiple placeholder="Select School">
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(in_array($school->id, (array)($filters['school_id'] ?? [])))>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade[]" multiple placeholder="Select Grade">
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(in_array($grade, (array)($filters['grade'] ?? [])))>{{ $grade }}</option>
                @endforeach
            </select>
            <select name="category[]" multiple placeholder="Select Category">
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(in_array($category, (array)($filters['category'] ?? [])))>{{ $category }}</option>
                @endforeach
            </select>
            <select name="order_status[]" multiple class="no-sort" placeholder="Order Status">
                <option value="">Order Status</option>
                {{-- Order statuses in workflow sequence: Order Placed -> Processing -> Packed -> Shipped -> Delivered --}}
                @foreach(['order_placed' => 'Order Placed', 'processing' => 'Processing', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $value => $label)
                    <option value="{{ $value }}" @selected(in_array($value, (array)($filters['order_status'] ?? [])))>{{ $label }}</option>
                @endforeach
            </select>

            <select name="product_type[]" multiple placeholder="Product Type">
                @foreach($productTypes as $type)
                    <option value="{{ $type }}" @selected(in_array($type, (array)($filters['product_type'] ?? [])))>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                @endforeach
            </select>

            <input type="text" name="order_number" placeholder="Order #" value="{{ $filters['order_number'] ?? '' }}">
            
            <div class="date-group">
                <input type="date" 
                    onclick="this.showPicker()"
                    name="date_from" 
                    placeholder="From Date"
                    value="{{ $filters['date_from'] ?? '' }}">
            </div>
            
            <div class="date-group">
                <input type="date" 
                    onclick="this.showPicker()"
                    name="date_to" 
                    placeholder="To Date"
                    value="{{ $filters['date_to'] ?? '' }}">
            </div>

            <div style="grid-column: 1 / -1; display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
                    <a href="{{ route('admin.back_to_school.orders.export', array_merge(request()->query(), ['export_type' => 'csv'])) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export Sheet</a>
                    <a href="{{ route('admin.back_to_school.orders.export', array_merge(request()->query(), ['export_type' => 'excel'])) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export Excel</a>
                    <a href="{{ route('admin.back_to_school.orders.export', array_merge(request()->query(), ['export_type' => 'pdf'])) }}" style="border: 1px solid #d0d5dd; border-radius: 999px; padding: 8px 14px; font-size: 13px; font-weight: 600; color: #490d59; text-decoration: none;">Export PDF</a>
                </div>
                <div style="display:flex;gap:10px;">
                    <button type="submit" style="width: auto; min-width: 120px;">Apply Filter</button>
                    <a href="{{ route('admin.back_to_school.orders.index') }}" class="reset" style="width: auto; min-width: 100px;">Reset</a>
                </div>
            </div>
        </form>
    </section>

    <section class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th style="width: 60px;">Image</th>
                        <th>Order Details</th>
                        <th>School / Student</th>
                        <th>Item Details</th>
                        <th>Customer</th>
                        <th style="text-align:right;">Amount</th>
                        <th style="text-align:center;">Payment</th>
                        <th style="min-width: 140px;">Order Status</th>
                        <th>Tracking</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($order->product && $order->product->featured_image)
                                    <img src="{{ asset('storage/' . $order->product->featured_image) }}" 
                                         alt="Product" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;"
                                         onerror="this.onerror=null; this.src='{{ asset('assets/img/no image/no_image.png') }}';">
                                @else
                                    <img src="{{ asset('assets/img/no image/no_image.png') }}" 
                                         alt="Default" 
                                         style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px; border: 1px solid #e5e7eb;">
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.back_to_school.orders.show', $order) }}" style="color:#490d59; font-weight:600; text-decoration:none;">{{ $order->order_number }}</a>
                                <small>{{ optional($order->order_date)->format('d M Y') }}</small>
                            </td>
                            <td>
                                <div style="font-weight:500; color:#111827;">{{ $order->school?->name ?? '—' }}</div>
                                <small>{{ $order->student_name }} ({{ $order->grade ?? '-' }})</small>
                            </td>
                            <td>
                                <div style="color:#111827; font-weight:500;">{{ Str::limit($order->item_name, 30) }}</div>
                                <small>Size: {{ $order->size }} | Qty: {{ $order->quantity }}</small>
                                <small>{{ $order->category ?? '' }}</small>
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $order->customer_name }}</div>
                                <small>{{ $order->customer_phone }}</small>
                            </td>
                            <td style="text-align:right;">
                                <div style="font-weight:600; color:#111827;">₹{{ number_format($order->total_amount, 2) }}</div>
                                @if($order->shipping_cost > 0)
                                    <small>+ Ship: ₹{{ number_format($order->shipping_cost, 0) }}</small>
                                @endif
                            </td>
                            <td style="text-align:center;">
                                <span style="
                                    padding: 2px 8px;
                                    border-radius: 12px;
                                    font-size: 11px;
                                    font-weight: 600;
                                    background: {{ $order->payment_status === 'paid' ? '#ecfdf5' : ($order->payment_status === 'failed' ? '#fef2f2' : '#fff7ed') }};
                                    color: {{ $order->payment_status === 'paid' ? '#047857' : ($order->payment_status === 'failed' ? '#b91c1c' : '#c2410c') }};
                                ">{{ ucfirst($order->payment_status) }}</span>
                            </td>
                            <td>
                                <form action="{{ route('admin.back_to_school.orders.status', $order) }}" method="POST">
                                    @csrf
                                    <select name="order_status" onchange="this.form.submit()" class="no-tom" style="
                                        padding: 6px 10px;
                                        border-radius: 6px;
                                        font-size: 12px;
                                        font-weight: 500;
                                        color: #1f2937;
                                        border: 1px solid #d1d5db;
                                        cursor: pointer;
                                        width: 100%;
                                        background-color: #fff;
                                        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
                                    ">
                                        @foreach([
                                            'order_placed' => 'Order Placed',
                                            'processing' => 'Processing',
                                            'packed' => 'Packed',
                                            'shipped' => 'Shipped',
                                            'delivered' => 'Delivered',
                                            'cancelled' => 'Cancel Shipment'
                                        ] as $value => $label)
                                            <option value="{{ $value }}" @selected($order->order_status == $value)>{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
                                @if($order->return_exchange_status)
                                    <div style="margin-top: 4px; font-size: 11px; padding: 2px 6px; background: #fef2f2; color: #b91c1c; border-radius: 4px; display: inline-block;">
                                        {{ $order->return_exchange_status }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if($order->tracking_number)
                                    <div style="font-family:monospace; font-size:12px; background:#f3f4f6; padding:2px 6px; border-radius:4px; display:inline-block;">
                                        {{ $order->tracking_number }}
                                    </div>
                                @else
                                    <span style="color:#9ca3af;">—</span>
                                @endif
                            </td>
                            <td style="text-align:right;">
                                <div style="display:flex; justify-content:flex-end; gap:6px;">
                                    <a href="{{ route('admin.back_to_school.orders.show', $order) }}" class="btn-vs-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.back_to_school.orders.invoice', $order) }}" class="btn-vs-sm" title="View Invoice">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('admin.back_to_school.orders.invoice.download', $order) }}" class="btn-vs-sm" title="Download Invoice">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" style="text-align:center; padding: 40px; color: #6b7280;">
                                <div style="margin-bottom: 8px; font-size: 24px; color: #d1d5db;"><i class="fas fa-search"></i></div>
                                No orders found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    <div class="pagination-container">
        {{ $orders->onEachSide(1)->links() }}
    </div>
    </section>
@endsection

@push('styles')
<style>
    /* Custom Pagination Styling */
    .pagination-container {
        padding: 12px 20px;
        border-top: 1px solid #e5e7eb;
        background: #fff;
    }
    
    /* Hide the mobile view (the 'Previous' 'Next' text links on the left) */
    .pagination-container nav > div:first-child {
        display: none !important;
    }

    /* Ensure the desktop view takes full width */
    .pagination-container nav > div:last-child {
        display: flex !important;
        justify-content: space-between;
        width: 100%;
        align-items: center;
    }

    /* The "Showing x to y" text */
    .pagination-container p {
        font-size: 13px;
        color: #6b7280;
        margin: 0;
    }

    /* Pagination Buttons Container (usually a div with shadow in Tailwind) */
    /* We reset the shadow and rounded corners of the container to apply them to buttons instead */
    .pagination-container nav span[class*="shadow-sm"],
    .pagination-container nav div[class*="shadow-sm"] {
        box-shadow: none !important;
        display: inline-flex;
        gap: 4px; /* Space between buttons */
    }

    /* Common Button Styles (Links, Disabled Spans, Active Spans) */
    .pagination-container nav a, 
    .pagination-container nav span[aria-disabled], 
    .pagination-container nav span[aria-current="page"] > span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 !important; /* Remove padding to enforce size */
        border: 1px solid #e5e7eb !important;
        border-radius: 8px !important;
        background: #fff;
        color: #374151;
        font-size: 13px;
        font-weight: 500;
        text-decoration: none;
        width: 36px !important; /* Fixed width */
        height: 36px !important; /* Fixed height */
        margin: 0 !important; /* Reset margin */
        cursor: pointer;
        box-sizing: border-box !important;
    }

    /* Active Page Styles */
    .pagination-container nav span[aria-current="page"] > span {
        background-color: #490d59 !important;
        border-color: #490d59 !important;
        color: white !important;
    }

    /* Disabled State (Previous/Next arrows when inactive) */
    .pagination-container nav span[aria-disabled] {
        opacity: 0.6;
        cursor: not-allowed;
        background: #f9fafb;
    }

    /* Hover State for Links */
    .pagination-container nav a:hover {
        background-color: #f9fafb;
        border-color: #d1d5db !important;
        color: #111827;
    }
    
    /* Fix for arrows (SVG alignment) */
    .pagination-container nav svg {
        width: 16px;
        height: 16px;
    }
</style>
@endpush

