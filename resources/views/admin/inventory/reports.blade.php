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
    </style>
@endpush

@section('content')
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

