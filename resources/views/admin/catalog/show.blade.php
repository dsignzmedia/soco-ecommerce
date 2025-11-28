@extends('admin.layouts.base')

@section('title', $product->product_name . ' | Products & Catalog')
@section('page_heading', $product->product_name)
@section('page_subheading', 'Detailed listing overview')

@section('content')
    <section class="card" style="display:flex;justify-content:space-between;align-items:center;">
        <div>
            <p style="margin:0;color:#475467;">{{ $product->school?->name }} • {{ $product->grade?->name ?? 'All grades' }}</p>
            <p style="margin:4px 0 0;color:#98a2b3;">Tag: {{ $product->tag_name ?? '—' }} • Availability: {{ $product->availability_label ?? '—' }}</p>
        </div>
        <a href="{{ route('master.admin.catalog.index') }}" style="color:#490d59;font-weight:600;">← Back to catalog</a>
    </section>

    <section class="card" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:18px;">
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Basic info</h4>
            <p style="margin:0;color:#475467;">
                School: {{ $product->school?->name }}<br>
                Grade: {{ $product->grade?->name ?? 'All' }}<br>
                Category: {{ $product->category ?? '—' }}<br>
                Type: {{ $product->product_type ?? '—' }}<br>
                Gender: {{ ucfirst($product->gender) }}<br>
                Stock status: {{ $product->stock_status === 'in_stock' ? 'In stock' : 'Out of stock' }}
            </p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Pricing</h4>
            <p style="margin:0;color:#475467;">
                Regular: ₹{{ number_format($product->price_regular, 2) }}<br>
                Sale: {{ $product->price_sale ? '₹' . number_format($product->price_sale, 2) : '—' }}<br>
                Tax: {{ $product->price_tax ?? 0 }}% ({{ $product->tax_profile ?? 'profile N/A' }})<br>
                Weight: {{ $product->product_weight ?? '—' }} kg
            </p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Inventory</h4>
            <p style="margin:0;color:#475467;">
                In stock: {{ $product->inventory_stock }}<br>
                Low stock alert: {{ $product->low_stock_threshold }}<br>
                Status: {{ ucfirst($product->status) }}
            </p>
        </div>
    </section>

    <section class="card" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:18px;">
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Description</h4>
            <p style="margin:0;color:#475467;">{!! nl2br(e($product->description)) !!}</p>
        </div>
        <div>
            <h4 style="margin:0 0 8px;color:#111827;">Size guidance</h4>
            <p style="margin:0;color:#475467;">{!! nl2br(e($product->size_guidance)) !!}</p>
        </div>
    </section>

    <section class="card">
        <h4 style="margin:0 0 12px;color:#111827;">Media</h4>
        <div style="display:flex;gap:16px;flex-wrap:wrap;">
            @if($product->featured_image)
                <img src="{{ $product->featured_image }}" alt="{{ $product->product_name }}" style="width:160px;height:160px;object-fit:cover;border-radius:16px;">
            @endif
            @foreach($product->media_images ?? [] as $image)
                <img src="{{ $image }}" alt="" style="width:120px;height:120px;object-fit:cover;border-radius:12px;">
            @endforeach
        </div>
        <div style="margin-top:16px;">
            <p style="margin:0;color:#475467;">Gallery: {{ implode(', ', $product->media_gallery ?? []) ?: '—' }}</p>
            <p style="margin:0;color:#475467;">Size chart: {{ $product->media_size_chart ?? '—' }}</p>
            <p style="margin:0;color:#475467;">Size measurement image: {{ $product->size_measurement_image ?? '—' }}</p>
            <p style="margin:0;color:#475467;">Measurement video: {{ $product->media_measurement_video ?? '—' }}</p>
        </div>
    </section>

    <section class="card" style="display:flex;gap:12px;flex-wrap:wrap;">
        <a href="{{ route('master.admin.catalog.edit', $product) }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#490d59;font-weight:600;">Edit product</a>
        <a href="{{ route('master.admin.catalog.index', array_merge(request()->query(), ['status' => 'live'])) }}" style="padding:10px 16px;border-radius:12px;border:1px solid #d0d5dd;color:#475467;font-weight:600;">Back to list</a>
    </section>
@endsection

