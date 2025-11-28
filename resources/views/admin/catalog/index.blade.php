@extends('admin.layouts.base')

@section('title', 'Products & Catalog | The Skool Store')
@section('page_heading', 'Products & Catalog')
@section('page_subheading', 'Monitor listings, pricing, inventory and exports')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size:13px; }
        th { font-size: 12px; color:#111827; text-transform:uppercase; letter-spacing:0.05em; }
        td small { color:#98a2b3; display:block; }
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(180px,1fr));
            gap: 12px;
        }
        .filters button, .filters a.reset {
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
        }
        .filters button {
            border: none;
            background: #490d59;
            color: #fff;
            padding: 10px 16px;
        }
        .filters a.reset {
            border: 1px solid #d0d5dd;
            color: #475467;
            padding: 10px 16px;
        }
        .export-links a {
            border: 1px solid #d0d5dd;
            border-radius: 999px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            color: #490d59;
        }
        .status-pill {
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }
        .status-live { background:#ecfdf3;color:#027a48; }
        .status-draft { background:#f2f4f7;color:#475467; }
        .status-archived { background:#fef3f2;color:#b42318; }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:16px;display:flex;flex-direction:column;gap:16px;">
        <form method="GET" class="filters">
            <select name="school_id">
                <option value="">All schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade_id">
                <option value="">All grades</option>
                @foreach($grades as $grade)
                    <option value="{{ $grade->id }}" @selected(($filters['grade_id'] ?? '') == $grade->id)>
                        {{ $grade->name }} ({{ $grade->school?->name }})
                    </option>
                @endforeach
            </select>
            <select name="category">
                <option value="">Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                @endforeach
            </select>
            <select name="product_type">
                <option value="">Product type</option>
                @foreach($productTypes as $type)
                    <option value="{{ $type }}" @selected(($filters['product_type'] ?? '') === $type)>{{ $type }}</option>
                @endforeach
            </select>
            <select name="gender">
                <option value="">Any gender</option>
                @foreach(['boys','girls','unisex'] as $gender)
                    <option value="{{ $gender }}" @selected(($filters['gender'] ?? '') === $gender)>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>
            <select name="status">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="stock_status">
                <option value="">Stock</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <input type="text" name="q" placeholder="Search name/type" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Apply filters</button>
            <a class="reset" href="{{ route('master.admin.catalog.index') }}">Reset</a>
        </form>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('master.admin.catalog.export', ['type' => 'csv'] + request()->query()) }}">Export CSV</a>
                <a href="{{ route('master.admin.catalog.export', ['type' => 'excel'] + request()->query()) }}">Export Excel</a>
                <a href="{{ route('master.admin.catalog.export', ['type' => 'pdf'] + request()->query()) }}">Export PDF</a>
            </div>
            <a href="{{ route('master.admin.catalog.create') }}" style="padding:10px 16px;border-radius:12px;background:#490d59;color:#fff;font-weight:600;">+ Add Product</a>
        </div>
    </div>
    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>School</th>
                    <th>Grade</th>
                    <th>Category / Type</th>
                    <th>Gender</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Stock</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mappings as $mapping)
                    <tr>
                        <td>
                            @if($mapping->featured_image)
                                <img src="{{ $mapping->featured_image }}" alt="{{ $mapping->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:12px;">
                            @else
                                <div style="width:64px;height:64px;border-radius:12px;background:#f2f4f7;display:flex;align-items:center;justify-content:center;color:#98a2b3;">N/A</div>
                            @endif
                        </td>
                        <td>
                            <strong style="color:#111827;">{{ $mapping->product_name }}</strong>
                            <small>{{ $mapping->tag_name ?? $mapping->availability_label }}</small>
                        </td>
                        <td>{{ $mapping->school->name }}</td>
                        <td>{{ $mapping->grade?->name ?? 'All grades' }}</td>
                        <td>
                            {{ $mapping->category ?? '—' }}
                            <small>{{ $mapping->product_type ?? '—' }}</small>
                        </td>
                        <td>{{ ucfirst($mapping->gender) }}</td>
                        <td>
                            ₹{{ number_format($mapping->price_regular ?? 0, 2) }}
                            @if($mapping->price_sale)
                                <small style="color:#b42318;">Sale: ₹{{ number_format($mapping->price_sale, 2) }}</small>
                            @endif
                        </td>
                        <td>
                            <span class="status-pill status-{{ $mapping->status }}">{{ ucfirst($mapping->status) }}</span>
                            <small>{{ $mapping->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}</small>
                        </td>
                        <td>
                            {{ $mapping->inventory_stock }}
                            <small>Alert @ {{ $mapping->low_stock_threshold }}</small>
                        </td>
                        <td style="white-space:nowrap;">
                            <a href="{{ route('master.admin.catalog.show', $mapping) }}" title="View" style="margin-right:8px;color:#111827;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="12" r="3"></circle>
                                    <path d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('master.admin.catalog.edit', $mapping) }}" title="Edit" style="margin-right:8px;color:#111827;">
                                <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 20h9"></path>
                                    <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4 12.5-12.5z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('master.admin.catalog.destroy', $mapping) }}" style="display:inline-block;" onsubmit="return confirm('Delete this product?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="Delete" style="border:none;background:none;color:#b42318;padding:0;">
                                    <svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"></path>
                                        <path d="M10 11v6"></path>
                                        <path d="M14 11v6"></path>
                                        <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                                    </svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="10">No catalog entries yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:16px;">
            {{ $mappings->links() }}
        </div>
    </div>
@endsection

