@extends('frontend.layouts.school')

@section('content')

<section class="space-top space-extra-bottom" style="background-color: #f3f4f6;">
    <div class="container-fluid px-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h3 mb-1" style="color: #111827; font-weight: 700;">Product Catalogue</h2>
                <p class="text-muted mb-0">Browse and monitor products assigned to your school</p>
            </div>
            <a href="{{ route('frontend.school.dashboard') }}" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
            </a>
        </div>

        <!-- Filters Section -->
        <div class="card shadow-sm rounded-4 border-0 mb-4">
            <div class="card-body p-4">
                <form method="GET" action="{{ route('frontend.school.products') }}" class="row g-3 align-items-end">
                    <!-- Category Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Category</label>
                        <select name="category" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Categories</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Product Type Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Product Type</label>
                        <select name="product_type" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Types</option>
                            @foreach($productTypes as $type)
                                <option value="{{ $type }}" {{ request('product_type') == $type ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $type)) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Gender Filter -->
                    <div class="col-md-2">
                        <label class="form-label small fw-bold text-muted">Gender</label>
                        <select name="gender" class="form-select rounded-pill bg-light border-0 px-3">
                            <option value="">All Genders</option>
                            @foreach($genders as $gender)
                                <option value="{{ $gender }}" {{ request('gender') == $gender ? 'selected' : '' }}>{{ $gender }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Search Input -->
                    <div class="col-md-3">
                        <label class="form-label small fw-bold text-muted">Search Product</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-pill bg-light border-0 ps-3"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control rounded-end-pill bg-light border-0" placeholder="Product name..." value="{{ request('search') }}">
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-3 text-end">
                        <button type="submit" class="btn rounded-pill px-4 text-white me-2" style="background-color: #490D59;">
                            Apply Filters
                        </button>
                        <a href="{{ route('frontend.school.products') }}" class="btn btn-outline-secondary rounded-pill px-4">
                            Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Table Section -->
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Image</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Product Name</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Category / Type</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Gender</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Grade</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Price</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Status</th>
                                <th class="py-3 text-uppercase text-xs font-weight-bolder opacity-7" style="font-size: 0.75rem; color: #6b7280; letter-spacing: 0.05em;">Stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($products as $product)
                                <tr>
                                    <td class="ps-4 py-3">
                                        <div style="width: 50px; height: 50px; border-radius: 8px; overflow: hidden; background-color: #f9f9f9; border: 1px solid #eee;">
                                            @if($product->featured_image)
                                                <img src="{{ asset('storage/' . $product->featured_image) }}" alt="{{ $product->product_name }}" style="width: 100%; height: 100%; object-fit: cover;">
                                            @else
                                                <div class="d-flex align-items-center justify-content-center h-100 text-muted">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <h6 class="mb-0 text-dark fw-bold" style="font-size: 0.9rem;">{{ $product->product_name }}</h6>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex flex-column">
                                            <span class="text-dark fw-medium" style="font-size: 0.9rem;">{{ $product->category }}</span>
                                            <span class="text-muted small" style="font-size: 0.8rem;">{{ ucfirst(str_replace('_', ' ', $product->product_type)) }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <span class="text-dark" style="font-size: 0.9rem;">{{ $product->gender }}</span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge rounded-pill bg-light text-dark border fw-normal" style="font-size: 0.8rem;">
                                            {{ $product->grade ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <div class="d-flex flex-column">
                                            @if($product->price_sale < $product->price_regular)
                                                <span class="text-danger fw-bold" style="font-size: 0.9rem;">₹{{ number_format($product->price_sale, 2) }}</span>
                                                <span class="text-muted text-decoration-line-through small" style="font-size: 0.8rem;">₹{{ number_format($product->price_regular, 2) }}</span>
                                            @else
                                                <span class="text-dark fw-bold" style="font-size: 0.9rem;">₹{{ number_format($product->price_regular, 2) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        @if($product->inventory_stock > 0)
                                            <span class="badge bg-soft-success text-success" style="background-color: #ecfdf5; color: #10b981;">Live</span>
                                            <div class="text-success small" style="font-size: 0.75rem;">In Stock</div>
                                        @else
                                            <span class="badge bg-soft-danger text-danger" style="background-color: #fef2f2; color: #ef4444;">Out of Stock</span>
                                        @endif
                                    </td>
                                    <td class="py-3">
                                        <span class="fw-bold text-dark">{{ $product->inventory_stock }}</span>
                                        @if($product->low_stock_threshold && $product->inventory_stock <= $product->low_stock_threshold)
                                            <div class="text-warning small"><i class="fas fa-exclamation-triangle me-1"></i>Low Stock</div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-5">
                                        <div class="text-muted">
                                            <i class="fas fa-box-open fa-3x mb-3 opacity-50"></i>
                                            <p class="mb-0">No products found matching your criteria.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="card-footer bg-white border-0 py-3">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<style>
    .form-control:focus, .form-select:focus {
        border-color: #490D59 !important;
        box-shadow: 0 0 0 0.25rem rgba(73, 13, 89, 0.1) !important;
    }
    .text-xs {
        font-size: 0.75rem !important;
    }
    .table > :not(caption) > * > * {
        padding: 1rem 1rem;
        background-color: var(--bs-table-bg);
        border-bottom-width: 1px;
        box-shadow: inset 0 0 0 9999px var(--bs-table-accent-bg);
    }
    .badge.bg-soft-success {
        background-color: #ecfdf5 !important;
        color: #059669 !important;
    }
    .badge.bg-soft-danger {
        background-color: #fef2f2 !important;
        color: #dc2626 !important;
    }
</style>
@endsection
