@extends('admin.layouts.base')

@section('title', 'Product Mapping | ' . $school->name)
@section('page_heading', 'Product Mapping • ' . $school->name)
@section('page_subheading', 'Assign compliant catalog entries per grade and gender')

@push('styles')
    <style>
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 14px; border-bottom: 1px solid #e5e7eb; text-align:left; }
        th { font-size: 14px; color:#111827; text-transform:uppercase; letter-spacing:0.05em; }
        td { font-size: 14px; color:#475467; }
        .inline-form { display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:12px; }
    </style>
@endpush

@section('content')
    <div class="card" style="margin-bottom:24px;">
        <form method="POST" action="{{ route('master.admin.schools.product-mapping.store', $school) }}" class="inline-form">
            @csrf
            <label>
                <span>Grade</span>
                <select name="grade_id">
                    <option value="">All grades</option>
                    @foreach($grades as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Product name *</span>
                <input type="text" name="product_name" required>
            </label>
            <label>
                <span>Category</span>
                <input type="text" name="category">
            </label>
            <label>
                <span>Product type</span>
                <input type="text" name="product_type">
            </label>
            <label>
                <span>Gender *</span>
                <select name="gender">
                    @foreach(['boys','girls','unisex'] as $gender)
                        <option value="{{ $gender }}">{{ ucfirst($gender) }}</option>
                    @endforeach
                </select>
            </label>
            <label>
                <span>Regular price</span>
                <input type="number" step="0.01" name="price_regular">
            </label>
            <label>
                <span>Sale price</span>
                <input type="number" step="0.01" name="price_sale">
            </label>
            <label>
                <span>Inventory</span>
                <input type="number" name="inventory_stock" min="0" value="0">
            </label>
            <label>
                <span>Low stock alert</span>
                <input type="number" name="low_stock_threshold" min="0" value="0">
            </label>
            <label>
                <span>Status</span>
                <select name="status">
                    @foreach(['live','draft','archived'] as $status)
                        <option value="{{ $status }}">{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </label>
            <button type="submit" style="border:none;background:#490d59;color:#fff;border-radius:12px;font-weight:600;">Add mapping</button>
        </form>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Grade</th>
                    <th>Gender</th>
                    <th>Pricing</th>
                    <th>Inventory</th>
                    <th>Status</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($mappings as $mapping)
                    <tr>
                        <td>
                            <strong style="color:#111827;">{{ $mapping->product_name }}</strong>
                            <div style="font-size:12px;color:#98a2b3;">
                                {{ $mapping->category ?? '—' }} • {{ $mapping->product_type ?? '—' }}
                            </div>
                        </td>
                        <td>{{ $mapping->grade?->name ?? 'All grades' }}</td>
                        <td>{{ ucfirst($mapping->gender) }}</td>
                        <td>
                            ₹{{ number_format($mapping->price_regular ?? 0, 2) }}
                            @if($mapping->price_sale)
                                <span style="color:#b42318;">(Sale: ₹{{ number_format($mapping->price_sale, 2) }})</span>
                            @endif
                        </td>
                        <td>{{ $mapping->inventory_stock }} <small>Alert @ {{ $mapping->low_stock_threshold }}</small></td>
                        <td style="text-transform:capitalize;">{{ $mapping->status }}</td>
                        <td style="display:flex;gap:8px;">
                            <form method="POST" action="{{ route('master.admin.schools.product-mapping.destroy', [$school, $mapping]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="border:none;background:#fee4e2;color:#b42318;border-radius:8px;padding:6px 12px;">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7">No mappings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

