@extends('admin.layouts.merchandise')

@section('title', 'Inventory | Merchandise Admin')
@section('page_heading', 'Inventory Management')
@section('page_subheading', 'Manage stock levels for your merchandise.')

@section('content')
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Merchandise Stock</h6>
        <form class="form-inline" method="GET">
            <input type="text" name="q" class="form-control form-control-sm mr-2" placeholder="Search products..." value="{{ request('q') }}">
            <button type="submit" class="btn btn-sm btn-primary">Search</button>
        </form>
    </div>
    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Category</th>
                        <th>School</th>
                        <th>Current Stock</th>
                        <th>Update Stock</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>
                            {{ $product->product_name }}
                            @if($product->inventory_stock <= 5)
                                <span class="badge badge-danger ml-2">Low Stock</span>
                            @endif
                        </td>
                        <td>{{ $product->category }}</td>
                        <td>{{ optional($product->school)->name ?? 'N/A' }}</td>
                        <td>
                            <strong class="{{ $product->inventory_stock <= 0 ? 'text-danger' : 'text-success' }}">
                                {{ $product->inventory_stock }}
                            </strong>
                        </td>
                        <td style="width: 200px;">
                            <form action="{{ route('admin.merchandise.inventory.update', $product) }}" method="POST" class="d-flex">
                                @csrf
                                @method('PUT')
                                <input type="number" name="inventory_stock" class="form-control form-control-sm mr-2" value="{{ $product->inventory_stock }}" min="0">
                                <button type="submit" class="btn btn-sm btn-outline-primary">Save</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">No merchandise products found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
