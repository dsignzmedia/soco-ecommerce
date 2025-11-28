@extends('admin.layouts.base')

@section('title', 'Inventory List | The Skool Store')
@section('page_heading', 'Inventory List')
@section('page_subheading', 'Master view. Inventory admins can be scoped by school or category later.')

@push('styles')
    <style>
        table { width:100%;border-collapse:collapse; }
        th,td { padding:12px 14px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13px; }
        th { text-transform:uppercase;letter-spacing:.05em;color:#111827;font-size:12px; }
        .filters { display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px; }
        .filters button, .filters a.reset { border-radius:12px;font-weight:600;text-align:center; }
        .filters button { border:none;background:#490d59;color:#fff;padding:10px 16px; }
        .filters a.reset { border:1px solid #d0d5dd;color:#475467;padding:10px 16px; }
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
            <select name="category">
                <option value="">Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" placeholder="Search product" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Filter</button>
            <a class="reset" href="{{ route('master.admin.inventory.list') }}">Reset</a>
        </form>
    </section>

    <section class="card" style="overflow-x:auto;">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Category</th>
                    <th>Current stock</th>
                    <th>Status</th>
                    <th>Last updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                    <tr>
                        <td>{{ $product->product_name }}</td>
                        <td>{{ $product->school?->name ?? '—' }}</td>
                        <td>{{ $product->grade?->name ?? 'All' }}</td>
                        <td>{{ $product->category ?? '—' }}</td>
                        <td>{{ $product->inventory_stock }}</td>
                        <td>{{ ucfirst($product->status) }}</td>
                        <td>{{ optional($product->updated_at)->diffForHumans() }}</td>
                        <td><a href="{{ route('master.admin.inventory.adjust', $product) }}" style="color:#490d59;font-weight:600;">Adjust stock</a></td>
                    </tr>
                @empty
                    <tr><td colspan="8">No products available.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $products->links() }}
        </div>
    </section>
@endsection

