@extends('admin.layouts.base')

@section('title', 'Inventory Reports | The Skool Store')
@section('page_heading', 'Inventory Reports')
@section('page_subheading', 'Low stock, out of stock, breakdowns, and movement log')

@push('styles')
    <style>
        table { width:100%;border-collapse:collapse; }
        th,td { padding:10px 12px;border-bottom:1px solid #e5e7eb;text-align:left;font-size:13px; }
        th { text-transform:uppercase;letter-spacing:.05em;color:#111827;font-size:12px; }
        .card h4 { margin:0 0 12px;color:#111827; }
        .grid { display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px; }

        /* Filter Styles from Returns & Exchanges */
        .filters-card {
            background: #ffffff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 24px;
            border: 1px solid #e5e7eb;
        }
        .filter-form-grid {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }
        .filter-input-rounded {
            padding: 10px 16px;
            border: 1px solid #e5e7eb;
            border-radius: 12px;
            font-size: 14px;
            color: #374151;
            outline: none;
            background-color: #fff;
            height: 46px;
            font-family: inherit;
            min-width: 150px;
            flex: 1;
        }
        .filter-input-rounded:focus {
            border-color: #490d59;
            box-shadow: 0 0 0 4px rgba(73, 13, 89, 0.1);
        }
        .btn-filter, .btn-reset {
            height: 46px;
            padding: 0 24px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            text-decoration: none;
        }
        .btn-filter {
            background-color: #490d59;
            color: #ffffff;
            border: none;
        }
        .btn-filter:hover { background-color: #3b0a48; }
        .btn-reset {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            color: #475467;
        }
        .btn-reset:hover {
            border-color: #d0d5dd;
            color: #0f172a;
            background: #f8fafc;
        }
    </style>
@endpush

@section('content')
    <div style="display:flex;justify-content:flex-end;align-items:center;margin-bottom:24px;">
        <a href="{{ route('master.admin.inventory.dashboard') }}" class="btn-back-outline" style="padding: 8px 16px; border: 1px solid #d1d5db; border-radius: 8px; text-decoration: none; color: #374151; display: inline-flex; align-items: center; gap: 6px; font-weight: 500; background: white;">
            <i class="fas fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Filters -->
    <div class="filters-card">
        <form method="GET" class="filter-form-grid">
            <div style="flex:1; min-width: 200px;">
                <select name="school_id" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Schools</option>
                    @foreach($schools as $school)
                        <option value="{{ $school->id }}" @selected(($filters['school_id'] ?? '') == $school->id)>{{ $school->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="flex:1; min-width: 200px;">
                <select name="category" class="filter-input-rounded no-tom" style="width:100%;">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category }}" @selected(($filters['category'] ?? '') === $category)>{{ $category }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn-filter">Apply Filters</button>
            <a href="{{ route('master.admin.inventory.reports') }}" class="btn-reset">Reset</a>
        </form>
    </div>

    <section class="grid">
        <div class="card">
            <h4>Low stock</h4>
            <table>
                <thead><tr><th>Product</th><th>Stock</th><th>Alert @</th></tr></thead>
                <tbody>
                    @forelse($lowStock as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->inventory_stock }}</td>
                            <td>{{ $product->low_stock_threshold }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="3">All good!</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card">
            <h4>Out of stock</h4>
            <table>
                <thead><tr><th>Product</th><th>School</th></tr></thead>
                <tbody>
                    @forelse($outOfStock as $product)
                        <tr>
                            <td>{{ $product->product_name }}</td>
                            <td>{{ $product->school?->name ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2">No SKUs are fully depleted.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="grid" style="margin-top:16px;">
        <div class="card">
            <h4>Stock by school</h4>
            <table>
                <thead><tr><th>School</th><th>Units</th></tr></thead>
                <tbody>
                    @forelse($stockBySchool as $row)
                        <tr>
                            <td>{{ $row->school?->name ?? 'Unassigned' }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2">No schools yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card">
            <h4>Stock by category</h4>
            <table>
                <thead><tr><th>Category</th><th>Units</th></tr></thead>
                <tbody>
                    @forelse($stockByCategory as $row)
                        <tr>
                            <td>{{ $row->category ?? 'Unassigned' }}</td>
                            <td>{{ $row->total }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="2">No categories yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="card" style="margin-top:16px;">
        <h4>Stock aging buckets</h4>
        <table>
            <thead><tr><th>Bucket</th><th>Units</th></tr></thead>
            <tbody>
                @foreach($agingBuckets as $label => $value)
                    <tr>
                        <td>{{ $label }}</td>
                        <td>{{ $value }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </section>

    <section class="card" style="margin-top:16px;">
        <h4>Stock movement (last adjustments)</h4>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Change</th>
                    <th>Reason</th>
                    <th>Comment</th>
                </tr>
            </thead>
            <tbody>
                @forelse($movements as $movement)
                    <tr>
                        <td>{{ $movement->created_at->format('d M Y H:i') }}</td>
                        <td>{{ $movement->product?->product_name }}</td>
                        <td>{{ $movement->quantity_change > 0 ? '+' : '' }}{{ $movement->quantity_change }}</td>
                        <td>{{ ucfirst($movement->reason) }}</td>
                        <td>{{ $movement->comment ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5">No movement recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div style="margin-top:12px;">
            {{ $movements->links() }}
        </div>
    </section>
@endsection

