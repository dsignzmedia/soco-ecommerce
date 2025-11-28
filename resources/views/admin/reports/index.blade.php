@extends('admin.layouts.base')

@section('title', 'Reports & Analytics | The Skool Store')
@section('page_heading', 'Reports & Analytics')
@section('page_subheading', 'Central place for master admin to drill into orders, revenue, school/grade/category performance and returns.')

@push('styles')
    <style>
        .report-grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:16px; }
        .report-card { border:1px solid rgba(15,23,42,0.06);border-radius:16px;padding:18px; }
        .report-card h4 { margin:0 0 8px;color:#111827;font-size:16px; }
        .report-card p { margin:0;color:#475467;font-size:13px; }
        .filters { display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; }
        .filters button, .filters a.reset { border-radius:12px;font-weight:600;text-align:center; }
        .filters button { border:none;background:#490d59;color:#fff;padding:10px 16px; }
        .filters a.reset { border:1px solid #d0d5dd;color:#475467;padding:10px 16px; }
        table { width:100%;border-collapse:collapse; }
        th,td { padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13px; }
        th { text-transform:uppercase;letter-spacing:.05em;color:#111827;font-size:12px; }
        .export-links a { border:1px solid #d0d5dd;border-radius:999px;padding:8px 14px;font-size:13px;font-weight:600;color:#490d59; }
    </style>
@endpush

@section('content')
    <section class="card" style="margin-bottom:16px;">
        <form method="GET" class="filters">
            <select name="school_id">
                <option value="">School</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <input type="text" name="grade" placeholder="Grade (All)" value="{{ $filters['grade'] ?? '' }}">
            <select name="category">
                <option value="">Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}">
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}">
            <input type="text" name="product_name" placeholder="Product name" value="{{ $filters['product_name'] ?? '' }}">
            <select name="status">
                <option value="">Status</option>
                @foreach(['processing','shipped','delivered','returned','cancelled'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <button type="submit">Apply filters</button>
            <a class="reset" href="{{ route('master.admin.reports.index') }}">Reset</a>
        </form>
    </section>

    <section class="card" style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
            <h3 style="margin:0;color:#111827;">Report types</h3>
            <div class="export-links" style="display:flex;gap:8px;">
                <a href="{{ route('master.admin.reports.export', ['type' => 'csv'] + request()->query()) }}">Export CSV</a>
                <a href="{{ route('master.admin.reports.export', ['type' => 'excel'] + request()->query()) }}">Export Excel</a>
                <a href="{{ route('master.admin.reports.export', ['type' => 'pdf'] + request()->query()) }}">Export PDF</a>
            </div>
        </div>
        <div class="report-grid">
            @foreach($reportTypes as $report)
                <article class="report-card">
                    <h4>{{ $report['label'] }}</h4>
                    <p>{{ $report['description'] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section class="card">
        <h3 style="margin:0 0 12px;color:#111827;">Latest orders snapshot</h3>
        <table>
            <thead>
                <tr>
                    <th>Order</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Product</th>
                    <th>Qty</th>
                    <th>Amount</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>{{ $order->order_number }}</td>
                        <td>{{ $order->school?->name ?? '—' }}</td>
                        <td>{{ $order->grade ?? '—' }}</td>
                        <td>{{ $order->item_name }}</td>
                        <td>{{ $order->quantity }}</td>
                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                        <td>{{ ucfirst($order->order_status) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </section>
@endsection

