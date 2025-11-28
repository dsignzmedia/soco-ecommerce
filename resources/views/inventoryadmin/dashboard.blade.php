@extends('inventoryadmin.layouts.base')

@section('title', 'Inventory Admin Dashboard | The Skool Store')
@section('page_heading', 'Inventory Control Room')
@section('page_subheading', 'Live stock pulse for delegated admins')

@section('content')
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:16px;margin-bottom:24px;">
        <!-- Order Processing Metrics -->
        <div class="card">
            <p style="margin:0;font-size:13px;color:#94a3b8;">Orders to process today</p>
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

