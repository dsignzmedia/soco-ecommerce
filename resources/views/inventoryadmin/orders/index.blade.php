@extends('inventoryadmin.layouts.base')

@section('title', 'Orders | Inventory Admin')
@section('page_heading', 'Orders List')
@section('page_subheading', 'Manage warehouse fulfillment and dispatch')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; vertical-align: middle; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; background-color: #f9fafb; font-weight: 600; }
        td small { color:#6b7280; display:block; font-size: 12px; margin-top: 2px; }
        tr:hover td { background-color: #f9fafb; }
        
        /* Unified Filter Styling */
        .filters { 
            display: grid; 
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
            gap: 16px; 
            margin-bottom: 24px; 
            align-items: start;
        }
        
        .filters input, .filters .date-group input { 
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
        }
        .filters input:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }

        .filters .date-group { width: 100%; display: block; }

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

        /* Pagination Containers and Buttons */
        .pagination-container {
            padding: 12px 20px;
            border-top: 1px solid #e5e7eb;
            background: #fff;
        }
        .pagination-container nav > div:first-child { display: none !important; } /* Hide mobile text */
        .pagination-container nav > div:last-child {
            display: flex !important;
            justify-content: space-between;
            width: 100%;
            align-items: center;
        }
        .pagination-container p { font-size: 13px; color: #6b7280; margin: 0; }
        .pagination-container nav span[class*="shadow-sm"],
        .pagination-container nav div[class*="shadow-sm"] {
            box-shadow: none !important;
            display: inline-flex;
            gap: 4px;
        }
        .pagination-container nav a, 
        .pagination-container nav span[aria-disabled], 
        .pagination-container nav span[aria-current="page"] > span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 !important;
            border: 1px solid #e5e7eb !important;
            border-radius: 8px !important;
            background: #fff;
            color: #374151;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            width: 36px !important;
            height: 36px !important;
            margin: 0 !important;
            cursor: pointer;
            box-sizing: border-box !important;
        }
        .pagination-container nav span[aria-current="page"] > span {
            background-color: #490d59 !important;
            border-color: #490d59 !important;
            color: white !important;
        }
        .pagination-container nav span[aria-disabled] {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f9fafb;
        }
        .pagination-container nav a:hover {
            background-color: #f3e8f5;
            border-color: #490d59 !important;
            color: #490d59;
        }
        .pagination-container nav svg { width: 16px; height: 16px; }

        .status-pill { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; text-transform:capitalize; display: inline-block; }
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
            <select name="order_status">
                <option value="">Order Status</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['order_status'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <input type="text" name="order_number" placeholder="Order #" value="{{ $filters['order_number'] ?? '' }}">
            
            <div class="date-group">
                <input type="text" 
                    onfocus="(this.type='date')" 
                    onblur="(this.value ? this.type='date' : this.type='text')"
                    name="date_from" 
                    placeholder="Start Date"
                    value="{{ $filters['date_from'] ?? '' }}">
            </div>
            
            <div class="date-group">
                <input type="text" 
                    onfocus="(this.type='date')" 
                    onblur="(this.value ? this.type='date' : this.type='text')"
                    name="date_to" 
                    placeholder="End Date"
                    value="{{ $filters['date_to'] ?? '' }}">
            </div>

            <button type="submit">Apply Filter</button>
            <a class="reset" href="{{ route('inventory.admin.orders.index') }}">Reset</a>
        </form>
    </section>

    <div class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
        <div style="overflow-x:auto;">
            <table>
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Customer / Address</th>
                        <th>School / Grade</th>
                        <th>Item / Qty</th>
                        <th>Status</th>
                        <th>Tracking</th>
                        <th style="text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <strong style="color:#490d59;">{{ $order->order_number }}</strong>
                            </td>
                            <td>{{ optional($order->order_date)->format('d M Y') }}</td>
                            <td>
                                <div style="font-weight:500;">{{ $order->customer_name }}</div>
                                <small>{{ Str::limit($order->customer_address, 40) }}</small>
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $order->school?->name ?? '—' }}</div>
                                <small>Grade {{ $order->grade }}</small>
                            </td>
                            <td>
                                <div style="font-weight:500;">{{ $order->item_name }}</div>
                                <small>Size: {{ $order->size }} | Qty: {{ $order->quantity }}</small>
                            </td>
                            <td>
                                <form action="{{ route('inventory.admin.orders.status', $order) }}" method="POST">
                                    @csrf
                                    <select name="order_status" class="no-tom" onchange="this.form.submit()" style="
                                        padding: 6px 10px;
                                        border-radius: 6px;
                                        font-size: 11px;
                                        font-weight: 700;
                                        color: white;
                                        border: none;
                                        cursor: pointer;
                                        width: 100%;
                                        text-transform: uppercase;
                                        background-color: 
                                            @if($order->order_status == 'order_placed') #6b7280
                                            @elseif($order->order_status == 'processing') #22c55e
                                            @elseif($order->order_status == 'packed') #3b82f6
                                            @elseif($order->order_status == 'shipped') #0ea5e9
                                            @elseif($order->order_status == 'delivered') #10b981
                                            @else #6b7280 @endif;
                                    ">
                                        @foreach([
                                            'order_placed' => 'ORDER PLACED',
                                            'processing' => 'PROCESSING',
                                            'packed' => 'PACKED',
                                            'shipped' => 'SHIPPED',
                                            'delivered' => 'DELIVERED'
                                        ] as $value => $label)
                                            <option value="{{ $value }}" @selected($order->order_status == $value) style="background-color: white; color: black;">{{ $label }}</option>
                                        @endforeach
                                    </select>
                                </form>
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
                                <div style="display:flex; justify-content:flex-end; gap:8px;">
                                    <a href="{{ route('inventory.admin.orders.show', $order) }}" class="btn-vs-sm">Manage</a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align:center; padding: 40px; color: #6b7280;">
                                <div style="margin-bottom: 8px; font-size: 24px; color: #d1d5db;"><i class="fas fa-search"></i></div>
                                No orders found matching your filters.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            {{ $orders->links() }}
        </div>
    </div>
@endsection
