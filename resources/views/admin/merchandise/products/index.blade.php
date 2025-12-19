@extends('admin.layouts.merchandise')

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
    <section class="card" style="margin-bottom:20px;">
        <h3 style="margin:0 0 16px;color:#111827;">Filters</h3>
        <form class="filters" method="GET">
            <select name="school_id">
                <option value="">School (All)</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                @endforeach
            </select>
            <select name="grade">
                <option value="">Grade (All)</option>
                @foreach($grades as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['grade'] ?? '') == $key)>{{ $label }}</option>
                @endforeach
            </select>
            <select name="category">
                <option value="">Category (All)</option>
                @foreach($categories as $key => $label)
                    <option value="{{ $key }}" @selected(($filters['category'] ?? '') === $key)>{{ $label }}</option>
                @endforeach
            </select>

            <select name="stock_status">
                <option value="">Stock status</option>
                <option value="in_stock" @selected(($filters['stock_status'] ?? '') === 'in_stock')>In stock</option>
                <option value="out_of_stock" @selected(($filters['stock_status'] ?? '') === 'out_of_stock')>Out of stock</option>
            </select>
            <select name="status">
                <option value="">Status</option>
                @foreach(['live','draft','archived'] as $status)
                    <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="gender">
                <option value="">Gender</option>
                @foreach(['male','female','unisex'] as $gender)
                    <option value="{{ $gender }}" @selected(($filters['gender'] ?? '') === $gender)>{{ ucfirst($gender) }}</option>
                @endforeach
            </select>
            <input type="text" name="q" placeholder="Product Name / SKU" value="{{ $filters['q'] ?? '' }}">
            <button type="submit">Apply</button>
            <button type="button" onclick="window.location.href='{{ route('admin.merchandise.products.index') }}'" class="btn-reset">Reset</button>
        </form>
    </section>

    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;">
        <div class="export-links" style="display:flex;gap:8px;flex-wrap:wrap;">
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'csv'] + request()->query()) }}">Export CSV</a>
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'excel'] + request()->query()) }}">Export Excel</a>
            <a href="{{ route('admin.merchandise.products.export', ['type' => 'pdf'] + request()->query()) }}">Export PDF</a>
        </div>
        <a href="{{ route('admin.merchandise.products.create') }}" style="padding:10px 20px;border-radius:9999px;background:#490d59;color:#fff;font-weight:600;font-size:14px;text-decoration:none;">+ Add Product</a>
    </div>
    <div class="product-grid-container" style="margin-top: 24px;">
        <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:24px;">
            @forelse($mappings as $mapping)
                <div class="product-card" style="background:#fff; border: 1px solid #eaecf0; border-radius: 16px; padding: 20px; display: flex; flex-direction: column; gap: 16px; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02); transition: transform 0.2s;">
                    <div style="display: flex; gap: 16px; align-items: start;">
                        <div style="flex-shrink: 0; background: #f9fafb; padding: 8px; border-radius: 12px; border: 1px solid #f2f4f7;">
                            @if($mapping->featured_image)
                                <img src="{{ Str::startsWith($mapping->featured_image, 'http') ? $mapping->featured_image : asset('storage/' . $mapping->featured_image) }}" alt="{{ $mapping->product_name }}" style="width:64px;height:64px;object-fit:cover;border-radius:8px;">
                            @else
                                <div style="width:64px;height:64px;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#98a2b3;">
                                    <i class="fas fa-image" style="font-size: 24px;"></i>
                                </div>
                            @endif
                        </div>
                        <div style="flex: 1; min-width: 0;">
                            <h4 style="font-size: 15px; font-weight: 600; margin: 0 0 8px; color: #101828; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                {{ $mapping->product_name }}
                            </h4>
                            <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                                <span class="status-pill status-{{ $mapping->status }}" style="padding: 2px 10px; font-size: 11px;">{{ ucfirst($mapping->status) }}</span>
                                <span style="font-size: 11px; color: #344054; background: #f2f4f7; padding: 2px 10px; border-radius: 999px; border: 1px solid #e4e7ec;">{{ $mapping->grade ?? 'All grades' }}</span>
                            </div>
                        </div>
                    </div>

                    <div style="background: #fcfcfd; border-radius: 12px; padding: 12px; border: 1px solid #f2f4f7; font-size: 13px; color: #475467; display: flex; flex-direction: column; gap: 8px; flex-grow: 1;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Price:</span>
                            <span style="font-weight: 600; color: #101828;">
                                ₹{{ number_format($mapping->price_regular ?? 0, 2) }}
                                @if($mapping->price_sale)
                                    <span style="color:#b42318; margin-left: 4px; font-size: 12px;">(₹{{ number_format($mapping->price_sale, 2) }})</span>
                                @endif
                            </span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>School:</span>
                            <span style="text-align: right; max-width: 60%; color: #344054;">{{ $mapping->school?->name ?? 'General / No School' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>Stock:</span>
                            <span class="{{ $mapping->stock_status === 'in_stock' ? 'text-success' : 'text-danger' }}" style="font-weight: 500;">
                                @if($mapping->variants_count > 0)
                                    Total: {{ $mapping->inventory_stock }} (Variants)
                                @else
                                    {{ $mapping->inventory_stock }} ({{ $mapping->stock_status === 'in_stock' ? 'In' : 'Out' }})
                                @endif
                            </span>
                        </div>
                    </div>

                    <div style="padding-top: 4px; display: flex; gap: 10px;">
                        <a href="{{ route('admin.merchandise.products.edit', $mapping) }}" class="btn-vs-sm" style="flex: 1; text-align: center; justify-content: center; background: #490d59; color: white !important; border-radius: 8px; padding: 8px; font-weight: 500; font-size: 13px; border: none;">Edit</a>
                        <a href="{{ route('admin.merchandise.products.show', $mapping) }}" class="btn-vs-sm" style="background:#fff; color:#344054; padding: 8px 12px; border: 1px solid #d0d5dd; border-radius: 8px;">
                            <i class="fas fa-eye"></i>
                        </a>
                        <form method="POST" action="{{ route('admin.merchandise.products.destroy', $mapping) }}" onsubmit="return confirm('Delete this product?');" style="margin:0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn-vs-sm" style="background:#fff; color:#b42318; border: 1px solid #fedf89; padding: 8px 12px; border-radius: 8px;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: #667085; background: #fff; border-radius: 16px; border: 1px dashed #d0d5dd;">
                    <div style="background: #f2f4f7; width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <i class="fas fa-box-open" style="font-size: 20px; color: #667085;"></i>
                    </div>
                    <h3 style="font-size: 16px; font-weight: 600; color: #101828; margin-bottom: 4px;">No products found</h3>
                    <p style="font-size: 14px; margin-bottom: 24px;">Your search did not return any results. Try different filters.</p>
                    <a href="{{ route('admin.merchandise.products.index') }}" class="btn-reset">Clear all filters</a>
                </div>
            @endforelse
        </div>
        <div style="margin-top: 24px;">
            {{ $mappings->links() }}
        </div>
    </div>
@endsection

