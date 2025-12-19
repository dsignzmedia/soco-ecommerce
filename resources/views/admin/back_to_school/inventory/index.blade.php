@extends('admin.layouts.back_to_school')

@section('title', 'Inventory | Back To School Admin')
@section('page_heading', 'Inventory Management')
@section('page_subheading', 'Manage stock levels for your products.')

@section('content')
<section class="card" style="margin-bottom:24px; border: 1px solid #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <div style="display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size: 18px; font-weight: 600; color:#111827;">Product Stock</h3>
        <form style="display:flex; gap:10px;" method="GET">
            <input type="text" name="q" placeholder="Search products..." value="{{ request('q') }}" style="width: 250px; height: 40px; padding: 0 12px; border: 1px solid #d1d5db; border-radius: 8px;">
            <button type="submit" class="btn-vs-sm" style="height: 40px; padding: 0 20px; border-radius: 8px; background: #490d59; color: white !important; border:none;">Search</button>
        </form>
    </div>
</section>

<section class="card" style="border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); padding:0;">
    <div style="overflow-x:auto;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Product Name</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Category</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">School</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:center; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Current Stock</th>
                    <th style="padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; font-size: 12px; background-color: #f9fafb; font-weight: 600; text-transform: uppercase;">Update Stock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($products as $product)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 12px 14px; vertical-align: middle;">
                        <div style="font-weight: 500; color: #111827;">{{ $product->product_name }}</div>
                        @if($product->inventory_stock <= 5)
                            <span style="font-size: 11px; color: #b91c1c; background: #fef2f2; padding: 2px 6px; border-radius: 4px; font-weight: 500;">Low Stock</span>
                        @endif
                    </td>
                    <td style="padding: 12px 14px; vertical-align: middle; color: #4b5563;">{{ $product->category }}</td>
                    <td style="padding: 12px 14px; vertical-align: middle; color: #4b5563;">{{ optional($product->school)->name ?? 'All' }}</td>
                    <td style="padding: 12px 14px; vertical-align: middle; text-align:center;">
                        <strong style="font-size: 14px; color: {{ $product->inventory_stock <= 0 ? '#ef4444' : '#10b981' }};">
                            {{ $product->inventory_stock }}
                        </strong>
                    </td>
                    <td style="padding: 12px 14px; vertical-align: middle;">
                        <form action="{{ route('admin.back_to_school.inventory.update', $product) }}" method="POST" style="display:flex; gap:8px; align-items:center;">
                            @csrf
                            @method('PUT')
                            <input type="number" name="inventory_stock" value="{{ $product->inventory_stock }}" min="0" style="width: 100px; padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 14px;">
                            <button type="submit" class="btn-vs-sm" style="margin:0; height: 34px; padding: 0 12px; cursor: pointer;">Save</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center; padding: 40px; color: #6b7280;">
                        No products found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-container p-3">
        {{ $products->links() }}
    </div>
</section>

@push('styles')
<style>
    tr:hover { background-color: #f9fafb; }
    
    /* Reuse pagination styles from orders page */
    .pagination-container nav > div:first-child { display: none; }
    .pagination-container nav > div:last-child { display: flex; justify-content: space-between; width: 100%; align-items: center; }
    .pagination-container nav span[class*="shadow-sm"] { box-shadow: none !important; display: inline-flex; gap: 4px; }
    .pagination-container nav a, .pagination-container nav span[aria-disabled], .pagination-container nav span[aria-current="page"] > span {
        display: inline-flex; align-items: center; justify-content: center; padding: 0 !important;
        border: 1px solid #e5e7eb !important; border-radius: 8px !important; background: #fff;
        color: #374151; font-size: 13px; font-weight: 500; text-decoration: none;
        width: 36px !important; height: 36px !important; margin: 0 !important; cursor: pointer;
    }
    .pagination-container nav span[aria-current="page"] > span {
        background-color: #490d59 !important; border-color: #490d59 !important; color: white !important;
    }
</style>
@endpush
@endsection
