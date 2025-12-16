@extends('inventoryadmin.layouts.base')

@section('title', 'Inventory Admin Dashboard | The Skool Store')
@section('page_heading', 'Inventory Control Room')
@section('page_subheading', 'Live stock pulse for delegated admins')

@section('content')
    <div class="filters-card">
        <h3 class="filters-title">Filters</h3>
        <form method="GET" action="{{ route('inventory.admin.dashboard') }}" class="filter-form-grid">
            <div class="filter-row">
                <select name="school_id">
                    <option value="">School (All)</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>

                <div class="date-input-wrapper">
                    <input type="date" 
                        onclick="this.showPicker()" 
                        name="date_from" 
                        class="filter-input-rounded" 
                        placeholder="Start Date" 
                        value="{{ $filters['date_from'] ?? '' }}">
                </div>

                <div class="date-input-wrapper">
                    <input type="date" 
                        onclick="this.showPicker()" 
                        name="date_to" 
                        class="filter-input-rounded" 
                        placeholder="End Date" 
                        value="{{ $filters['date_to'] ?? '' }}">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-purple-solid" style="flex: 1;">Apply</button>
                    <button type="button" onclick="window.location.href='{{ route('inventory.admin.dashboard') }}'" class="btn-reset" style="flex: 1;">Reset</button>
                </div>
            </div>
        </form>
    </div>

    <style>
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05); /* Subtle shadow per image */
            margin-bottom: 24px;
        }
        .filters-title {
            font-size: 16px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 16px;
        }
        .filter-form-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }
        .filter-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 12px;
        }
        .filter-input-rounded {
            width: 100%;
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px; /* Standard rounded box */
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px; /* Match Select height */
            font-family: inherit;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1); /* Consistent focus ring */
        }
        .btn-purple-solid {
            background-color: #4c1d95; /* Deep purple */
            color: #ffffff;
            border: none;
            border-radius: 12px; /* Standard rounded box */
            padding: 0 24px;
            height: 46px; /* Match Select height */
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: background 0.2s;
            width: 100%; /* Fill grid cell */
        }
        .btn-purple-solid:hover {
            background-color: #3b0a48;
        }
        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
            padding: 0 24px;
            height: 46px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            transition: all 0.2s;
        }
        .btn-reset:hover {
            border-color: #d0d5dd;
            color: #0f172a;
            background: #f8fafc;
        }
    </style>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
        <!-- Order Processing Metrics -->
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">
                @if(!empty($filters['date_from']) || !empty($filters['date_to']))
                    Orders in Range
                @else
                    Orders to process today
                @endif
            </p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#0f172a;">{{ number_format($ordersToday) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Pending picking</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#f59e0b;">{{ number_format($pendingPicking) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Pending packing</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#3b82f6;">{{ number_format($pendingPacking) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Pending shipment</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#10b981;">{{ number_format($pendingShipment) }}</h3>
        </div>
        
        <!-- Alerts & Stock -->
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Delayed orders</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#ef4444;">{{ number_format($delayedOrders) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Low stock</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#f97316;">{{ number_format($lowStock) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Out of stock</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#b91c1c;">{{ number_format($outOfStock) }}</h3>
        </div>
        
        <!-- Scope Metrics -->
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Active Schools</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#64748b;">{{ number_format($schoolsWithOrders) }}</h3>
        </div>
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Active Grades</p>
            <h3 style="margin:6px 0 0;font-size:32px;color:#64748b;">{{ number_format($gradesWithOrders) }}</h3>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card" style="display:flex;align-items:center;justify-content:space-between;">
        <div>
            <h4 style="margin:0;color:#0f172a;">Quick Actions</h4>
            <p style="margin:4px 0 0;color:#94a3b8;font-size:13px;">Common tasks for warehouse operations</p>
        </div>
        <a href="{{ route('inventory.admin.orders.index', ['date_from' => today()->toDateString()]) }}" 
           style="display:inline-flex;align-items:center;gap:8px;background:#4f46e5;color:#fff;padding:10px 20px;border-radius:8px;font-weight:500;transition:background 0.2s;">
            <span>View Today's Orders</span>
            <span style="font-size:18px;">→</span>
        </a>
    </div>
@endsection

