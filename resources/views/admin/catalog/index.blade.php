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
            border-radius: 9999px;
            font-weight: 600;
            text-align: center;
        }
        .filters button {
            border: none;
            background: #490d59;
            color: #fff;
            padding: 6px 14px;
            font-size: 12px;
        }
        .filters a.reset {
            border: 1.5px solid #d0d5dd;
            color: #475467;
            padding: 5px 14px;
            font-size: 12px;
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
        <form method="GET" class="filters" style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;">
            <select name="school_id" style="flex:1;min-width:140px;">
                <option value="">All schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade" style="flex:1;min-width:120px;">
                <option value="">All grades</option>
                @foreach($grades as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['grade'] ?? '') == $key)>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            <select name="category" style="width:auto;">
                <option value="">Category</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['category'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="product_type" style="width:auto;">
                <option value="">Product type</option>
                @foreach($productTypes as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['product_type'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="gender" style="width:auto;">
                <option value="">Any gender</option>
                @foreach(['boys','girls','unisex'] as $gender)
                    <option value="{{ $gender }}" @selected(($filters['gender'] ?? '') === $gender)>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>
            <select name="status" style="width:auto;">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="stock_status" style="width:auto;">
                <option value="">Stock</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <input type="text" name="q" placeholder="Search name/type" value="{{ $filters['q'] ?? '' }}" style="flex:2;min-width:200px;">
            <button type="submit" style="height:42px;padding:0 24px;background:#490d59;color:#fff;border:none;border-radius:12px;font-weight:500;">Apply filters</button>
            <a class="reset" href="{{ route('master.admin.catalog.index') }}" style="height:42px;padding:0 16px;display:inline-flex;align-items:center;background:#fff;border:1px solid #d0d5dd;color:#344054;border-radius:12px;font-weight:500;text-decoration:none;">Reset</a>
        </form>
        <div style="display:flex;justify-content:space-between;align-items:center;">
            <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
                <a href="{{ route('master.admin.catalog.export', ['type' => 'csv'] + request()->query()) }}">Export CSV</a>
                <a href="{{ route('master.admin.catalog.export', ['type' => 'excel'] + request()->query()) }}">Export Excel</a>
                <a href="{{ route('master.admin.catalog.export', ['type' => 'pdf'] + request()->query()) }}">Export PDF</a>
            </div>
            <a href="{{ route('master.admin.catalog.create') }}" style="padding:8px 16px;border-radius:9999px;background:#490d59;color:#fff;font-weight:600;font-size:13px;">+ Add Product</a>
        </div>
    </div>
    <div class="card">
        <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px; margin-top:15px !important">
            @forelse($mappings as $mapping)
                <div class="card p-3 h-100" style="border: 1px solid #e5e7eb; border-radius: 12px; display: flex; flex-direction: column; gap: 8px; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                    <div style="display: flex; gap: 12px; align-items: start; margin-bottom: 8px;">
                        <div style="flex-shrink: 0;">
                            @if($mapping->featured_image)
                                <img src="{{ Str::startsWith($mapping->featured_image, 'http') ? $mapping->featured_image : asset('storage/' . $mapping->featured_image) }}" alt="{{ $mapping->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            @else
                                <div style="width:64px;height:64px;border-radius:8px;background:#f2f4f7;display:flex;align-items:center;justify-content:center;color:#98a2b3;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </div>
                        <div>
                            <h4 style="font-size: 14px; font-weight: 600; margin: 0 0 4px; color: #111827; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $mapping->product_name }}
                            </h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 4px;">
                                <span class="status-pill status-{{ $mapping->status }}" style="padding: 2px 8px; font-size: 11px;">{{ ucfirst($mapping->status) }}</span>
                                <span style="font-size: 11px; color: #667085; background: #f9fafb; padding: 2px 8px; border-radius: 999px; border: 1px solid #f2f4f7;">{{ $mapping->grade ?? 'All grades' }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="font-size: 13px; color: #475467; display: flex; flex-direction: column; gap: 4px; flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Price:</span>
                            <span style="font-weight: 600; color: #111827;">
                                ₹{{ number_format($mapping->price_regular ?? 0, 2) }}
                                @if($mapping->price_sale)
                                    <span style="color:#b42318; margin-left: 4px;">(₹{{ number_format($mapping->price_sale, 2) }})</span>
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>School:</span>
                            <span style="text-align: right; max-width: 60%;">{{ $mapping->school->name }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Stock:</span>
                            <span class="{{ $mapping->stock_status === 'in_stock' ? 'text-success' : 'text-danger' }}">
                                {{ $mapping->inventory_stock }} ({{ $mapping->stock_status === 'in_stock' ? 'In' : 'Out' }})
                            </span>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #f2f4f7; padding-top: 12px; margin-top: 8px; display: flex; gap: 8px;">
                        <a href="{{ route('master.admin.catalog.edit', $mapping) }}" class="btn-vs-sm" style="flex: 1; text-align: center; justify-content: center;">Edit</a>
                        <a href="{{ route('master.admin.catalog.show', $mapping) }}" class="btn-vs-sm" style="background:#f2f4f7; color:#475467; padding: 6px 10px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('master.admin.catalog.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-vs-sm" style="background:#fef3f2; color:#b42318; border-color:#fee4e2; padding: 6px 10px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #667085;">
                    <i class="fas fa-box-open" style="font-size: 32px; margin-bottom: 12px; color: #d0d5dd;"></i>
                    <p>No products found matching your criteria.</p>
                </div>
            @endforelse
        </div>
        <div style="margin-top:16px;">
            {{ $mappings->links() }}
        </div>
    </div>
@endsection

