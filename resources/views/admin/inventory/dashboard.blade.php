@extends('admin.layouts.base')

@section('title', 'Inventory Dashboard | The Skool Store')
@section('page_heading', 'Inventory Dashboard')
@section('page_subheading', 'Master Admin can see everything; delegate trimmed views to inventory admins.')

@push('styles')
    <style>
        .kpi-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px; }
        .kpi { border:1px solid rgba(15,23,42,0.06); border-radius:16px; padding:18px; }
        .kpi span { color:#475467;font-size:13px; }
        .kpi strong { display:block;font-size:32px;color:#111827; }
        table { width:100%; border-collapse:collapse; }
        th,td { padding:10px 12px; border-bottom:1px solid #e5e7eb; text-align:left; font-size:13px; }
        th { text-transform:uppercase; letter-spacing:.05em; font-size:12px; color:#111827; }
    </style>
@endpush

@section('content')
    <section class="card">
        <div class="kpi-grid">
            <div class="kpi">
                <span>Total stock</span>
                <strong>{{ number_format($totalStock) }}</strong>
            </div>
            <div class="kpi">
                <span>In stock SKUs</span>
                <strong>{{ $inStock }}</strong>
            </div>
            <div class="kpi">
                <span>Out of stock SKUs</span>
                <strong>{{ $outOfStock }}</strong>
            </div>
            <div class="kpi">
                <span>Low stock SKUs</span>
                <strong>{{ $lowStock }}</strong>
            </div>
        </div>
    </section>

    <section class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h4 style="margin:0;color:#111827;">Stock aging report</h4>
            <a href="{{ route('master.admin.inventory.reports') }}" style="color:#490d59;font-weight:600;">Open full reports →</a>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Category</th>
                    <th>Stock</th>
                    <th>Days since update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($aging as $row)
                    <tr>
                        <td>{{ $row['product'] }}</td>
                        <td>{{ $row['category'] ?? '—' }}</td>
                        <td>{{ $row['stock'] }}</td>
                        <td>{{ $row['days'] }} days</td>
                    </tr>
                @empty
                    <tr><td colspan="4">No products yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="card" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('master.admin.inventory.list') }}" class="nav__item" style="background:#f8f9ff;color:#490d59;">Go to inventory list</a>
        <a href="{{ route('master.admin.inventory.reports') }}" class="nav__item" style="background:#f8f9ff;color:#490d59;">View reports</a>
    </section>
@endsection

