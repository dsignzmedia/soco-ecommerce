@extends('admin.layouts.base')

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

        .btn-vs-sm { padding: 6px 12px; font-size: 12px; border-radius: 6px; text-decoration: none; border: 1px solid #d0d5dd; background: white; color: #490d59; font-weight: 500; display: inline-flex; align-items: center; gap: 4px; transition: all 0.2s; }
        .btn-vs-sm:hover { background-color: #490d59; border-color: #490d59; text-decoration: none; color: #490d59; transform: translateY(-1px); box-shadow: 0 2px 4px rgba(73, 13, 89, 0.15); }
        
        /* Delete button styling */
        .btn-delete { background: #fef2f2 !important; color: #dc2626 !important; border: 1px solid #fecaca !important; }
        .btn-delete:hover { background: #fee2e2 !important; border-color: #fca5a5 !important; color: #b91c1c !important; }
        
        /* Tablet Responsive Styles (768px - 1024px) */
        @media (min-width: 768px) and (max-width: 1024px) {
            .filters {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
            
            .filters input,
            .filters select,
            .filters .date-group input,
            .filters button,
            .filters a.reset {
                height: 42px !important;
                font-size: 13px;
            }
            
            table {
                font-size: 12px;
            }
            
            th, td {
                padding: 10px 8px;
                font-size: 12px;
            }
            
            th {
                font-size: 11px;
            }
            
            .card {
                padding: 18px;
            }
            
            /* Hide less important columns on tablet */
            th:nth-child(5), /* Grade */
            td:nth-child(5),
            th:nth-child(6), /* Category */
            td:nth-child(6) {
                display: none;
            }
        }
        
        /* Mobile Responsive Styles */
        @media (max-width: 767px) {
            .filters {
                grid-template-columns: 1fr;
            }
            
            table {
                display: block;
                overflow-x: auto;
            }
        }
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom: 16px;">
            <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Order Filters</h3>
        </div>
        <form class="filters" method="GET">
            <select name="school_id">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade">
                <option value="">All Grades</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade }}" @selected(($filters['grade'] ?? '') === $grade)>{{ $grade }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="product_type">
                <option value="">All Product Types</option>
                @foreach($productTypes as $type)
                     @php
                        // Format cleaner labels if possible
                        $label = $type;
                        if($type === 'merchandised') $label = 'Merchandise';
                        if($type === 'b2s') $label = 'Back To School';
                    @endphp
                    <option value="{{ $type }}" @selected(($filters['product_type'] ?? '') === $type)>{{ ucfirst($label) }}</option>
                @endforeach
            </select>
            <select name="order_status" class="no-sort">
                <option value="">Order Status</option>
                {{-- Order statuses in workflow sequence: Order Placed -> Processing -> Packed -> Shipped -> Delivered --}}
                @foreach(['order_placed' => 'Order Placed', 'processing' => 'Processing', 'packed' => 'Packed', 'shipped' => 'Shipped', 'delivered' => 'Delivered'] as $value => $label)
                    <option value="{{ $value }}" @selected(($filters['order_status'] ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="payment_status">
                <option value="">Payment Status</option>
                @foreach(['pending','paid','failed','refunded'] as $status)
                    <option value="{{ $status }}" @selected(($filters['payment_status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
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

            <div style="grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px;">
                <button type="submit" style="width: auto; min-width: 120px;">Apply Filter</button>
                <a href="{{ route('master.admin.orders.index') }}" class="reset" style="width: auto; min-width: 100px;">Reset</a>
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
                                <a href="{{ route('master.admin.orders.show', $order) }}" style="color:#490d59; font-weight:600; text-decoration:none;">{{ $order->order_number }}</a>
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
                                <form action="{{ route('master.admin.orders.status', $order) }}" method="POST">
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
                                            'delivered' => 'Delivered'
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
                                    <a href="{{ route('master.admin.orders.show', $order) }}" class="btn-vs-sm" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('master.admin.orders.invoice', $order) }}" class="btn-vs-sm" title="View Invoice">
                                        <i class="fas fa-file-invoice"></i>
                                    </a>
                                    <a href="{{ route('master.admin.orders.invoice.download', $order) }}" class="btn-vs-sm" title="Download Invoice">
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
        padding: 20px;
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

    /* Pagination Styling - Enhanced (Same as Inventory) */
    .pagination {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        flex-wrap: wrap;
        gap: 8px;
        margin-top: 0;
        padding: 0;
        list-style: none;
        margin-left: 0;
        margin-right: 0;
    }
    
    .pagination > * {
        margin: 0 !important;
    }
    
    /* All pagination links and spans */
    .pagination-container nav a, 
    .pagination-container nav span[aria-disabled], 
    .pagination-container nav span[aria-current="page"] > span,
    .pagination a,
    .pagination span,
    .pagination .page-link {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 42px !important;
        height: 42px !important;
        padding: 0 14px !important;
        border-radius: 8px !important;
        text-decoration: none !important;
        font-size: 14px !important;
        font-weight: 500 !important;
        transition: all 0.2s ease !important;
        border: 1px solid #e5e7eb !important;
        background-color: #ffffff !important;
        color: #6b7280 !important;
        margin: 0 2px !important;
        cursor: pointer;
        box-sizing: border-box !important;
    }
    
    .pagination-container nav a:hover,
    .pagination a:hover,
    .pagination .page-link:hover {
        background-color: #f9fafb !important;
        border-color: #490d59 !important;
        color: #490d59 !important;
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(73, 13, 89, 0.15) !important;
    }

    /* Active Page Styles */
    .pagination-container nav span[aria-current="page"] > span,
    .pagination span[aria-current="page"],
    .pagination .active span,
    .pagination .page-item.active .page-link,
    .pagination .page-link.active {
        background-color: #490d59 !important;
        border-color: #490d59 !important;
        color: #ffffff !important;
        font-weight: 600 !important;
    }

    /* Disabled State (Previous/Next arrows when inactive) */
    .pagination-container nav span[aria-disabled],
    .pagination span[aria-disabled="true"],
    .pagination .page-item.disabled .page-link,                                                                                                                 
    .pagination .page-link.disabled {                   
        background-color: #f3f4f6 !important;       
        color: #9ca3af !important;      
        cursor: not-allowed !important;         
        opacity: 0.6 !important;            
        pointer-events: none;               
    }               
    
    /* Previous/Next buttons */
    .pagination-container nav a[rel="prev"],
    .pagination-container nav a[rel="next"],
    .pagination a[rel="prev"],
    .pagination a[rel="next"],
    .pagination .page-link[rel="prev"],
    .pagination .page-link[rel="next"] {
        background-color: #ffffff !important;
        border: 1px solid #d1d5db !important;
        color: #490d59 !important;
        font-weight: 600 !important;
        padding: 0 16px !important;
        min-width: auto !important;
    }
    
    .pagination-container nav a[rel="prev"]:hover,
    .pagination-container nav a[rel="next"]:hover,
    .pagination a[rel="prev"]:hover,
    .pagination a[rel="next"]:hover {
        background-color: #490d59 !important;
        color: #ffffff !important;
        border-color: #490d59 !important;
    }
    
    /* Fix for arrows (SVG alignment) */
    .pagination-container nav svg,
    .pagination svg {
        width: 16px;
        height: 16px;
    }
    
    /* Responsive pagination */
    @media (max-width: 768px) {
        .pagination {
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }
        
        .pagination a,
        .pagination span,
        .pagination .page-link,
        .pagination-container nav a,
        .pagination-container nav span {
            min-width: 38px !important;
            height: 38px !important;
            font-size: 13px !important;
            padding: 0 10px !important;
        }
    }
</style>
@endpush

