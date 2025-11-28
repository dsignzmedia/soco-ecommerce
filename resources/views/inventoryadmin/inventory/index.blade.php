@extends('inventoryadmin.layouts.base')

@section('title', 'Inventory List | Inventory Admin')
@section('page_heading', 'Inventory List')
@section('page_subheading', 'Real-time stock levels across all schools')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 13px; }
        th { text-transform: uppercase; letter-spacing: 0.05em; color:#111827; font-size: 12px; }
        td small { color:#98a2b3; display:block; }
        .filters { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:12px; margin-bottom: 20px; }
        .filters button, .filters a.reset { border-radius:8px; font-weight:600; text-align:center; padding:10px 16px; font-size: 13px; }
        .filters button { border:none; background:#4f46e5; color:#fff; cursor: pointer; }
        .filters a.reset { border:1px solid #d0d5dd; color:#475467; display: inline-block; text-decoration: none; }
        .status-pill { padding:4px 10px; border-radius:999px; font-size:12px; font-weight:600; text-transform:capitalize; display: inline-block; }
        .status-active { background:#f0fdf4; color:#15803d; }
        .status-inactive { background:#fef2f2; color:#b91c1c; }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px;">
        <form class="filters" method="GET">
            <select name="school_id" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="category" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
                <option value="">All Statuses</option>
                <option value="active" @selected(($filters['status'] ?? '') === 'active')>Active</option>
                <option value="inactive" @selected(($filters['status'] ?? '') === 'inactive')>Inactive</option>
            </select>
            <input type="text" name="q" placeholder="Search Product..." value="{{ $filters['q'] ?? '' }}" style="padding:8px;border:1px solid #d0d5dd;border-radius:8px;">
            <button type="submit">Filter</button>
            <a class="reset" href="{{ route('inventory.admin.inventory.index') }}">Reset</a>
        </form>
    </div>

    <div class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Stock</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Last Updated</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>
                            <strong>{{ $product->product_name }}</strong>
                        </td>
                        <td>
                            @if($product->inventory_stock <= 0)
                                <span style="color:#b91c1c;font-weight:600;">Out of Stock</span>
                            @elseif($product->inventory_stock <= ($product->low_stock_threshold ?? 5))
                                <span style="color:#d97706;font-weight:600;">{{ $product->inventory_stock }} (Low)</span>
                            @else
                                <span style="color:#15803d;font-weight:600;">{{ $product->inventory_stock }}</span>
                            @endif
                        </td>
                        <td>{{ $product->school?->name ?? '—' }}</td>
                        <td>{{ $product->grade?->name ?? '—' }}</td>
                        <td>{{ $product->category }}</td>
                        <td>
                            <span class="status-pill status-{{ $product->status }}">
                                {{ ucfirst($product->status) }}
                            </span>
                        </td>
                        <td>
                            {{ optional($product->updated_at)->format('d M Y') }}
                            <small>{{ optional($product->updated_at)->format('h:i A') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('inventory.admin.inventory.adjust', $product) }}" style="color:#4f46e5;font-weight:600;font-size:13px;text-decoration:none;">Adjust Stock</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:24px;color:#94a3b8;">No products found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $products->links() }}
        </div>
    </div>
@endsection
